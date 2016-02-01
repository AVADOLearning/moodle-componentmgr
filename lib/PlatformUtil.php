<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
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
     * Temporary file/directory prefix.
     *
     * @var string
     */
    const TEMP_PREFIX = 'componentmgr-';

    /**
     * Create a temporary directory in the system temporary directory.
     *
     * @return string The absolute path to the created directory.
     */
    public static function createTempDirectory() {
        $root      = sys_get_temp_dir();
        $directory = tempnam($root, static::TEMP_PREFIX);

        unlink($directory);
        mkdir($directory);

        return $directory;
    }

    /**
     * Retrieve the platform's directory separator.
     *
     * @return string
     */
    public static function directorySeparator() {
        return DIRECTORY_SEPARATOR;
    }

    /**
     * Find the named executable on the system PATH.
     *
     * @param string $name
     *
     * @return string
     */
    public static function executable($name) {
        switch (PHP_OS) {
            case 'Linux':
                $delimiter = ':';
                break;

            case 'WINNT':
                $delimiter = ';';
                break;

            default:
                throw new PlatformException(
                        PHP_OS, PlatformException::CODE_UNKNOWN_PLATFORM);
        }

        $paths    = explode($delimiter, getenv('PATH'));
        $pathexts = (PHP_OS === 'WINNT')
                ? explode($delimiter, getenv('PATHEXT')) : null;

        foreach ($paths as $path) {
            $executable = $path . static::directorySeparator() . $name;
            if (is_executable($executable)) {
                return $executable;
            }

            if (PHP_OS === 'WINNT') {
                foreach ($pathexts as $pathext) {
                    $executableext = $executable . $pathext;
                    if (is_executable($executableext)) {
                        return $executableext;
                    }
                }
            }
        }

        throw new PlatformException($executable, PlatformException::CODE_MISSING_EXECUTABLE);
    }

    /**
     * Retrieve the user's home directory.
     *
     * @return string
     */
    public static function homeDirectory() {
        switch (PHP_OS) {
            case 'Linux': return getenv('HOME');
            case 'WINNT': return getenv('HOMEDRIVE') . getenv('HOMEPATH');
            default:      throw new PlatformException(PHP_OS, PlatformException::CODE_UNKNOWN_PLATFORM);
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
            default:      throw new PlatformException(PHP_OS, PlatformException::CODE_UNKNOWN_PLATFORM);
        }
    }

    /**
     * Get the path to the php executable.
     *
     * @return string
     */
    public static function phpExecutable() {
        return PHP_BINARY;
    }

    /**
     * Expand placeholders in the supplied path.
     *
     * @param string $path
     *
     * @return string
     */
    public static function expandPath($path) {
        /* Paths beginning with ~ and a directory separator are relative to
         * the user's home directory. */
        if ($path{0} === '~' && $path{1} === static::directorySeparator()) {
            $path = static::homeDirectory() . substr($path, 1);
        }

        return $path;
    }

    /**
     * Get the current working directory.
     *
     * @return string
     */
    public static function workingDirectory() {
        return getcwd();
    }

    /**
     * Get the name of the running script.
     *
     * @return string
     */
    public static function phpScript() {
        return $_SERVER['argv'][0];
    }
}
