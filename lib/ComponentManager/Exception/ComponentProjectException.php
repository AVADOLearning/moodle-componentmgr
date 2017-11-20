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
 * Component project exception.
 *
 * Raised when issues are encountered with an individual component's project
 * file.
 */
class ComponentProjectException extends AbstractException {
    /**
     * Code: missing script definition.
     *
     * @var string
     */
    const CODE_MISSING_SCRIPT = 1;

    /**
     * Code: script execution failed.
     *
     * @var string
     */
    const CODE_SCRIPT_FAILED = 2;

    /**
     * @inheritdoc AbstractException
     */
    public function getExceptionType() {
        return 'ComponentProjectException';
    }

    /**
     * @inheritdoc AbstractException
     */
    public function getExceptionCodeName() {
        switch ($this->code) {
            case static::CODE_MISSING_SCRIPT:
                return 'Missing a required script entry';
            case static::CODE_SCRIPT_FAILED:
                return 'Execution of a component script failed';
        }
    }
}
