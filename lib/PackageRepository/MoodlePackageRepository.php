<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\PackageRepository;

use ComponentManager\Component;
use ComponentManager\ComponentSpecification;
use ComponentManager\ComponentVersion;
use ComponentManager\PlatformUtil;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use stdClass;
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
    const METADATA_CACHE_FILENAME = '%s%scomponents.json';

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
     * Package cache.
     *
     * @var \stdClass
     */
    protected $packageCache;

    /**
     * Get the component metadata cache filename.
     *
     * @return string
     */
    protected function getMetadataCacheFilename() {
        return sprintf(static::METADATA_CACHE_FILENAME,
                       $this->cacheDirectory,
                       PlatformUtil::directorySeparator());
    }

    /**
     * @override \ComponentManager\PackageRepository\PackageRepository
     */
    public function getId() {
        return 'Moodle';
    }

    /**
     * @override \ComponentManager\PackageRepository\PackageRepository
     */
    public function getName() {
        return 'Moodle.org plugin repository';
    }

    /**
     * @override \ComponentManager\PackageRepository\PackageRepository
     */
    public function getComponent(ComponentSpecification $componentSpecification) {
        $this->maybeLoadPackageCache();

        $componentName = $componentSpecification->getName();
        $package = $this->packageCache->{$componentName};

        $versions = [];
        foreach ($package->versions as $version) {
            $versions[] = new ComponentVersion($version->version, $version->release, $version->maturity);
        }

        return new Component($package->component, $versions);
    }

    protected function loadPackageCache() {
        $this->packageCache = json_decode(file_get_contents(
                $this->getMetadataCacheFilename()));
    }

    protected function maybeLoadPackageCache() {
        if ($this->packageCache === null) {
            $this->loadPackageCache();
        }
    }

    /**
     * @override \ComponentManager\PackageRepository\CachingPackageRepository
     */
    public function refreshMetadataCache(LoggerInterface $logger) {
        $logger->debug('Fetching metadata', ['url' => static::PLUGIN_LIST_URL]);
        $client   = new Client();
        $response = $client->get(static::PLUGIN_LIST_URL);

        $logger->debug('Indexing component data');
        $rawComponents = json_decode($response->getBody());
        $components    = new stdClass();
        foreach ($rawComponents->plugins as $component) {
            if ($component->component === null) {
                $logger->warn('Component has no component name; is it a patch or external tool?', [
                    'id'   => $component->id,
                    'name' => $component->name,
                ]);
                continue;
            }
            $components->{$component->component} = $component;
        }

        $file = $this->getMetadataCacheFilename();
        $logger->info('Storing metadata', ['file' => $file]);
        $this->filesystem->dumpFile($file, json_encode($components));
    }
}
