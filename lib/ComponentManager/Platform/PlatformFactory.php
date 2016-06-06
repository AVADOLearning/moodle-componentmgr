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
use Symfony\Component\DependencyInjection\Definition;

class PlatformFactory {
    /**
     * Get the platform with the specified name.
     *
     * @param string $platformName The name of the platform (retrieved from
     *                             PHP_OS).
     *
     * @return \ComponentManager\Platform\Platform
     *
     * @throws \ComponentManager\Exception\PlatformException
     */
    public function getPlatform($platformName) {
        switch ($platformName) {
            case 'Darwin':
            case 'FreeBSD':
            case 'Linux':
                return new LinuxPlatform();
                break;

            case 'WINNT':
            case 'Windows':
                return new WindowsPlatform();
                break;

            default:
                throw new PlatformException(
                        sprintf('Unsupported platform %s', PHP_OS),
                        PlatformException::CODE_UNKNOWN_PLATFORM);
        }
    }
}
