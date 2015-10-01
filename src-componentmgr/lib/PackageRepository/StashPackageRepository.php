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
use ComponentManager\ComponentSource\GitComponentSource;
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
     * Path to the list of branches within a repository.
     *
     * @var string
     */
    const REPOSITORY_BRANCHES_PATH = '/rest/api/1.0/projects/%s/repos/%s/branches';

    /**
     * Path to the list of tags within a repository.
     *
     * @var string
     */
    const REPOSITORY_TAGS_PATH = '/rest/api/1.0/projects/%s/repos/%s/tags';

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
    public function getComponent(ComponentSpecification $componentSpecification) {
        $this->maybeLoadPackageCache();

        $componentName = $componentSpecification->getName();
        $package = $this->packageCache->{$componentName};

        /* Unfortunately Stash doesn't allow us to retrieve a list of
         * repositories with branches/tags included, so we'll have to
         * incrementally retrieve them for each component as they're
         * requested. */

        $packageCacheDirty = false;

        if (!property_exists($package, 'branches')) {
            $path = $this->getRepositoryBranchesPath($componentName);

            $this->packageCache->{$componentName}->branches
                    = $this->getAllPages($path);
            $packageCacheDirty = true;
        }

        if (!property_exists($package, 'tags')) {
            $path = $this->getRepositoryTagsPath($componentName);

            $this->packageCache->{$componentName}->tags
                    = $this->getAllPages($path);
            $packageCacheDirty = true;
        }

        if ($packageCacheDirty) {
            // TODO: we should probably be logging writes here
            $this->writeMetadataCache($this->packageCache);
        }

        $versions = [];

        /* TODO: For the time being, we'll do these first so that tags take
         *       precedence over branches later when we attempt to satisfy
         *       version specifications. We should definitely be seeking to
         *       replace this crude approach with an indication of priority on
         *       source or version objects later. */
        foreach ($package->tags as $tag) {
            $sources = [];

            foreach ($package->links->clone as $cloneSource) {
                $sources[] = new GitComponentSource(
                        $cloneSource->href, $tag->displayId);
            }

            $versions[] = new ComponentVersion(
                null, $tag->displayId, null, $sources);
        }

        foreach ($package->branches as $branch) {
            $sources = [];

            foreach ($package->links->clone as $cloneSource) {
                $sources[] = new GitComponentSource(
                        $cloneSource->href, $branch->displayId);
            }

            $versions[] = new ComponentVersion(
                    null, $branch->displayId, null, $sources);
        }

        return new Component($package->slug, $versions, $this);
    }

    /**
     * @override \ComponentManager\PackageRepository\PackageRepository
     */
    public function satisfiesVersion($versionSpecification, ComponentVersion $version) {
        return $versionSpecification === $version->getRelease();
    }

    /**
     * @override \ComponentManager\PackageRepository\CachingPackageRepository
     */
    public function refreshMetadataCache(LoggerInterface $logger) {
        $path = $this->getProjectRepositoryListUrl();

        $logger->debug('Fetching metadata', [
            'path' => $path,
        ]);
        $rawComponents = $this->getAllPages($path);

        $logger->debug('Indexing component data');
        $components = new stdClass();
        foreach ($rawComponents as $component) {
            $components->{$component->slug} = $component;
        }

        $logger->info('Storing metadata', [
            'filename' => $this->getMetadataCacheFilename(),
        ]);
        $this->writeMetadataCache($components);
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
     * Write the metadata cache to the disk.
     *
     * @param \stdClass $components
     *
     * @return void
     */
    protected function writeMetadataCache($components) {
        $file = $this->getMetadataCacheFilename();
        $this->filesystem->dumpFile($file, json_encode($components));
    }

    /**
     * Perform a GET request on a Stash path.
     *
     * @param string  $path
     * @param mixed[] $queryParams
     *
     * @return mixed The JSON-decoded representation of the response body.
     */
    protected function get($path, array $queryParams=[]) {
        $uri = $this->options->uri . $path;

        $client = new Client();
        $response = $client->get($uri, [
            'headers' => [
                'Authorization' => "Basic {$this->options->authentication}",
            ],
            'query' => $queryParams,
        ]);

        return json_decode($response->getBody());
    }

    /**
     * Get a complete result for a paged Stash REST API resource.
     *
     * @param string  $path
     * @param mixed[] $queryParams
     *
     * @return mixed The value attribute of the JSON-decoded response body.
     */
    protected function getAllPages($path, array $queryParams=[]) {
        $values = [];

        $responseBody = (object) [
            'limit' => 25,
            'start' => 0,
            'size'  => 0,
        ];

        do {
            $queryParams['limit'] = $responseBody->limit;
            $queryParams['start'] = $responseBody->start + $responseBody->size;

            $responseBody = $this->get($path, $queryParams);

            $values = array_merge($values, $responseBody->values);
        } while (!$responseBody->isLastPage);

        return $values;
    }

    /**
     * Get the repository list path for this Stash project.
     *
     * @return string
     */
    protected function getProjectRepositoryListUrl() {
        return sprintf(static::PROJECT_REPOSITORY_LIST_PATH,
                       $this->options->project);
    }

    /**
     * Get the branch list path for the specified repository within the project.
     *
     * @param string $componentName
     *
     * @return string
     */
    protected function getRepositoryBranchesPath($componentName) {
        return sprintf(static::REPOSITORY_BRANCHES_PATH, $this->options->project,
                       $componentName);
    }

    /**
     * Get the tag list path for the specified repository within this project.
     *
     * @param string $componentName
     *
     * @return string
     */
    protected function getRepositoryTagsPath($componentName) {
        return sprintf(static::REPOSITORY_TAGS_PATH, $this->options->project,
                       $componentName);
    }
}
