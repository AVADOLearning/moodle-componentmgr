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
 * Windows platform support.
 */
class WindowsPlatform extends AbstractPlatform implements Platform {
    /**
     * Path component delimiter.
     *
     * @var string
     */
    const PATH_DELIMITER = ';';

    /**
     * @inheritdoc Platform
     */
    public function expandPath($path) {
        return $path;
    }

    /**
     * @inheritdoc Platform
     */
    public function getExecutablePath($name) {
        $paths = explode(static::PATH_DELIMITER, getenv('PATH'));
        $exts  = explode(static::PATH_DELIMITER, getenv('PATHEXT'));

        foreach ($paths as $path) {
            $executable = $this->joinPaths([$path, $name]);

            foreach ($exts as $ext) {
                $qualifiedExecutable = $executable . $ext;
                if (is_executable($qualifiedExecutable)) {
                    return $qualifiedExecutable;
                }
            }
        }

        throw new PlatformException(
                $name, PlatformException::CODE_MISSING_EXECUTABLE);
    }

    /**
     * @inheritdoc Platform
     */
    public function getHomeDirectory() {
        return getenv('HOMEDRIVE') . getenv('HOMEPATH');
    }

    /**
     * @inheritdoc Platform
     */
    public function getLocalSharedDirectory() {
        return getenv('APPDATA');
    }
}
