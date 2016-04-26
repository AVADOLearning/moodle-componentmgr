<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Step;

use ComponentManager\Moodle;
use ComponentManager\PlatformUtil;
use ComponentManager\Project\Project;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class InstallComponentsStep implements Step {
    /**
     * Project.
     *
     * @var \ComponentManager\Project\Project
     */
    protected $project;

    /**
     * Moodle.
     *
     * @var \ComponentManager\Moodle
     */
    protected $moodle;

    /**
     * Filesystem.
     *
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Initialiser.
     *
     * @param \ComponentManager\Project\Project        $project
     * @param \ComponentManager\Moodle                 $moodle
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(Project $project, Moodle $moodle,
                                Filesystem $filesystem) {
        $this->project    = $project;
        $this->moodle     = $moodle;
        $this->filesystem = $filesystem;
    }

    /**
     * @override \ComponentManager\Task\Step
     *
     * @param \ComponentManager\Task\InstallTask $task
     */
    public function execute($task, LoggerInterface $logger) {
        $resolvedComponentVersions = $task->getResolvedComponentVersions();

        foreach ($resolvedComponentVersions as $resolvedComponentVersion) {
            $logger->info('Installing component', [
                'component'         => $resolvedComponentVersion->getComponent()->getName(),
                'packageRepository' => $resolvedComponentVersion->getPackageRepository()->getName(),
                'version'           => $resolvedComponentVersion->getVersion()->getVersion(),
                'release'           => $resolvedComponentVersion->getVersion()->getRelease(),
            ]);

            $projectLockFile = $this->project->getProjectLockFile();
            $component       = $resolvedComponentVersion->getComponent();
            $packageSource   = $this->project->getPackageSource(
                    $resolvedComponentVersion->getSpecification()->getPackageSource());
            $typeDirectory = $this->moodle->getPluginTypeDirectory(
                    $component->getPluginType());

            $targetDirectory = $typeDirectory
                    . PlatformUtil::directorySeparator()
                    . $component->getPluginName();

            $tempDirectory = PlatformUtil::createTempDirectory();

            $sourceDirectory = $packageSource->obtainPackage(
                    $tempDirectory, $resolvedComponentVersion,
                    $this->filesystem, $logger);

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
                $logger->info('Component directory already exists; removing', [
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
