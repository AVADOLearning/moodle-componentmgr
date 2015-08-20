<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\PackageSource;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Zip package source.
 */
class ZipPackageSource extends AbstractPackageSource
        implements PackageSource {
    /**
     * @override \ComponentManager\PackageSource\PackageSource
     */
    public function getId() {
        return 'Zip';
    }

    /**
     * @override \ComponentManager\PackageSource\PackageSource
     */
    public function getName() {
        return 'Zip';
    }

    /**
     * @override \ComponentManager\PackageSource\PackageSource
     */
    public function prepare(Filesystem $filesystem, $tempDirectory,
                            $componentVersion) {
        // TODO: Implement prepare() method.
    }
}
