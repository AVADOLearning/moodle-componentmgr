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

class PlatformFactory {
    /**
     * Filesystem.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Initialiser.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem) {
        $this->filesystem = $filesystem;
    }

    /**
     * Get the platform with the specified name.
     *
     * @param string $platformName The name of the platform (retrieved from
     *                             PHP_OS).
     *
     * @return Platform
     *
     * @throws PlatformException
     */
    public function getPlatform($platformName) {
        switch ($platformName) {
            case 'Darwin':
            case 'FreeBSD':
            case 'Linux':
                return new LinuxPlatform($this->filesystem);
                break;

            case 'WINNT':
            case 'Windows':
                return new WindowsPlatform($this->filesystem);
                break;

            default:
                throw new PlatformException(
                        sprintf('Unsupported platform %s', PHP_OS),
                        PlatformException::CODE_UNKNOWN_PLATFORM);
        }
    }
}
