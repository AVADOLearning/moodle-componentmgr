<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\PackageRepository;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Abstract package repository.
 */
abstract class AbstractPackageRepository {
    /**
     * Base directory for our disk cache.
     *
     * @var string
     */
    protected $cacheDirectory;

    /**
     * Filesystem.
     *
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Options.
     *
     * @var \stdClass
     */
    protected $options;

    /**
     * Initialiser.
     *
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     * @param string                                   $cacheDirectory
     * @param \stdClass                                $options
     */
    public function __construct(Filesystem $filesystem, $cacheDirectory,
                                $options) {
        $this->filesystem     = $filesystem;
        $this->cacheDirectory = $cacheDirectory;
        $this->options        = $options;
    }
}
