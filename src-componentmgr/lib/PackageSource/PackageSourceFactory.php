<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\PackageSource;

use Symfony\Component\Filesystem\Filesystem;

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
     * Filesystem.
     *
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Initialiser.
     *
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem) {
        $this->filesystem = $filesystem;
    }

    /**
     * Get a package source.
     *
     * @return \ComponentManager\PackageSource\PackageSource
     */
    public function getPackageSource($name) {
        $className = sprintf(static::CLASS_NAME_FORMAT, $name);

        return new $className($this->filesystem);
    }
}
