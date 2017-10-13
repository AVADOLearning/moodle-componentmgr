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
 * Git command implementation.
 */
interface Command {
    /**
     * Get the command line arguments.
     *
     * Note that it's not necessary to include the git executable, which will
     * be added by the version control class when building the process class.
     *
     * @return string[]
     */
    public function getCommandLine();
}
