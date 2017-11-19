<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Platform;

use ComponentManager\Exception\PlatformException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Platform interface.
 *
 * Platform implementations provide a cross platform interface to per-platform
 * behaviours.
 */
interface Platform {
    /**
     * Initialiser.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem);

    /**
     * Create a temporary directory.
     *
     * @return string
     */
    public function createTempDirectory();

    /**
     * Apply expansions to the specified path.
     *
     * Used to expand path components, e.g. replacing ~ with the value of $HOME.
     *
     * @param string $path
     *
     * @return string
     */
    public function expandPath($path);

    /**
     * Get the platforms directory separator.
     *
     * @return string
     */
    public function getDirectorySeparator();

    /**
     * Get the path of the named executable.
     *
     * @param string $name
     *
     * @return string
     *
     * @throws PlatformException
     */
    public function getExecutablePath($name);

    /**
     * Get the user's home directory.
     *
     * @return mixed
     */
    public function getHomeDirectory();

    /**
     * Get the user's local shared data directory.
     *
     * @return string
     */
    public function getLocalSharedDirectory();

    /**
     * Get the path the hosting PHP executable.
     *
     * @return string
     */
    public function getPhpExecutable();

    /**
     * Get the path to the running PHP script.
     *
     * @return string
     */
    public function getPhpScript();

    /**
     * Get the current working directory.
     *
     * @return string
     */
    public function getWorkingDirectory();

    /**
     * Join the specified path parts.
     *
     * @param string[] $parts
     *
     * @return string
     */
    public function joinPaths($parts);

    /**
     * Remove all created temporary directories.
     *
     * @return void
     */
    public function removeTempDirectories();

    /**
     * Remove an individual temporary directory.
     *
     * @param string $directory
     *
     * @return void
     */
    public function removeTempDirectory($directory);
}
