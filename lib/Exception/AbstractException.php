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
abstract class AbstractException extends Exception {
    /**
     * Message format string.
     *
     * @var string
     */
    const MESSAGE_FORMAT = '[%s %s] %s';

    /**
     * Export a representation of the exception to string.
     *
     * @return string
     */
    public function __toString() {
        return sprintf(static::MESSAGE_FORMAT, $this->getExceptionType(),
                       $this->code, $this->message);
    }

    /**
     * Get the exception's type.
     *
     * @return string
     */
    abstract public function getExceptionType();
}
