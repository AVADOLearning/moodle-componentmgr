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
 * Invalid project exception.
 *
 * Raised during evaluation of a project file to indicate an integrity problem.
 */
class InvalidProjectException extends AbstractException {
    /**
     * Code: unknown platform.
     *
     * @var integer
     */
    const CODE_MISSING_PACKAGE_REPOSITORY = 1;

    /**
     * Code: invalid plugin type.
     *
     * @var integer
     */
    const CODE_INVALID_PLUGIN_TYPE = 2;

    /**
     * Code: decode failed.
     *
     * @var integer
     */
    const CODE_DECODE_FAILED = 3;

    /**
     * Code: missing component.
     *
     * @var integer
     */
    const CODE_MISSING_COMPONENT = 4;

    /**
     * Code: missing package repository cache.
     *
     * @var integer
     */
    const CODE_MISSING_PACKAGE_REPOSITORY_CACHE = 5;

    /**
     * Code: invalid Moodle component name.
     *
     * @var integer
     */
    const CODE_VALIDATION_FAILED = 6;

    /**
     * @override AbstractException
     */
    public function getExceptionType() {
        return 'InvalidProjectException';
    }

    /**
     * @override AbstractException
     */
    public function getExceptionCodeName() {
        switch ($this->code) {
            case static::CODE_MISSING_PACKAGE_REPOSITORY:
                return 'Missing package repository';

            case static::CODE_INVALID_PLUGIN_TYPE:
                return 'Invalid plugin type';

            case static::CODE_DECODE_FAILED:
                return 'Failed to decode project file; is it valid?';

            case static::CODE_MISSING_COMPONENT:
                return 'A required component could not be found in the specified package repository';

            case static::CODE_MISSING_PACKAGE_REPOSITORY_CACHE:
                return 'A package repository was missing its cache';

            case static::CODE_VALIDATION_FAILED:
                return 'Validation of the project file contents failed';
        }
    }
}
