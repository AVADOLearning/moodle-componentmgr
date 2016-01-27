<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL-3.0+
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
    const MESSAGE_FORMAT = '[%s %s (%d)] %s';

    /**
     * Export a representation of the exception to string.
     *
     * @return string
     */
    public function __toString() {
        return sprintf(static::MESSAGE_FORMAT, $this->getExceptionType(),
                       $this->getExceptionCodeName(), $this->code,
                       $this->message);
    }

    /**
     * Get the name for the exception's code.
     *
     * @return string
     */
    abstract public function getExceptionCodeName();

    /**
     * Get the exception's type.
     *
     * @return string
     */
    abstract public function getExceptionType();
}
