<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
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
     * @param string                                                $versions
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
                "component version satisfying {$this->name}@{$version} not found",
                UnsatisfiedVersionException::CODE_UNKNOWN_VERSION);
    }
}
