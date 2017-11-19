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
use Symfony\Component\Filesystem\Filesystem;

/**
 * Base abstract package format.
 */
abstract class AbstractPackageFormat {
    /**
     * Filesystem.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Platform support library.
     *
     * @var Platform
     */
    protected $platform;

    /**
     * Initialiser.
     *
     * @param Platform   $platform
     * @param Filesystem $filesystem
     */
    public function __construct(Platform $platform, Filesystem $filesystem) {
        $this->platform   = $platform;
        $this->filesystem = $filesystem;
    }
}
