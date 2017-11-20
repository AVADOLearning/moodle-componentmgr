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
 * Not implemented exception.
 *
 * Raised when attempting to call a method which has not yet been implemented.
 */
class NotImplementedException extends AbstractException {
    /**
     * @inheritdoc AbstractException
     */
    public function getExceptionType() {
        return 'NotImplementedException';
    }

    /**
     * @inheritdoc AbstractException
     */
    public function getExceptionCodeName() {
        return 'Functionality not yet implemented';
    }
}
