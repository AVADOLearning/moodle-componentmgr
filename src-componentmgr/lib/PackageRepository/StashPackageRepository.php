<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\PackageRepository;

use ComponentManager\ComponentSpecification;
use ComponentManager\ComponentVersion;
use ComponentManager\PlatformUtil;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use stdClass;

/**
 * Atlassian Stash project package repository.
 *
 * Requires the following configuration keys to be set for each relevant
 * packageRepository stanza in the project file:
 *
 * -> uri - the root URL of the Stash web UI, e.g. "http://stash.atlassian.com".
 * -> project - the name of the Stash project to search, e.g. "MDL".
 * -> authentication - the Base64-encoded representation of the user's
 *    "username:password" combination. You're advised to use a read only user
 *    with access to only the specific project, as Base64 encoded text is
 *    *trivial* to decode.
 *
 * To use multiple projects, add one stanza to the configuration file for each
 * Stash project.
 */
class StashPackageRepository extends AbstractPackageRepository
        implements CachingPackageRepository, PackageRepository {
    /**
     * Metadata cache filename.
     *
     * @var string
     */
    const METADATA_CACHE_FILENAME = '%s%sStash%s%s.json';

    /**
     * Path to the list of repositories within a project.
     *
     * @var string
     */
    const PROJECT_REPOSITORY_LIST_PATH = '/rest/api/1.0/projects/%s/repos';

    /**
     * Path to the list of tags within a repository.
     *
     * @var string
     */
    const REPOSITORY_TAGS_PATH = '';

    /**
     * Package cache.
     *
     * @var \stdClass
     */
    protected $packageCache;

    /**
     * @override \ComponentManager\PackageRepository\PackageRepository
     */
    public function getId() {
        return 'Stash';
    }

    /**
     * @override \ComponentManager\PackageRepository\PackageRepository
     */
    public function getName() {
        return 'Atlassian Stash plugin repository';
    }

    /**
     * @override \ComponentManager\PackageRepository\PackageRepository
     */
    public function getComponent(ComponentSpecification $componentSpecification) {}

    /**
     * @override \ComponentManager\PackageRepository\PackageRepository
     */
    public function satisfiesVersion($versionSpecification, ComponentVersion $version) {}

    /**
     * @override \ComponentManager\PackageRepository\CachingPackageRepository
     */
    public function refreshMetadataCache(LoggerInterface $logger) {
        $path = $this->getProjectRepositoryListUrl();

        $logger->debug('Fetching metadata', [
            'path' => $path,
        ]);
        $rawComponents = $this->get($path);

        $logger->debug('Indexing component data');
        $components    = new stdClass();
        foreach ($rawComponents->values as $component) {
            $components->{$component->slug} = $component;
        }

        $file = $this->getMetadataCacheFilename();
        $logger->info('Storing metadata', [
            'filename' => $file
        ]);
        $this->filesystem->dumpFile($file, json_encode($components));
    }

    /**
     * Get the repository list URL for this Stash project.
     *
     * @return string
     */
    protected function getProjectRepositoryListUrl() {
        return sprintf(static::PROJECT_REPOSITORY_LIST_PATH,
                       $this->options->project);
    }

    /**
     * Get the component metadata cache filename.
     *
     * @return string
     */
    protected function getMetadataCacheFilename() {
        $urlHash = parse_url($this->options->uri, PHP_URL_HOST) . '-'
                 . $this->options->project;

        return sprintf(static::METADATA_CACHE_FILENAME,
                       $this->cacheDirectory,
                       PlatformUtil::directorySeparator(),
                       PlatformUtil::directorySeparator(),
                       $urlHash);
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
     * Perform a GET request on a Stash path.
     *
     * @param string $path
     *
     * @return mixed The JSON-decoded representation of the response body.
     */
    protected function get($path) {
        $uri = $this->options->uri . $path;

        $client = new Client();
        $response = $client->get($uri, [
            'headers' => [
                'Authorization' => "Basic {$this->options->authentication}",
            ],
        ]);

        return json_decode($response->getBody());
    }
}
