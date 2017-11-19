<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager;

use OutOfBoundsException;
use stdClass;

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
     * Extra metadata.
     *
     * @var stdClass
     */
    protected $extra;

    /**
     * Initialiser.
     *
     * @param string   $name
     * @param string   $version
     * @param string   $packageRepository
     * @param string   $packageSource
     * @param stdClass $extra
     */
    public function __construct($name, $version, $packageRepository=null,
                                $packageSource=null, stdClass $extra=null) {
        $this->name    = $name;
        $this->version = $version;

        $this->packageRepository = $packageRepository;
        $this->packageSource     = $packageSource;

        $this->extra = $extra;
    }

    /**
     * Get the component's name.
     *
     * @return string
     *
     * @codeCoverageIgnore
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get the ID of the component's package repository.
     *
     * @return string
     *
     * @codeCoverageIgnore
     */
    public function getPackageRepository() {
        return $this->packageRepository;
    }

    /**
     * Get the ID of the component's package source.
     *
     * @return string
     *
     * @codeCoverageIgnore
     */
    public function getPackageSource() {
        return $this->packageSource;
    }

    /**
     * Get the component's version.
     *
     * @return string
     *
     * @codeCoverageIgnore
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * Get the specified extra key.
     *
     * @param string $property
     *
     * @return mixed
     *
     * @throws OutOfBoundsException
     */
    public function getExtra($property) {
        if (!property_exists($this->extra, $property)) {
            throw new OutOfBoundsException(sprintf(
                    'No value specified in project file for "%s"', $property));
        }

        return $this->extra->{$property};
    }
}
