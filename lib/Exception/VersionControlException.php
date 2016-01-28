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
 * Version control exception.
 *
 * Raised upon the failure of a VCS command during an installation.
 */
class VersionControlException extends AbstractException {
    /**
     * Code: fetch failed.
     *
     * @var integer
     */
    const CODE_FETCH_FAILED = 1;

    /**
     * Code: checkout failed.
     *
     * @var integer
     */
    const CODE_CHECKOUT_FAILED = 2;

    /**
     * Code: remote add failed.
     *
     * @var integer
     */
    const CODE_REMOTE_ADD_FAILED = 3;

    /**
     * Code: repository initialisation failed.
     *
     * @var integer
     */
    const CODE_INIT_FAILED = 4;

    /**
     * Code: checkout-index failed.
     *
     * @var integer
     */
    const CODE_CHECKOUT_INDEX_FAILED = 5;

    /**
     * Code: rev-parse failed.
     *
     * @var integer
     */
    const CODE_REV_PARSE_FAILED = 6;

    /**
     * @override \ComponentManager\Exception\AbstractException
     */
    public function getExceptionType() {
        return 'VersionControlException';
    }

    /**
     * @override \ComponentManager\Exception\AbstractException
     */
    public function getExceptionCodeName() {
        switch ($this->code) {
            case static::CODE_FETCH_FAILED:
                return 'Failed to fetch the specified remote';

            case static::CODE_CHECKOUT_FAILED:
                return 'Failed to checkout the specified reference';

            case static::CODE_REMOTE_ADD_FAILED:
                return 'Failed to add the specified remote';

            case static::CODE_INIT_FAILED:
                return 'Failed to initialise the Git repository';

            case static::CODE_CHECKOUT_INDEX_FAILED:
                return 'Failed to checkout files in the index to the specified prefix';

            case static::CODE_REV_PARSE_FAILED:
                return 'Failed to locate a commit hash for the supplied reference';
        }
    }
}
