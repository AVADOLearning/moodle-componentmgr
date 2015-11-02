<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\Project;
use ComponentManager\ComponentSpecification;

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
                $version           = property_exists($component, 'version')
                    ? $component->version           : null;
                $packageRepository = property_exists($component, 'packageRepository')
                    ? $component->packageRepository : null;
                $packageSource     = property_exists($component, 'packageSource')
                    ? $component->packageSource     : null;

                $this->components[$name] = new ComponentSpecification(
                        $name, $version,
                        $packageRepository, $packageSource);
            }
        }

        return $this->components;
    }

    /**
     * Get package repositories.
     *
     * @return \ComponentManager\PackageRepository\PackageRepository
     */
    public function getPackageRepositories() {
        return $this->contents->packageRepositories;
    }
}
