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
        }
    }
}
