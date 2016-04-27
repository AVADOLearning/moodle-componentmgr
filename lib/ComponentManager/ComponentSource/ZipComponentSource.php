<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\ComponentSource;

/**
 * Zip archive component source.
 */
class ZipComponentSource extends AbstractComponentSource
        implements ComponentSource {
    /**
     * Zip file URI.
     *
     * @var string
     */
    protected $archiveUri;

    /**
     * MD5 checksum.
     *
     * @var string
     */
    protected $md5Checksum;

    /**
     * Initialiser.
     *
     * @param string $archiveUri
     */
    public function __construct($archiveUri, $md5Checksum) {
        $this->archiveUri  = $archiveUri;
        $this->md5Checksum = $md5Checksum;
    }

    /**
     * @override \ComponentManager\ComponentSource\ComponentSource
     */
    public function getName() {
        return 'Zip archive';
    }

    /**
     * @override \ComponentManager\ComponentSource\ComponentSource
     */
    public function getId() {
        return 'Zip';
    }

    /**
     * Get the zip file URI.
     *
     * @return string
     */
    public function getArchiveUri() {
        return $this->archiveUri;
    }

    /**
     * Get the MD5 checksum.
     *
     * @return string
     */
    public function getMd5Checksum() {
        return $this->md5Checksum;
    }
}
