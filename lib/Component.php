<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager;

class Component {
    /**
     * Dependencies.
     *
     * @var \ComponentManager\Component[]
     */
    protected $dependencies;

    /**
     * Component name.
     *
     * @var string
     */
    protected $name;

    /**
     * Package repository.
     *
     * @var string
     */
    protected $packageRepository;

    /**
     * Package source.
     *
     * @var string
     */
    protected $packageSource;

    /**
     * Component versions.
     *
     * @var \ComponentManager\ComponentVersion[]
     */
    protected $versions;

    /**
     * Initialiser.
     *
     * @param string $name
     * @param string $version
     * @param string $packageRepository
     * @param string $packageSource
     */
    public function __construct($name, $versions, $packageRepository=null,
                                $packageSource=null) {
        $this->name     = $name;
        $this->versions = $versions;

        $this->packageRepository = $packageRepository;
        $this->packageSource     = $packageSource;
    }

    /**
     * Get the component's name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get the ID of the component's package repository.
     *
     * @return string
     */
    public function getPackageRepository() {
        return $this->packageRepository;
    }

    /**
     * Get the ID of the component's pacakge source.
     *
     * @return string
     */
    public function getPackageSource() {
        return $this->packageSource;
    }

    /**
     * Get the component's version.
     *
     * @return string
     */
    public function getVersion() {
        return $this->version;
    }
}
