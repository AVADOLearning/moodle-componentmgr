<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Platform;

/**
 * Abstract platform implementation.
 *
 * Utility methods for individual platforms.
 */
abstract class AbstractPlatform implements Platform {
    /**
     * Temporary file/directory prefix.
     *
     * @var string
     */
    const TEMP_PREFIX = 'componentmgr-';

    /**
     * @override \ComponentManager\Platform\Platform
     */
    public function createTempDirectory() {
        $root      = sys_get_temp_dir();
        $directory = tempnam($root, static::TEMP_PREFIX);

        unlink($directory);
        mkdir($directory);

        return $directory;
    }

    /**
     * @override \ComponentManager\Platform\Platform
     */
    public function getDirectorySeparator() {
        return DIRECTORY_SEPARATOR;
    }

    /**
     * @override \ComponentManager\Platform\Platform
     */
    public function getWorkingDirectory() {
        return getcwd();
    }

    /**
     * @override \ComponentManager\Platform\Platform
     */
    public function joinPaths($parts) {
        return implode(static::getDirectorySeparator(), $parts);
    }

    /**
     * @override \ComponentManager\Platform\Platform
     */
    public function getPhpExecutable() {
        return PHP_BINARY;
    }

    /**
     * @override \ComponentManager\Platform\Platform
     */
    public function getPhpScript() {
        return $_SERVER['argv'][0];
    }
}
