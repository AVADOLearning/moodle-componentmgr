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
 * Component specification.
 *
 * A component specification is a structure used to hold all information about a
 * specific component before we're able to look up the component in the
 * associated package source. We should resolve this to obtain a Component
 * object as soon as we can during installation.
 */
class ComponentSpecification {
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
     * Component version.
     *
     * @var string
     */
    protected $version;

    /**
     * Initialiser.
     *
     * @param string $name
     * @param string $version
     * @param string $packageRepository
     * @param string $packageSource
     */
    public function __construct($name, $version, $packageRepository=null,
                                $packageSource=null) {
        $this->name    = $name;
        $this->version = $version;

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
     * Get the ID of the component's package source.
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
