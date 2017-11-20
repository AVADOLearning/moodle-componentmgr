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

/**
 * Linux platform support.
 */
class LinuxPlatform extends AbstractPlatform implements Platform {
    /**
     * Path component delimiter.
     *
     * @var string
     */
    const PATH_DELIMITER = ':';

    /**
     * @inheritdoc Platform
     */
    public function expandPath($path) {
        /* Paths beginning with ~ and a directory separator are relative to
         * the user's home directory. */
        if ($path{0} === '~' && $path{1} === $this->getDirectorySeparator()) {
            $path = $this->getHomeDirectory() . substr($path, 1);
        }

        return $path;
    }

    /**
     * @inheritdoc Platform
     */
    public function getExecutablePath($name) {
        $paths = explode(static::PATH_DELIMITER, getenv('PATH'));

        foreach ($paths as $path) {
            $executable = $this->joinPaths([$path, $name]);
            if (is_executable($executable)) {
                return $executable;
            }
        }

        throw new PlatformException(
                $name, PlatformException::CODE_MISSING_EXECUTABLE);
    }

    /**
     * @inheritdoc Platform
     */
    public function getHomeDirectory() {
        return getenv('HOME');
    }

    /**
     * @inheritdoc Platform
     */
    public function getLocalSharedDirectory() {
        return $this->joinPaths([
            $this->getHomeDirectory(),
            '.local',
            'share',
        ]);
    }
}
