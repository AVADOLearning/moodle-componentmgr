<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Project;

use ComponentManager\Component;
use ComponentManager\ComponentSpecification;
use ComponentManager\Exception\InvalidProjectException;
use ComponentManager\Exception\NotImplementedException;
use stdClass;

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
     * @var Component[]
     */
    protected $componentSpecifications;

    /**
     * Get component specifications.
     *
     * @return ComponentSpecification[]
     */
    public function getComponentSpecifications() {
        if ($this->componentSpecifications === null) {
            $this->componentSpecifications = [];

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

                $this->componentSpecifications[$name] = new ComponentSpecification(
                        $name, $properties['version'],
                        $properties['packageRepository'], $properties['packageSource'],
                        (object) $extra);
            }
        }

        return $this->componentSpecifications;
    }

    /**
     * Get package repositories.
     *
     * @return stdClass[]
     */
    public function getPackageRepositories() {
        return (array) $this->contents->packageRepositories;
    }

    /**
     * @override JsonFile
     */
    public function dump() {
        throw new NotImplementedException();
    }

    /**
     * Get the Moodle version.
     *
     * @return mixed
     *
     * @throws InvalidProjectException
     */
    public function getMoodleVersion() {
        $hasProperties = property_exists($this->contents, 'moodle')
                && property_exists($this->contents->moodle, 'version');

        return $hasProperties ? $this->contents->moodle->version : null;
    }
}
