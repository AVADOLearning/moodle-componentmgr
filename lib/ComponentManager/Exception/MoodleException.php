<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Exception;

/**
 * Moodle exception.
 *
 * Raised during execution of a command within the Component Manager Moodle
 * plugin.
 */
class MoodleException extends AbstractException {
    /**
     * Code: execution failed.
     *
     * @var integer
     */
    const CODE_EXECUTION_FAILED = 1;

    /**
     * Code: invalid action.
     *
     * @var integer
     */
    const CODE_INVALID_ACTION = 2;

    /**
     * Code: Moodle instance is not configured.
     *
     * @var integer
     */
    const CODE_NOT_CONFIGURED = 3;

    /**
     * @override \ComponentManager\Exception\AbstractException
     */
    public function getExceptionType() {
        return 'MoodleException';
    }

    /**
     * @override \ComponentManager\Exception\AbstractException
     */
    public function getExceptionCodeName() {
        switch ($this->code) {
            case static::CODE_EXECUTION_FAILED:
                return 'Unable to execute a CLI script';
            case static::CODE_INVALID_ACTION:
                return 'Invalid action specified';
            case static::CODE_NOT_CONFIGURED:
                return 'The Moodle instance\'s configuration file did not define $CFG';
        }
    }
}
