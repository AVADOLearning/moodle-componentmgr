<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\PackageSource;

use ComponentManager\Component;
use ComponentManager\ComponentVersion;
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
     * @param string                                     $tempDirectory
     * @param \ComponentManager\ResolvedComponentVersion $resolvedComponentVersion
     * @param \Symfony\Component\Filesystem\Filesystem   $filesystem
     * @param \Psr\Log\LoggerInterface                   $logger
     *
     * @return string The path to the module's root directory.
     */
    public function obtainPackage($tempDirectory,
                                  ResolvedComponentVersion $resolvedComponentVersion,
                                  Filesystem $filesystem,
                                  LoggerInterface $logger);
}
