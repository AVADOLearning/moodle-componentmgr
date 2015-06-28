<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\PackageRepository;

use ComponentManager\ComponentSpecification;

/**
 * Package repository interface.
 *
 * Package repositories contain metadata about the different available
 * components, including their names, versions and descriptions.
 */
interface PackageRepository {
    /**
     * Get repository identifier.
     *
     * @return string
     */
    public function getId();

    /**
     * Get repository name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get available versions for the specified component.
     *
     * @param \ComponentManager\ComponentSpecification $componentSpecification
     *
     * @return \ComponentManager\Component
     */
    public function getComponent(ComponentSpecification $componentSpecification);
}
