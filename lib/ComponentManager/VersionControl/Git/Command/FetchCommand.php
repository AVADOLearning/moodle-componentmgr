<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\VersionControl\Git\Command;

/**
 * Fetch references from a remote.
 */
class FetchCommand implements Command {
    /**
     * The remote name.
     *
     * @var string|null
     */
    protected $remote;

    /**
     * Include tags?
     *
     * @var boolean|null
     */
    protected $tags;

    /**
     * Initialiser.
     *
     * @param string|null $remote
     */
    public function __construct($remote=null) {
        $this->remote = $remote;
    }

    /**
     * @override Command
     */
    public function getCommandLine() {
        $args = ['fetch'];

        if ($tags = $this->getTagsArgument()) {
            $args[] = $tags;
        }

        if ($this->remote !== null) {
            $args[] = $this->remote;
        }

        return $args;
    }

    /**
     * Get the remote name.
     *
     * @return string|null
     *
     * @codeCoverageIgnore
     */
    public function getRemote() {
        return $this->remote;
    }

    /**
     * Is the fetch with tags?
     *
     * @return boolean|null
     *
     * @codeCoverageIgnore
     */
    public function getTags() {
        return $this->tags;
    }

    /**
     * Get the tags argument.
     *
     * @return string|null
     */
    protected function getTagsArgument() {
        if ($this->tags === null) {
            return null;
        }

        return '--' . ($this->tags ? '' : 'no-') . 'tags';
    }

    /**
     * Set the remote name.
     *
     * @param string $remote
     *
     * @codeCoverageIgnore
     */
    public function setRemote($remote) {
        $this->remote = $remote;
    }

    /**
     * Enable or disable tags.
     *
     * @param boolean|null $tags
     *
     * @codeCoverageIgnore
     */
    public function setTags($tags) {
        $this->tags = $tags;
    }
}
