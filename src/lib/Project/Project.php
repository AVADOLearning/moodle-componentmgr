<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\Project;

use ComponentManager\Exception\InvalidProjectException;
use ComponentManager\PackageRepository\PackageRepositoryFactory;
use ComponentManager\PackageSource\PackageSourceFactory;

/**
 * Project class.
 *
 * Component Manager projects are collections of versioned components and the
 * sources necessary to locate and install them. They're defined in JSON files.
 */
class Project {
    /**
     * Package repositories.
     *
     * @var \ComponentManager\PackageRepository\PackageRepository[]
     */
    protected $packageRepositories;

    /**
     * Package repository factory.
     *
     * @var \ComponentManager\PackageRepository\PackageRepositoryFactory
     */
    protected $packageRepositoryFactory;

    /**
     * Package source factory.
     *
     * @var \ComponentManager\PackageSource\PackageSourceFactory
     */
    protected $packageSourceFactory;

    /**
     * Project file.
     *
     * @var \ComponentManager\Project\ProjectFile
     */
    protected $projectFile;

    /**
     * Project lock file.
     *
     * @var \ComponentManager\Project\ProjectLockFile
     */
    protected $projectLockFile;

    /**
     * Initialiser.
     *
     * @param string                                                       $filename
     * @param \ComponentManager\PackageRepository\PackageRepositoryFactory $packageRepositoryFactory
     */
    public function __construct(ProjectFile $projectFile,
                                ProjectLockFile $projectLockFile,
                                PackageRepositoryFactory $packageRepositoryFactory,
                                PackageSourceFactory $packageSourceFactory) {
        $this->projectFile     = $projectFile;
        $this->projectLockFile = $projectLockFile;

        $this->packageRepositoryFactory = $packageRepositoryFactory;
        $this->packageSourceFactory     = $packageSourceFactory;
    }

    /**
     * Get package repositories.
     *
     * @return \ComponentManager\PackageRepository\PackageRepository[]
     */
    public function getPackageRepositories() {
        if ($this->packageRepositories === null) {
            $this->packageRepositories = [];

            foreach ($this->projectFile->getPackageRepositories()
                    as $name => $packageRepository) {
                $this->packageRepositories[$name] = $this->packageRepositoryFactory->getPackageRepository(
                        $packageRepository->type, $packageRepository);
            }
        }

        return $this->packageRepositories;
    }

    /**
     * Get the package repository.
     *
     * @param string $packageRepository
     *
     * @return \ComponentManager\PackageRepository\PackageRepository
     *
     * @throws \ComponentManager\Exception\InvalidProjectException
     */
    public function getPackageRepository($packageRepository) {
        $packageRepositories = $this->getPackageRepositories();

        if (array_key_exists($packageRepository, $packageRepositories)) {
            return $packageRepositories[$packageRepository];
        } else {
            throw new InvalidProjectException(
                    "The package repository named '{$packageRepository}' was not listed in your project file",
                    InvalidProjectException::CODE_MISSING_PACKAGE_REPOSITORY);
        }
    }

    /**
     * Get package source.
     *
     * @param string $packageSource
     *
     * @return \ComponentManager\PackageSource\PackageSource
     */
    public function getPackageSource($packageSource) {
        return $this->packageSourceFactory->getPackageSource(
                $packageSource);
    }

    /**
     * Get the project file.
     *
     * @return \ComponentManager\Project\ProjectFile
     */
    public function getProjectFile() {
        return $this->projectFile;
    }

    /**
     * Get the project lock file.
     *
     * @return \ComponentManager\Project\ProjectLockFile
     */
    public function getProjectLockFile() {
        return $this->projectLockFile;
    }
}
