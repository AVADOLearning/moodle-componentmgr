<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Step;

use ComponentManager\Exception\InvalidProjectException;
use ComponentManager\PackageRepository\CachingPackageRepository;
use Psr\Log\LoggerInterface;

/**
 * Ensure that the project's package repositories have cached metadata.
 */
class VerifyPackageRepositoriesCachedStep implements Step {
    /**
     * Package repositories.
     *
     * @var \ComponentManager\PackageRepository\PackageRepository[]
     */
    protected $packageRepositories;

    /**
     * Initialiser.
     *
     * @param \ComponentManager\PackageRepository\PackageRepository[] $packageRepositories
     */
    public function __construct($packageRepositories) {
        $this->packageRepositories = $packageRepositories;
    }

    /**
     * @override \ComponentManager\Task\Step
     */
    public function execute($task, LoggerInterface $logger) {
        $logger->info('Ensuring necessary package repositories are cached');

        $haveCaches = true;

        foreach ($this->packageRepositories as $name => $packageRepository) {
            if ($packageRepository instanceof CachingPackageRepository) {
                $lastRefreshed = $packageRepository->metadataCacheLastRefreshed();

                if ($lastRefreshed === null) {
                    $logger->error('Package repository missing cache; requires refresh', [
                        'packageRepository' => $name,
                    ]);

                    $haveCaches = false;
                }
            }
        }

        if (!$haveCaches) {
            throw new InvalidProjectException(
                    'One or more caching package repositories was missing its metadata cache',
                    InvalidProjectException::CODE_MISSING_PACKAGE_REPOSITORY_CACHE);
        }
    }
}
