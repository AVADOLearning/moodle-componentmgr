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
 * Git version control.
 */
class GitVersionControl implements VersionControl {
    /**
     * The repository's on-disk location.
     *
     * @var string
     */
    protected $directory;

    /**
     * Remotes.
     *
     * @var \ComponentManager\VersionControl\Git\GitRemote[]
     */
    protected $remotes;

    /**
     * Initialiser.
     *
     * @param string                                           $directory
     * @param \ComponentManager\VersionControl\Git\GitRemote[] $remotes
     */
    public function __construct($directory, $remotes) {
        $this->directory = $directory;
        $this->remotes   = $remotes;
    }

    /**
     * @override ComponentManager\VersionControl\Git\GitVersionControl
     */
    public function checkoutTag($tag) {}

    /**
     * @override ComponentManager\VersionControl\Git\GitVersionControl
     */
    public function cloneRepository() {}
}
