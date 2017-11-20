<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\VersionControl\Git\Command;

use ComponentManager\VersionControl\Git\GitRemote;

/**
 * Initialise a new repository.
 */
class RemoteAddCommand implements Command {
    /**
     * Remote.
     *
     * @var GitRemote
     */
    protected $remote;

    /**
     * Initialiser.
     *
     * @param GitRemote $remote
     */
    public function __construct(GitRemote $remote) {
        $this->remote = $remote;
    }

    /**
     * @inheritdoc Command
     */
    public function getCommandLine() {
        return [
            'remote',
            'add',
            $this->remote->getName(),
            $this->remote->getUri(),
        ];
    }

    /**
     * Get the remote.
     *
     * @return GitRemote
     *
     * @codeCoverageIgnore
     */
    public function getRemote() {
        return $this->remote;
    }

    /**
     * Set the remote.
     *
     * @param GitRemote $remote
     *
     * @codeCoverageIgnore
     */
    public function setRemote($remote) {
        $this->remote = $remote;
    }
}
