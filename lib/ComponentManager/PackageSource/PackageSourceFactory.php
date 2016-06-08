<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\PackageSource;

use ComponentManager\Platform\Platform;
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
     * Platform support library.
     *
     * @var \ComponentManager\Platform\Platform
     */
    protected $platform;

    /**
     * Initialiser.
     *
     * @param \ComponentManager\Platform\Platform      $platform
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(Platform $platform, Filesystem $filesystem) {
        $this->platform   = $platform;
        $this->filesystem = $filesystem;
    }

    /**
     * Get a package source.
     *
     * @return \ComponentManager\PackageSource\PackageSource
     */
    public function getPackageSource($name) {
        $className = sprintf(static::CLASS_NAME_FORMAT, $name);

        return new $className($this->platform, $this->filesystem);
    }
}
