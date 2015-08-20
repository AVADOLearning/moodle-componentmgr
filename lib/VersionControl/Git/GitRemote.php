<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
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
     * @param string $url
     */
    public function __construct($name, $url) {
        $this->name = $name;
        $this->url  = $url;
    }
}
