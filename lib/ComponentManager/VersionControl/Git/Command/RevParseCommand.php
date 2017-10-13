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
 * Parse revision.
 */
class RevParseCommand implements Command {
    /**
     * Reference.
     *
     * @var string
     */
    protected $ref;

    /**
     * Initialiser.
     *
     * @param string $ref
     */
    public function __construct($ref) {
        $this->ref = $ref;
    }

    /**
     * @override Command
     */
    public function getCommandLine() {
        return ['rev-parse', $this->ref];
    }

    /**
     * Get the reference.
     *
     * @return string
     *
     * @codeCoverageIgnore
     */
    public function getRef() {
        return $this->ref;
    }

    /**
     * Set the reference.
     *
     * @param string $ref
     *
     * @codeCoverageIgnore
     */
    public function setRef($ref) {
        $this->ref = $ref;
    }
}
