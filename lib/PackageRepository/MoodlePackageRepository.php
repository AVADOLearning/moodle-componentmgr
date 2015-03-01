<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\PackageRepository;

use GuzzleHttp\Client;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Moodle.org/plugins package repository.
 */
class MoodlePackageRepository extends AbstractPackageRepository
        implements CachingPackageRepository, PackageRepository {
    /**
     * Metadata cache filename.
     *
     * @var string
     */
    const METADATA_CACHE_FILENAME = '%s/components.json';

    /**
     * Plugin information endpoint URL.
     *
     * The service here returns information about the specified and latest
     * available releases of the specified plugins.
     *
     * @var string
     */
    const PLUGIN_INFO_URL = 'https://download.moodle.org/api/1.2/pluginfo.php';

    /**
     * Complete plugin list endpoint URL.
     *
     * Returns all metadata and versions of all plugins known to the plugin
     * repository.
     *
     * @var string
     */
    const PLUGIN_LIST_URL = 'https://download.moodle.org/api/1.3/pluglist.php';

    /**
     *
     */
    protected $cacheDirectory;

    /**
     *
     */
    protected $filesystem;

    public function __construct(Filesystem $filesystem, $cacheDirectory) {
        $this->filesystem     = $filesystem;
        $this->cacheDirectory = $cacheDirectory;
    }

    protected function getMetadataCacheFilename() {
        return sprintf(static::METADATA_CACHE_FILENAME, $this->cacheDirectory);
    }

    /**
     * @override \ComponentManager\PackageRepository\AbstractPackageRepository
     */
    public function getPackageVersions($packageNames) {
        // load from the cache
    }

    /**
     * @override \ComponentManager\PackageRepository\CachingPackageRepository
     */
    public function refreshMetadataCache() {
        $client   = new Client();
        $response = $client->get(static::PLUGIN_LIST_URL);

        $this->filesystem->dumpFile($this->getMetadataCacheFilename(),
                                    $response->getBody());
    }
}
