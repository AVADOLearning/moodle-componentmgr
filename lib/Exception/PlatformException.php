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
 * Abstract exception.
 *
 * All exceptions in Component Manager inherit from this base class.
 */
class PlatformException extends AbstractException {
    /**
     * Code: unknown platform.
     *
     * @var integer
     */
    const CODE_UNKNOWN_PLATFORM = 'Unknown platform';

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
        }
    }
}
