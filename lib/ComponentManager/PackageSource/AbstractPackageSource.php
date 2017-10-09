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
     * HTTP client.
     *
     * @var HttpClient
     */
    protected $httpClient;

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
     * @param HttpClient                               $httpClient
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(Platform $platform, HttpClient $httpClient,
                                Filesystem $filesystem) {
        $this->platform   = $platform;
        $this->httpClient = $httpClient;
        $this->filesystem = $filesystem;
    }
}
