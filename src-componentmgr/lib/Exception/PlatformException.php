<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\Exception;

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
     * @override \ComponentManager\Exception\AbstractException
     */
    public function getExceptionType() {
        return 'PlatformException';
    }

    /**
     * @override \ComponentManager\Exception\AbstractException
     */
    public function getExceptionCodeName() {
        switch ($this->code) {
            case static::CODE_UNKNOWN_PLATFORM:
                return 'Unknown platform';

            case static::CODE_MISSING_EXECUTABLE:
                return 'Required executable could not be found on PATH';
        }
    }
}
