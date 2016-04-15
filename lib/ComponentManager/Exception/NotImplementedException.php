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
 * Not implemented exception.
 *
 * Raised when attempting to call a method which has not yet been implemented.
 */
class NotImplementedException extends AbstractException {
    /**
     * @override \ComponentManager\Exception\AbstractException
     */
    public function getExceptionType() {
        return 'NotImplementedException';
    }

    /**
     * @override \ComponentManager\Exception\AbstractException
     */
    public function getExceptionCodeName() {
        return 'Functionality not yet implemented';
    }
}
