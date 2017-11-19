<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Step;

use ComponentManager\Exception\InstallationFailureException;
use ComponentManager\Exception\RetryablePackageFailureException;
use ComponentManager\Exception\UnsatisfiedVersionException;
use ComponentManager\Moodle;
use ComponentManager\Platform\Platform;
use ComponentManager\Project\Project;
use ComponentManager\Task\InstallTask;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class InstallComponentsStep implements Step {
    /**
     * Project.
     *
     * @var Project
     */
    protected $project;

    /**
     * Moodle.
     *
     * @var Moodle
     */
    protected $moodle;

    /**
     * Filesystem.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Platform support library.
     *
     * @var Platform
     */
    protected $platform;

    /**
     * Number of retries.
     *
     * @var integer
     */
    protected $attempts;

    /**
     * Initialiser.
     *
     * @param Project    $project
     * @param Moodle     $moodle
     * @param Platform   $platform
     * @param Filesystem $filesystem
     * @param integer    $attempts
     */
    public function __construct(Project $project, Moodle $moodle,
                                Platform $platform, Filesystem $filesystem,
                                $attempts) {
        $this->project = $project;
        $this->moodle  = $moodle;

        $this->filesystem = $filesystem;
        $this->platform   = $platform;

        $this->attempts = $attempts;
    }

    /**
     * @override Step
     *
     * @param InstallTask $task
     */
    public function execute($task, LoggerInterface $logger) {
        $resolvedComponentVersions = $task->getResolvedComponentVersions();

        foreach ($resolvedComponentVersions as $resolvedComponentVersion) {
            $obtained = false;
            $remainingAttempts = $this->attempts;

            $logger->info('Installing component', [
                'component'         => $resolvedComponentVersion->getComponent()->getName(),
                'packageRepository' => $resolvedComponentVersion->getPackageRepository()->getName(),
                'version'           => $resolvedComponentVersion->getVersion()->getVersion(),
                'release'           => $resolvedComponentVersion->getVersion()->getRelease(),
            ]);

            $projectLockFile = $this->project->getProjectLockFile();
            $component = $resolvedComponentVersion->getComponent();
            $packageSource = $this->project->getPackageSource(
                    $resolvedComponentVersion->getSpecification()->getPackageSource());

            $typeDirectory = $this->moodle->getPluginTypeDirectory(
                    $component->getPluginType());
            if (!$typeDirectory) {
                throw new InstallationFailureException(
                        sprintf(
                            'Target directory for component "%s" unknown; is its parent installed?',
                            $component->getName()),
                        InstallationFailureException::CODE_UNKNOWN_TARGET_DIRECTORY);
            }

            $targetDirectory = $this->platform->joinPaths([
                $typeDirectory,
                $component->getPluginName(),
            ]);
            $tempDirectory = $this->platform->createTempDirectory();

            do {
                try {
                    $sourceDirectory = $packageSource->obtainPackage(
                            $tempDirectory, $resolvedComponentVersion,
                            $this->filesystem, $logger);
                    $obtained = true;
                } catch (RetryablePackageFailureException $e) {
                    $remainingAttempts--;
                    $logger->warning('Failed to obtain package', [
                        'attempt'   => $this->attempts - $remainingAttempts,
                        'attempts'  => $this->attempts,
                        'exception' => $e->getPrevious(),
                    ]);
                }
            } while ($remainingAttempts > 0 && !$obtained);

            if (!$obtained) {
                throw new UnsatisfiedVersionException(
                        sprintf(
                                'Package source "%s" unable to obtain component "%s"',
                                $packageSource->getId(),
                                $resolvedComponentVersion->getComponent()->getName()),
                        UnsatisfiedVersionException::CODE_PACKAGE_SOURCE_FAILED);
            }

            if ($resolvedComponentVersion->getFinalVersion() === null) {
                $logger->warning('Package source did not indicate final version; defaulting to desired version', [
                    'version' => $resolvedComponentVersion->getVersion()->getVersion(),
                ]);

                $resolvedComponentVersion->setFinalVersion(
                        $resolvedComponentVersion->getVersion()->getVersion());
            }

            $logger->debug('Downloaded component source', [
                'packageSource'   => $packageSource->getName(),
                'sourceDirectory' => $sourceDirectory,
            ]);

            if ($this->filesystem->exists($targetDirectory)) {
                $logger->warning('Component directory already exists; removing', [
                    'targetDirectory' => $targetDirectory,
                ]);

                $this->filesystem->remove($targetDirectory);
            }

            $logger->info('Copying component source to Moodle directory', [
                'sourceDirectory' => $sourceDirectory,
                'targetDirectory' => $targetDirectory,
            ]);
            $this->filesystem->mirror($sourceDirectory, $targetDirectory);

            $logger->info('Pinning component at installed final version', [
                'finalVersion' => $resolvedComponentVersion->getFinalVersion(),
            ]);
            $projectLockFile->addResolvedComponentVersion($resolvedComponentVersion);

            $logger->info('Cleaning up after component installation', [
                'tempDirectory' => $tempDirectory,
            ]);
            try {
                $this->filesystem->chmod([$tempDirectory], 0750, 0000, true);
                $this->filesystem->remove([$tempDirectory]);
            } catch (IOException $e) {
                $logger->warning('Unable to clean up temporary directory', [
                    'code'          => $e->getCode(),
                    'message'       => $e->getMessage(),
                    'tempDirectory' => $tempDirectory,
                ]);
            }
        }
    }
}
