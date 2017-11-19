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
     * Code: package source failed.
     *
     * @var integer
     */
    const CODE_PACKAGE_SOURCE_FAILED = 2;

    /**
     * @override AbstractException
     */
    public function getExceptionType() {
        return 'UnsatisfiedVersionException';
    }

    /**
     * @override AbstractException
     */
    public function getExceptionCodeName() {
        switch ($this->code) {
            case static::CODE_UNKNOWN_VERSION:
                return 'Unknown component version';

            case static::CODE_PACKAGE_SOURCE_FAILED:
                return 'Package source failed to obtain package for component';
        }
    }
}
