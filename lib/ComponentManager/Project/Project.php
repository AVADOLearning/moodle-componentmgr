<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Project;

use ComponentManager\Exception\InvalidProjectException;
use ComponentManager\PackageFormat\PackageFormat;
use ComponentManager\PackageFormat\PackageFormatFactory;
use ComponentManager\PackageRepository\PackageRepository;
use ComponentManager\PackageRepository\PackageRepositoryFactory;
use ComponentManager\PackageSource\PackageSource;
use ComponentManager\PackageSource\PackageSourceFactory;
use ComponentManager\Project\ProjectFile;
use ComponentManager\Project\ProjectLockFile;

/**
 * Project class.
 *
 * Component Manager projects are collections of versioned components and the
 * sources necessary to locate and install them. They're defined in JSON files.
 */
class Project {
    /**
     * Package format factory.
     *
     * @var PackageFormatFactory
     */
    protected $packageFormatFactory;

    /**
     * Package repositories.
     *
     * @var PackageRepository[]
     */
    protected $packageRepositories;

    /**
     * Package repository factory.
     *
     * @var PackageRepositoryFactory
     */
    protected $packageRepositoryFactory;

    /**
     * Package source factory.
     *
     * @var PackageSourceFactory
     */
    protected $packageSourceFactory;

    /**
     * Project file.
     *
     * @var ProjectFile
     */
    protected $projectFile;

    /**
     * Project lock file.
     *
     * @var ProjectLockFile
     */
    protected $projectLockFile;

    /**
     * Initialiser.
     *
     * @param ProjectFile              $projectFile
     * @param ProjectLockFile          $projectLockFile
     * @param PackageRepositoryFactory $packageRepositoryFactory
     * @param PackageSourceFactory     $packageSourceFactory
     * @param PackageFormatFactory     $packageFormatFactory
     */
    public function __construct(ProjectFile $projectFile,
                                ProjectLockFile $projectLockFile,
                                PackageRepositoryFactory $packageRepositoryFactory,
                                PackageSourceFactory $packageSourceFactory,
                                PackageFormatFactory $packageFormatFactory) {
        $this->projectFile     = $projectFile;
        $this->projectLockFile = $projectLockFile;

        $this->packageRepositoryFactory = $packageRepositoryFactory;
        $this->packageSourceFactory     = $packageSourceFactory;
        $this->packageFormatFactory     = $packageFormatFactory;
    }

    /**
     * Get package repositories.
     *
     * @return PackageRepository[]
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
     * @return PackageRepository
     *
     * @throws InvalidProjectException
     */
    public function getPackageRepository($packageRepository) {
        $packageRepositories = $this->getPackageRepositories();

        return $packageRepositories[$packageRepository];
    }

    /**
     * Get package source.
     *
     * @param string $packageSource
     *
     * @return PackageSource
     */
    public function getPackageSource($packageSource) {
        return $this->packageSourceFactory->getPackageSource(
                $packageSource);
    }

    /**
     * Get the project file.
     *
     * @return ProjectFile
     */
    public function getProjectFile() {
        return $this->projectFile;
    }

    /**
     * Get the project lock file.
     *
     * @return ProjectLockFile
     */
    public function getProjectLockFile() {
        return $this->projectLockFile;
    }

    /**
     * Get the named package format.
     *
     * @param string $packageFormat
     *
     * @return PackageFormat
     */
    public function getPackageFormat($packageFormat) {
        return $this->packageFormatFactory->getPackageFormat($packageFormat);
    }
}
