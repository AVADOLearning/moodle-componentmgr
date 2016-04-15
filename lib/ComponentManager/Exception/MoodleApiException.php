<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Exception;

class MoodleApiException extends AbstractException {
    /**
     * Code: request failed.
     *
     * @var integer
     */
    const CODE_REQUEST_FAILED = 1;

    /**
     * @override \ComponentManager\Exception\AbstractException
     */
    public function getExceptionType() {
        return 'MoodleApiException';
    }

    /**
     * @override \ComponentManager\Exception\AbstractException
     */
    public function getExceptionCodeName() {
        switch ($this->code) {
            case static::CODE_REQUEST_FAILED:
                return 'A request to the API endpoint failed';
        }
    }
}
