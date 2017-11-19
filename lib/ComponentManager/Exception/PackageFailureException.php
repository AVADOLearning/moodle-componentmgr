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

class PackageFailureException extends AbstractException {
    /**
     * Generic packaging failure.
     *
     * @todo We probably ought to try and categorise the different types of
     *       failure more specifically in the near future.
     *
     * @var integer
     */
    const CODE_OTHER = 1;

    /**
     * @override AbstractException
     */
    public function getExceptionType() {
        return 'PackageFailureException';
    }

    /**
     * @override AbstractException
     */
    public function getExceptionCodeName() {
        switch ($this->code) {
            case static::CODE_OTHER:
                return 'Something happened';
        }
    }
}
