<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Helper;

use ComponentManager\ComponentSpecification;
use ComponentManager\Exception\InvalidProjectException;
use ComponentManager\Moodle;
use ComponentManager\PackageRepository\CachingPackageRepository;
use ComponentManager\PlatformUtil;
use ComponentManager\Project\Project;
use ComponentManager\ResolvedComponentVersion;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Install helper.
 *
 * Provides utility methods for installation of Moodle and related components.
 */
class InstallHelper {
    /**
     * Component manager project.
     *
     * @var \ComponentManager\Project\Project
     */
    protected $project;

    /**
     * Moodle installation object.
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
     * Logger.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Initialiser.
     *
     * @param \ComponentManager\Project\Project        $project
     * @param \ComponentManager\Moodle                 $moodle
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     * @param \Psr\Log\LoggerInterface                 $logger
     */
    public function __construct(Project $project, Moodle $moodle,
                                Filesystem $filesystem,
                                LoggerInterface $logger) {
        $this->project    = $project;
        $this->moodle     = $moodle;
        $this->filesystem = $filesystem;
        $this->logger     = $logger;
    }

    /**
     * Ensure that the project's package repositories have cached metadata.
     *
     * @return void
     *
     * @throws \ComponentManager\Exception\InvalidProjectException
     */
    public function ensurePackageRepositoryMetadataCached() {
        $packageRepositories = $this->project->getPackageRepositories();
        $haveCaches = true;

        foreach ($packageRepositories as $name => $packageRepository) {
            if ($packageRepository instanceof CachingPackageRepository) {
                $lastRefreshed = $packageRepository->metadataCacheLastRefreshed();

                if ($lastRefreshed === null) {
                    $this->logger->error('Package repository missing cache; requires refresh', [
                        'packageRepository' => $name,
                    ]);

                    $haveCaches = false;
                }
            }
        }

        if (!$haveCaches) {
            throw new InvalidProjectException(
                    'One or more caching package repositories was missing its metadata cache',
                    InvalidProjectException::CODE_MISSING_PACKAGE_REPOSITORY_CACHE);
        }
    }

    /**
     * Install a component.
     *
     * @param \ComponentManager\ResolvedComponentVersion $resolvedComponentVersion
     *
     * @return void
     */
    public function installComponent(ResolvedComponentVersion $resolvedComponentVersion) {
        $this->logger->info('Installing component', [
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

        $targetDirectory = $typeDirectory . PlatformUtil::directorySeparator()
                         . $component->getPluginName();

        $tempDirectory = PlatformUtil::createTempDirectory();

        $sourceDirectory = $packageSource->obtainPackage(
                $tempDirectory, $resolvedComponentVersion, $this->filesystem, $this->logger);

        if ($resolvedComponentVersion->getFinalVersion() === null) {
            $this->logger->warning('Package source did not indicate final version; defaulting to desired version', [
                'version' => $resolvedComponentVersion->getVersion()->getVersion(),
            ]);

            $resolvedComponentVersion->setFinalVersion(
                    $resolvedComponentVersion->getVersion()->getVersion());
        }

        $this->logger->debug('Downloaded component source', [
            'packageSource'   => $packageSource->getName(),
            'sourceDirectory' => $sourceDirectory,
        ]);

        if ($this->filesystem->exists($targetDirectory)) {
            $this->logger->info('Component directory already exists; removing', [
                'targetDirectory' => $targetDirectory,
            ]);

            $this->filesystem->remove($targetDirectory);
        }

        $this->logger->info('Copying component source to Moodle directory', [
            'sourceDirectory' => $sourceDirectory,
            'targetDirectory' => $targetDirectory,
        ]);
        $this->filesystem->mirror($sourceDirectory, $targetDirectory);

        $this->logger->info('Pinning component at installed final version', [
            'finalVersion' => $resolvedComponentVersion->getFinalVersion(),
        ]);
        $projectLockFile->addResolvedComponentVersion($resolvedComponentVersion);

        $this->logger->info('Cleaning up after component installation', [
            'tempDirectory' => $tempDirectory,
        ]);
        try {
            $this->filesystem->chmod([$tempDirectory], 0750, 0000, true);
            $this->filesystem->remove([$tempDirectory]);
        } catch (IOException $e) {
            $this->logger->warning('Unable to clean up temporary directory', [
                'code'          => $e->getCode(),
                'message'       => $e->getMessage(),
                'tempDirectory' => $tempDirectory,
            ]);
        }
    }

    /**
     * Install components from component specifications in project file.
     *
     * @return void
     *
     * @throws \ComponentManager\Exception\InvalidProjectException
     */
    public function installProjectComponents() {
        $this->ensurePackageRepositoryMetadataCached();

        $componentSpecifications = $this->project->getProjectFile()->getComponentSpecifications();

        /* TODO: resolve dependencies before attempting installation. For the
         *       time being, we'll just assume that the developer has specified
         *       all necessary components in the project file. */
        $resolvedComponents = [];
        foreach ($componentSpecifications as $componentSpecification) {
            $resolvedComponents[] = $this->resolveComponentVersion(
                    $componentSpecification);
        }

        foreach ($resolvedComponents as $resolvedComponent) {
            $this->installComponent($resolvedComponent);
        }

        $this->logger->info('Writing project lock file');
        $this->project->getProjectLockFile()->commit();
    }

    /**
     * Resolve a component's version.
     *
     * @param \ComponentManager\ComponentSpecification $specification
     *
     * @return \ComponentManager\ResolvedComponentVersion
     */
    public function resolveComponentVersion(ComponentSpecification $componentSpecification) {
        $packageRepository = $this->project->getPackageRepository(
                $componentSpecification->getPackageRepository());

        $this->logger->info('Resolving component version', [
            'component'         => $componentSpecification->getName(),
            'packageRepository' => $componentSpecification->getPackageRepository(),
            'version'           => $componentSpecification->getVersion(),
        ]);

        $componentName         = $componentSpecification->getName();
        $componentVersion      = $componentSpecification->getVersion();
        $packageRepositoryName = $componentSpecification->getPackageRepository();

        if (!$component = $packageRepository->getComponent($componentSpecification)) {
            throw new InvalidProjectException(
                    "The component \"{$componentName}\" could not be found within repository \"{$packageRepositoryName}\"",
                    InvalidProjectException::CODE_MISSING_COMPONENT);
        }

        /* Note that even at this late stage, we still might not have a final
         * version for the component:
         * -> If the package repository provides us with the Moodle
         *    $plugin->version value, we'll be using it here.
         * -> If the package repository is a version control system, the version
         *    will contain the name of a branch or tag and will need to be
         *    resolved to an individual commit. */
        $version = $component->getVersion($componentVersion);

        return new ResolvedComponentVersion(
                $componentSpecification, $packageRepository, $component, $version);
    }
}
