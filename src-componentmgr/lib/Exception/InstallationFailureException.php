<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Exception;

/**
 * Installation failure exception.
 *
 * Thrown when errors occur during the installation of a package. Must be
 * caught in order to ensure that cleanup operations take place.
 */
class InstallationFailureException extends AbstractException {
    /**
     * Code: unable to obtain source.
     *
     * @var integer
     */
    const CODE_SOURCE_UNAVAILABLE = 1;

    /**
     * Code: invalid source checksum.
     *
     * @var integer
     */
    const CODE_INVALID_SOURCE_CHECKSUM = 2;

    /**
     * Code: extraction of a source file failed.
     *
     * @var integer
     */
    const CODE_EXTRACTION_FAILED = 3;

    /**
     * Code: missing source root.
     *
     * @var integer
     */
    const CODE_SOURCE_MISSING = 4;

    /**
     * @override \ComponentManager\Exception\AbstractException
     */
    public function getExceptionType() {
        return 'InstallationFailureException';
    }

    /**
     * @override \ComponentManager\Exception\AbstractException
     */
    public function getExceptionCodeName() {
        switch ($this->code) {
            case static::CODE_SOURCE_UNAVAILABLE:
                return 'Unable to obtain package source';

            case static::CODE_INVALID_SOURCE_CHECKSUM:
                return 'Unable to verify source file checksum';

            case static::CODE_EXTRACTION_FAILED:
                return 'Unable to extract an archive file';

            case static::CODE_SOURCE_MISSING:
                return 'Unable to locate module root directory in source';
        }
    }
}
