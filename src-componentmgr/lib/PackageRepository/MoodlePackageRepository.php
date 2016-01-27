<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\PackageRepository;

use ComponentManager\Component;
use ComponentManager\ComponentSource\GitComponentSource;
use ComponentManager\ComponentSource\ZipComponentSource;
use ComponentManager\ComponentSpecification;
use ComponentManager\ComponentVersion;
use ComponentManager\Exception\InvalidProjectException;
use ComponentManager\PlatformUtil;
use DateTime;
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
    const METADATA_CACHE_FILENAME = '%s%sMoodle%scomponents.json';

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
                       PlatformUtil::directorySeparator(),
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
        if (!property_exists($this->packageCache, $componentName)) {
            throw new InvalidProjectException(
                    "No component named \"{$componentName}\"",
                    InvalidProjectException::CODE_MISSING_COMPONENT);
        }
        $package = $this->packageCache->{$componentName};

        $versions = [];
        foreach ($package->versions as $version) {
            $sources = [];

            if ($version->downloadurl) {
                $sources[] = new ZipComponentSource(
                        $version->downloadurl, $version->downloadmd5);
            }

            if ($version->vcssystem === 'git') {
                $sources[] = new GitComponentSource(
                        $version->vcsrepositoryurl, $version->vcstag);
            }

            $versions[] = new ComponentVersion(
                    $version->version, $version->release, $version->maturity,
                    $sources);
        }

        return new Component($package->component, $versions, $this);
    }

    /**
     * Load the package cache.
     *
     * @return void
     */
    protected function loadPackageCache() {
        $this->packageCache = json_decode(file_get_contents(
                $this->getMetadataCacheFilename()));
    }

    /**
     * Load the package cache (if not already loaded).
     *
     * @return void
     */
    protected function maybeLoadPackageCache() {
        if ($this->packageCache === null) {
            $this->loadPackageCache();
        }
    }

    /**
     * @override \ComponentManager\PackageRepository\CachingPackageRepository
     */
    public function metadataCacheLastRefreshed() {
        $filename = $this->getMetadataCacheFilename();

        if (!$this->filesystem->exists($filename)) {
            return null;
        }

        $time = new DateTime();
        $time->setTimestamp(filemtime($filename));

        return $time;
    }

    /**
     * @override \ComponentManager\PackageRepository\CachingPackageRepository
     */
    public function refreshMetadataCache(LoggerInterface $logger) {
        $logger->debug('Fetching metadata', [
            'url' => static::PLUGIN_LIST_URL,
        ]);

        $client   = new Client();
        $response = $client->get(static::PLUGIN_LIST_URL);

        $logger->debug('Indexing component data');
        $rawComponents = json_decode($response->getBody());
        $components    = new stdClass();
        foreach ($rawComponents->plugins as $component) {
            if ($component->component === null) {
                $logger->warning('Component has no component name; is it a patch or external tool?', [
                    'id'   => $component->id,
                    'name' => $component->name,
                ]);
                continue;
            }
            $components->{$component->component} = $component;
        }

        $file = $this->getMetadataCacheFilename();
        $logger->info('Storing metadata', [
            'filename' => $file,
        ]);
        $this->filesystem->dumpFile($file, json_encode($components));
    }

    /**
     * @override \ComponentManager\PackageRepository\PackageRepository
     */
    public function satisfiesVersion($versionSpecification, ComponentVersion $version) {
        return (string) $versionSpecification === $version->getVersion()
                || (string) $versionSpecification === $version->getRelease();
    }
}
