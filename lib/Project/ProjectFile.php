<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Project;

use ComponentManager\ComponentSpecification;
use ComponentManager\Exception\InvalidProjectException;
use ComponentManager\Exception\NotImplementedException;

/**
 * Project file.
 *
 * The project file contains the majority of the configuration, including
 * sources for component information, sources for distribution packages and
 * desired version specifications.
 */
class ProjectFile extends JsonFile {
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
     * Get component specifications.
     *
     * @return \ComponentManager\ComponentSpecification[]
     */
    public function getComponentSpecifications() {
        if ($this->components === null) {
            $this->components = [];

            foreach ($this->contents->components as $name => $component) {
                $properties = array_fill_keys(
                        ['packageRepository', 'packageSource', 'version'],
                        null);
                $extra      = [];

                foreach ($component as $key => &$value) {
                    switch ($key) {
                        case 'packageRepository':
                        case 'packageSource':
                        case 'version':
                            $properties[$key] = $value;
                            break;

                        default:
                            $extra[$key] = $value;
                    }
                }

                $this->components[$name] = new ComponentSpecification(
                        $name, $properties['version'],
                        $properties['packageRepository'], $properties['packageSource'],
                        (object) $extra);
            }
        }

        return $this->components;
    }

    /**
     * Get package repositories.
     *
     * @return \ComponentManager\PackageRepository\PackageRepository[]
     */
    public function getPackageRepositories() {
        return $this->contents->packageRepositories;
    }

    /**
     * @override \ComponentManager\Project\JsonFile
     */
    public function dump() {
        throw new NotImplementedException();
    }

    /**
     * Get the Moodle version.
     *
     * @return mixed
     *
     * @throws \ComponentManager\Exception\InvalidProjectException
     */
    public function getMoodleVersion() {
        if (!property_exists($this->contents, 'moodle')
                || !property_exists($this->contents->moodle, 'version')) {
            throw new InvalidProjectException('Missing "moodle.version" key',
                    InvalidProjectException::CODE_MISSING_MOODLE_VALUE);
        }

        return $this->contents->moodle->version;
    }
}
