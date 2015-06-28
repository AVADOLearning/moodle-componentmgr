<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager;

/**
 * Component.
 *
 * Component objects represent metadata about Moodle components sourced from
 * package repositories.
 */
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
    public function __construct($name, $versions, $packageRepository=null) {
        $this->name     = $name;
        $this->versions = $versions;

        $this->packageRepository = $packageRepository;
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
}
