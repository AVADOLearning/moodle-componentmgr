<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\VersionControl\Git;

/**
 * Git remote.
 */
class GitRemote {
    /**
     * Remote name.
     *
     * @var string
     */
    protected $name;

    /**
     * Remote URL.
     *
     * @var string
     */
    protected $url;

    /**
     * Initialiser.
     *
     * @param string $name
     * @param string $uri
     */
    public function __construct($name, $uri) {
        $this->name = $name;
        $this->uri  = $uri;
    }

    /**
     * Get the remote name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get the remote URI.
     *
     * @return string
     */
    public function getUri() {
        return $this->uri;
    }
}
