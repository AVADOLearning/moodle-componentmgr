<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager;

use ComponentManager\PackageRepository\PackageRepositoryFactory;

/**
 * Project class.
 *
 * Component Manager projects are collections of versioned components and the
 * sources necessary to locate and install them. They're defined in JSON files.
 */
class Project {
    /**
     * Components.
     *
     * Lazily loaded -- be sure to call getComponents() in order to ensure the
     * value is defined.
     *
     * @var \ComponentManager\Component[]
     */
    protected $components;

    /**
     * Decoded components file contents.
     *
     * @var \stdClass
     */
    protected $contents;

    /**
     * File name.
     *
     * @var string
     */
    protected $fileName;

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
     * Initialiser.
     *
     * @param string                                                       $fileName
     * @param \ComponentManager\PackageRepository\PackageRepositoryFactory $packageRepositoryFactory
     */
    public function __construct($fileName, PackageRepositoryFactory $packageRepositoryFactory) {
        $projectFileContents = file_get_contents($fileName);
        $projectFileObject   = json_decode($projectFileContents);

        $this->fileName                 = $fileName;
        $this->contents                 = $projectFileObject;
        $this->packageRepositoryFactory = $packageRepositoryFactory;
    }

    /**
     * Get components.
     *
     * @return \ComponentManager\Component[]
     */
    public function getComponents() {
        if ($this->components === null) {
            $this->components = [];

            foreach ($this->contents->components as $name => $component) {
                $version           = property_exists($component, 'version')
                        ? $component->version           : null;
                $packageRepository = property_exists($component, 'packageRepository')
                        ? $component->packageRepository : null;
                $packageSource     = property_exists($component, 'packageSource')
                        ? $component->packageSource     : null;

                $this->components[$name] = new Component($name, $version,
                        $packageRepository, $packageSource);
            }
        }

        return $this->components;
    }

    /**
     * Get package repositories.
     *
     * @return \ComponentManager\PackageRepository\PackageRepository
     */
    public function getPackageRepositories() {
        if ($this->packageRepositories === null) {
            $this->packageRepositories = [];

            foreach ($this->contents->packageRepositories
                    as $name => $packageRepository) {
                $this->packageRepositories[$name] = $this->packageRepositoryFactory->getPackageRepository(
                        $packageRepository->type, $packageRepository);
            }
        }

        return $this->packageRepositories;
    }
}
