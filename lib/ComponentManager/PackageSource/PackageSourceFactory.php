<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\PackageSource;

use ComponentManager\HttpClient;
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
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * HTTP client.
     *
     * @var HttpClient
     */
    protected $httpClient;

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
     * @param HttpClient $httpClient
     * @param Filesystem $filesystem
     */
    public function __construct(Platform $platform, HttpClient $httpClient,
                                Filesystem $filesystem) {
        $this->platform   = $platform;
        $this->httpClient = $httpClient;
        $this->filesystem = $filesystem;
    }

    /**
     * Get a package source.
     *
     * @return PackageSource
     */
    public function getPackageSource($name) {
        $className = sprintf(static::CLASS_NAME_FORMAT, $name);

        return new $className($this->platform, $this->httpClient, $this->filesystem);
    }
}
