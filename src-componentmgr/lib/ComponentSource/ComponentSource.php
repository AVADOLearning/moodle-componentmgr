<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\ComponentSource;

/**
 * Component source.
 *
 * A component source is a means of describing a source of a specific version of
 * a component. They can represent any form of source archive or built
 * distribution, and each {@link \ComponentManager\PackageSource\PackageSource}
 * implementation must manually implement support for each individual type of
 * component source they wish to handle.
 */
interface ComponentSource {
    /**
     * Get the human readable name of the component source's type.
     *
     * @return string
     */
    public function getName();

    /**
     * Get the type of the component source.
     *
     * @return string
     */
    public function getType();
}
