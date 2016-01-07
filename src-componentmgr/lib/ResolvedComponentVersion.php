<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager;
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
     * @var \ComponentManager\Component
     */
    protected $component;

    /**
     * Package repository.
     *
     * @var \ComponentManager\PackageRepository\PackageRepository
     */
    protected $packageRepository;

    /**
     * Component specification.
     *
     * @var \ComponentManager\ComponentSpecification
     */
    protected $specification;

    /**
     * Component version.
     *
     * @var \ComponentManager\ComponentVersion
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
     * @param \ComponentManager\ComponentSpecification              $specification
     * @param \ComponentManager\PackageRepository\PackageRepository $packageRepository
     * @param \ComponentManager\Component                           $component
     * @param \ComponentManager\ComponentVersion                    $version
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
     * @return \ComponentManager\Component
     */
    public function getComponent() {
        return $this->component;
    }

    /**
     * Get the package repository.
     *
     * @return \ComponentManager\PackageRepository\PackageRepository
     */
    public function getPackageRepository() {
        return $this->packageRepository;
    }

    /**
     * Get the component specification.
     *
     * @return \ComponentManager\ComponentSpecification
     */
    public function getSpecification() {
        return $this->specification;
    }

    /**
     * Get the component version.
     *
     * @return \ComponentManager\ComponentVersion
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
     * @override \JsonSerializable
     */
    public function jsonSerialize() {
        return (object) [
            'componentName'       => $this->component->getName(),
            'packageRepositoryId' => $this->packageRepository->getId(),
            'finalVersion'        => $this->finalVersion,
        ];
    }
}
