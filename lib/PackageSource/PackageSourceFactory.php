<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\PackageSource;

/**
 * Package source factory.
 */
class PackageSourceFactory {
    /**
     * Class name format string.
     *
     * @var string
     */
    const CLASS_NAME_FORMAT = '\ComponentManager\PackageSource\%sPackageSource';

    /**
     * Get a package source.
     *
     * @return \ComponentManager\PackageSource\PackageSource
     */
    public function getPackageSource($name) {
        $className = sprintf(static::CLASS_NAME_FORMAT, $name);

        return new $className();
    }
}
