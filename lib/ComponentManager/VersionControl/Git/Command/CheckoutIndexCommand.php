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
 * Initialise a new repository.
 */
class CheckoutIndexCommand implements Command {
    /**
     * Prefix.
     *
     * @var string
     */
    protected $prefix;

    /**
     * Initialiser.
     *
     * @param string $prefix
     */
    public function __construct($prefix) {
        $this->prefix = $prefix;
    }

    /**
     * @override Command
     */
    public function getCommandLine() {
        return [
            'checkout-index',
            '--all',
            sprintf('--prefix=%s', $this->prefix),
        ];
    }

    /**
     * Get the prefix.
     *
     * @return string
     *
     * @codeCoverageIgnore
     */
    public function getPrefix() {
        return $this->prefix;
    }

    /**
     * Set the prefix.
     *
     * @param string $prefix
     *
     * @codeCoverageIgnore
     */
    public function setPrefix($prefix) {
        $this->prefix = $prefix;
    }
}
