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
 * Version control package source.
 */
class VersionControlPackageSource extends AbstractPackageSource
        implements PackageSource {
    /**
     * @override \ComponentManager\PackageSource\PackageSource
     */
    public function getId() {
        return 'VersionControl';
    }

    /**
     * @override \ComponentManager\PackageSource\PackageSource
     */
    public function getName() {
        return 'Version control';
    }
}
