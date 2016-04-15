<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager;

use ComponentManager\Exception\UnsatisfiedVersionException;

/**
 * Component.
 *
 * Component objects represent metadata about Moodle components sourced from
 * package repositories.
 */
class Component {
    /**
     * Separator between component type and name.
     *
     * @see https://docs.moodle.org/dev/Frankenstyle
     *
     * @var string
     */
    const COMPONENT_NAME_SEPARATOR = '_';

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
     * @var \ComponentManager\PackageRepository\PackageRepository
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
     * @param string                                                $name
     * @param \ComponentManager\ComponentVersion[]                  $versions
     * @param \ComponentManager\PackageRepository\PackageRepository $packageRepository
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
     * Get the component's package repository.
     *
     * @return \ComponentManager\PackageRepository\PackageRepository
     */
    public function getPackageRepository() {
        return $this->packageRepository;
    }

    /**
     * Get the component's plugin name.
     *
     * @see https://docs.moodle.org/dev/Frankenstyle
     *
     * @return string
     */
    public function getPluginName() {
        list(, $name) = $this->getNameParts();

        return $name;
    }

    /**
     * Split the name of the component into plugin type and name.
     *
     * @see https://docs.moodle.org/dev/Frankenstyle
     *
     * @return string[]
     */
    public function getNameParts() {
        return explode(static::COMPONENT_NAME_SEPARATOR, $this->name, 2);
    }

    /**
     * Get the component's plugin type.
     *
     * @see https://docs.moodle.org/dev/Frankenstyle
     *
     * @return string
     */
    public function getPluginType() {
        list($type, ) = $this->getNameParts();

        return $type;
    }

    /**
     * Get package version.
     *
     * @param string $versionSpecification
     *
     * @return \ComponentManager\ComponentVersion
     */
    public function getVersion($versionSpecification) {
        foreach ($this->versions as $version) {
            if ($this->packageRepository->satisfiesVersion(
                    $versionSpecification, $version)) {
                return $version;
            }
        }

        throw new UnsatisfiedVersionException(
                "component version satisfying {$this->name}@{$versionSpecification} not found",
                UnsatisfiedVersionException::CODE_UNKNOWN_VERSION);
    }
}
