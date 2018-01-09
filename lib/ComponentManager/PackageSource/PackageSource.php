<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\PackageSource;

use ComponentManager\Exception\RetryablePackageFailureException;
use ComponentManager\ResolvedComponentVersion;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Package source interface.
 *
 * Package sources provide Component Manager with access to components' source
 * code, either through obtaining pre-built releases or downloading them from
 * source control systems.
 */
interface PackageSource {
    /**
     * Get package source's ID.
     *
     * @return string
     */
    public function getId();

    /**
     * Get package source's name.
     *
     * @return string
     */
    public function getName();

    /**
     * Download the package's source to a given directory.
     *
     * Note than when implementing this method, package sources are expected to
     * honour pinned package versions by checking the final version supplied by
     * the resolved component version object. If this value is null, it is safe
     * to assume that the plugin does not appear in the project lock file and
     * that the package source should attempt to determine a final version from
     * the supplied specification.
     *
     * Implementations should raise an exception on failure.
     * {@link RetryablePackageFailureException} will be treated as non-fatal
     * until the last attempt.
     *
     * @param string                   $tempDirectory
     * @param integer|null             $timeout
     * @param ResolvedComponentVersion $resolvedComponentVersion
     * @param Filesystem               $filesystem
     * @param LoggerInterface          $logger
     *
     * @return string The path to the module's root directory.
     */
    public function obtainPackage($tempDirectory, $timeout,
                                  ResolvedComponentVersion $resolvedComponentVersion,
                                  Filesystem $filesystem,
                                  LoggerInterface $logger);
}
