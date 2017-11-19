<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\PackageRepository;

use ComponentManager\HttpClient;
use ComponentManager\Platform\Platform;
use stdClass;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Package repository factory.
 */
class PackageRepositoryFactory {
    /**
     * Class name format.
     *
     * @var string
     */
    const CLASS_NAME_FORMAT = '\ComponentManager\PackageRepository\%sPackageRepository';

    /**
     * Filesystem.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Platform.
     *
     * @var Platform
     */
    protected $platform;

    /**
     * HTTP client.
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * Initialiser.
     *
     * @param Filesystem $filesystem
     * @param HttpClient $httpClient
     * @param Platform   $platform
     */
    public function __construct(Filesystem $filesystem, HttpClient $httpClient, Platform $platform) {
        $this->filesystem = $filesystem;
        $this->httpClient = $httpClient;
        $this->platform   = $platform;
    }

    /**
     * Get package repository.
     *
     * @param string   $id
     * @param stdClass $options
     *
     * @return PackageRepository
     */
    public function getPackageRepository($id, $options) {
        $className = sprintf(self::CLASS_NAME_FORMAT, $id);

        return new $className(
                $this->filesystem, $this->httpClient, $this->platform, $options);
    }
}
