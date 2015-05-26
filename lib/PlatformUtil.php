<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager;

use ComponentManager\Exception\PlatformException;

/**
 * Platform utility methods.
 *
 * Provides functionality for working around platform inconsistencies.
 */
class PlatformUtil {
    /**
     * Retrieve the platform's directory separator.
     *
     * @return string
     */
    public static function directorySeparator() {
        return DIRECTORY_SEPARATOR;
    }

    /**
     * Retrieve the user's home directory.
     *
     * @return string
     */
    public static function homeDirectory() {
        switch (PHP_OS) {
            case 'Linux': return get_env('HOME');
            case 'WINNT': return get_env('HOMEDRIVE') . get_env('HOMEPATH');
            default:      throw new PlatformException(PHP_OS);
        }
    }

    /**
     * Retrieve the user's local shared data directory.
     *
     * @return string
     */
    public static function localSharedDirectory() {
        switch (PHP_OS) {
            case 'Linux': return static::homeDirectory() . '/.local/share';
            case 'WINNT': return getenv('APPDATA');
            default:      throw new PlatformException(PHP_OS);
        }
    }
}
