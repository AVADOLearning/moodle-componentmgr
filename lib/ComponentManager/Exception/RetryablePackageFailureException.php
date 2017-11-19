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
use Exception;
use Throwable;

/**
 * Retryable failure occurred during obtaining a package.
 */
class RetryablePackageFailureException extends AbstractException {
    /**
     * @override Exception
     */
    public function __construct(Throwable $previous) {
        parent::__construct(
                $previous->getMessage(), $previous->getCode(), $previous);
    }

    /**
     * @override AbstractException
     */
    public function getExceptionType() {
        return $this->getPrevious()->getExceptionType();
    }

    /**
     * @override AbstractException
     */
    public function getExceptionCodeName() {
        return $this->getPrevious()->getExceptionCodeName();
    }
}
