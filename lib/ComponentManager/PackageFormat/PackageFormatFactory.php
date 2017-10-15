<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\PackageFormat;

use ComponentManager\Platform\Platform;

/**
 * Package format factory.
 */
class PackageFormatFactory {
    /**
     * Class name format string.
     *
     * @var string
     */
    const CLASS_NAME_FORMAT = '\ComponentManager\PackageFormat\%sPackageFormat';

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
    public function __construct(Platform $platform, $filesystem) {
        $this->platform   = $platform;
        $this->filesystem = $filesystem;
    }

    /**
     * Get the specified package format.
     *
     * @param string $name
     *
     * @return \ComponentManager\PackageFormat\PackageFormat
     */
    public function getPackageFormat($name) {
        $className = sprintf(static::CLASS_NAME_FORMAT, $name);

        return new $className($this->platform, $this->filesystem);
    }
}
