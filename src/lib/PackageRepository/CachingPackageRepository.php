<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\PackageRepository;

use Psr\Log\LoggerInterface;

/**
 * Caching package repository interface.
 */
interface CachingPackageRepository {
    /**
     * Update the metadata cache.
     *
     * @return void
     */
    public function refreshMetadataCache(LoggerInterface $logger);
}
