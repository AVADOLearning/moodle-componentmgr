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
     * @var integer
     */
    const CODE_OTHER = 1;

    /**
     * @inheritdoc AbstractException
     */
    public function getExceptionType() {
        return 'PackageFailureException';
    }

    /**
     * @inheritdoc AbstractException
     */
    public function getExceptionCodeName() {
        switch ($this->code) {
            case static::CODE_OTHER:
                return 'Something happened';
        }
    }
}
