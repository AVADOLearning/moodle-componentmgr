<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\Exception;

use Exception;

/**
 * Abstract exception.
 *
 * All exceptions in Component Manager inherit from this base class.
 */
class InvalidProjectException extends AbstractException {
    /**
     * Code: unknown platform.
     *
     * @var integer
     */
    const CODE_MISSING_PACKAGE_REPOSITORY = 1;

    /**
     * @override \ComponentManager\Exception\AbstractException
     */
    public function getExceptionType() {
        return 'InvalidProjectException';
    }

    /**
     * @override \ComponentManager\Exception\AbstractException
     */
    public function getExceptionCodeName() {
        switch ($this->code) {
            case static::CODE_MISSING_PACKAGE_REPOSITORY:
                return 'Missing package repository';
        }
    }
}
