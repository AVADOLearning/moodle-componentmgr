<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Exception;

use ComponentManager\Exception\AbstractException;

/**
 * Platform exception.
 *
 * Raised during the execution of platform-specific operations to indicate
 * either a failure to perform a task or an unsupported platform.
 */
class PlatformException extends AbstractException {
    /**
     * Code: unknown platform.
     *
     * @var integer
     */
    const CODE_UNKNOWN_PLATFORM = 1;

    /**
     * Code: missing executable.
     *
     * @var integer
     */
    const CODE_MISSING_EXECUTABLE = 2;

    /**
     * Code: unknown temporary directory.
     *
     * @var integer
     */
    const CODE_UNKNOWN_TEMP_DIRECTORY = 3;

    /**
     * @override AbstractException
     */
    public function getExceptionType() {
        return 'PlatformException';
    }

    /**
     * @override AbstractException
     */
    public function getExceptionCodeName() {
        switch ($this->code) {
            case static::CODE_UNKNOWN_PLATFORM:
                return 'Unknown platform';

            case static::CODE_MISSING_EXECUTABLE:
                return 'Required executable could not be found on PATH';

            case static::CODE_UNKNOWN_TEMP_DIRECTORY:
                return 'Attempted to remove an unknown temporary directory';
        }
    }
}
