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
class InitCommand implements Command {
    /**
     * @inheritdoc Command
     */
    public function getCommandLine() {
        return ['init'];
    }
}
