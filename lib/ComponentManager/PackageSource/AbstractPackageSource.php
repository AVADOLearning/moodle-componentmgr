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
 * Abstract package source.
 */
abstract class AbstractPackageSource {
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
}
