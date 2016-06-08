<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\PackageRepository;

/**
 * Caching package repository base class.
 *
 * Contains utility methods for use across caching package repositories.
 */
abstract class AbstractCachingPackageRepository
        extends AbstractPackageRepository {
    /**
     * Cache directory format string.
     *
     * @var string[]
     */
    const CACHE_DIRECTORY_FORMAT = ['%s', 'componentmgr', 'cache', '%s'];

    /**
     * Get repository cache directory.
     *
     * @return string
     */
    protected function getMetadataCacheDirectory() {
        return sprintf(
                $this->platform->joinPaths(static::CACHE_DIRECTORY_FORMAT),
                $this->platform->getLocalSharedDirectory(), $this->getId());
    }
}
