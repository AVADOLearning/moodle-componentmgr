<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\PackageRepository;

interface PackageRepository {
    /**
     * Get available versions for the specified package.
     *
     * @param string $packageName
     *
     * @return \ComponentManager\Package\PackageVersion[]
     */
    public function getPackageVersions($packageName);
}
