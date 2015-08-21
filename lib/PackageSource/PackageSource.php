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
use Psr\Log\LoggerInterface;

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
     * @param string                             $tempDirectory
     * @param \ComponentManager\Component        $component
     * @param \ComponentManager\ComponentVersion $version
     * @param \Psr\Log\LoggerInterface           $logger
     *
     * @return string The path to the module's root directory.
     */
    public function obtainPackage($tempDirectory,
                                  Component $component,
                                  ComponentVersion $version,
                                  LoggerInterface $logger);
}
