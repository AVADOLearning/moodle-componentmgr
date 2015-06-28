<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\Console;

/**
 * Named arguments for reuse across commands.
 */
class Argument {
    /**
     * Option: dry run.
     *
     * @var string
     */
    const OPTION_DRY_RUN = 'dry-run';

    /**
     * Option help: dry run.
     *
     * @var string
     */
    const OPTION_DRY_RUN_HELP = 'Print out operations instead of applying them';
}
