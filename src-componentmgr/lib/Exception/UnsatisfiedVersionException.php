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
 * Unsatisfied version exception.
 *
 * Raised in the event that a version specification cannot be mapped to a
 * version of a component which satisfies it.
 */
class UnsatisfiedVersionException extends AbstractException {
    /**
     * Code: unknown version.
     *
     * @var integer
     */
    const CODE_UNKNOWN_VERSION = 1;

    /**
     * @override \ComponentManager\Exception\AbstractException
     */
    public function getExceptionType() {
        return 'UnsatisfiedVersionException';
    }

    /**
     * @override \ComponentManager\Exception\AbstractException
     */
    public function getExceptionCodeName() {
        switch ($this->code) {
            case static::CODE_UNKNOWN_VERSION:
                return 'Unknown component version';
        }
    }
}
