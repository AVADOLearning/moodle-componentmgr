<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\PackageSource;

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
     * Prepare the component's source for installation.
     *
     * @param \Symfony\Component\Filesystem\Filesystem $filesysem
     * @param string                                   $tempDirectory
     * @param mixed                                    $componentVersion
     *
     * @return void
     */
    public function prepare(Filesystem $filesystem, $tempDirectory,
                            $componentVersion);
}
