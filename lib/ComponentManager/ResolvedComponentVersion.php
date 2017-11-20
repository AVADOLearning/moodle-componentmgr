<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager;

use ComponentManager\Component;
use ComponentManager\ComponentSpecification;
use ComponentManager\ComponentVersion;
use ComponentManager\PackageRepository\PackageRepository;
use JsonSerializable;

/**
 * Resolved component version.
 *
 * A resolved component version represents a version of a component that has
 * been resolved from a project's components specification.
 */
class ResolvedComponentVersion implements JsonSerializable {
    /**
     * Component.
     *
     * @var Component
     */
    protected $component;

    /**
     * Package repository.
     *
     * @var PackageRepository
     */
    protected $packageRepository;

    /**
     * Component specification.
     *
     * @var ComponentSpecification
     */
    protected $specification;

    /**
     * Component version.
     *
     * @var ComponentVersion
     */
    protected $version;

    /**
     * Final component version.
     *
     * @var string
     */
    protected $finalVersion;

    /**
     * Initialiser.
     *
     * @param ComponentSpecification $specification
     * @param PackageRepository      $packageRepository
     * @param Component              $component
     * @param ComponentVersion       $version
     */
    public function __construct(ComponentSpecification $specification,
                                PackageRepository $packageRepository,
                                Component $component,
                                ComponentVersion $version) {
        $this->specification     = $specification;
        $this->packageRepository = $packageRepository;
        $this->component         = $component;
        $this->version           = $version;
    }

    /**
     * Get the component.
     *
     * @return Component
     */
    public function getComponent() {
        return $this->component;
    }

    /**
     * Get the package repository.
     *
     * @return PackageRepository
     */
    public function getPackageRepository() {
        return $this->packageRepository;
    }

    /**
     * Get the component specification.
     *
     * @return ComponentSpecification
     */
    public function getSpecification() {
        return $this->specification;
    }

    /**
     * Get the component version.
     *
     * @return ComponentVersion
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Get the final component version.
     *
     * @return string
     */
    public function getFinalVersion() {
        return $this->finalVersion;
    }

    /**
     * Set the final component version.
     *
     * @param string $finalVersion
     *
     * @return void
     */
    public function setFinalVersion($finalVersion) {
        $this->finalVersion = $finalVersion;
    }

    /**
     * @inheritdoc JsonSerializable
     */
    public function jsonSerialize() {
        return (object) [
            'componentName'       => $this->component->getName(),
            'packageRepositoryId' => $this->packageRepository->getId(),
            'finalVersion'        => $this->finalVersion,
        ];
    }
}
