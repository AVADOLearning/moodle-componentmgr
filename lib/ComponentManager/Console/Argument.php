<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Console;

/**
 * Named arguments for reuse across commands.
 */
class Argument {
    /**
     * Argument: action.
     *
     * @var string
     */
    const ARGUMENT_ACTION = 'action';

    /**
     * Argument help: action.
     *
     * @var string
     */
    const ARGUMENT_ACTION_HELP = 'Specifies the action to perform';

    /**
     * Argument value: list plugin types.
     *
     * @var string
     */
    const ARGUMENT_ACTION_LIST_PLUGIN_TYPES = 'list-plugin-types';

    /**
     * Option: package format.
     *
     * @var string
     */
    const OPTION_PACKAGE_FORMAT = 'package-format';

    /**
     * Option help: package format.
     *
     * @var string
     */
    const OPTION_PACKAGE_FORMAT_HELP = 'Specifies the format of the package to be generated';

    /**
     * Option: project file.
     *
     * @var string
     */
    const OPTION_PROJECT_FILE = 'project-file';

    /**
     * Option help: project lock file.
     *
     * @var string
     */
    const OPTION_PROJECT_FILE_HELP = 'The project file from which to source version information';

    /**
     * Option: attempts.
     *
     * @var string
     */
    const OPTION_ATTEMPTS = 'attempts';

    /**
     * Option help: attempts.
     *
     * @var string
     */
    const OPTION_ATTEMPTS_HELP = 'Number of attempts for an operation before giving up';

    /**
     * Option: process timeout.
     *
     * @var string
     */
    const OPTION_TIMEOUT = 'timeout';

    /**
     * Option help: process timeout.
     *
     * @var string
     */
    const OPTION_TIMEOUT_HELP = 'VCS and build subprocess timeout';

    /**
     * Option: Moodle directory.
     *
     * @var string
     */
    const ARGUMENT_MOODLE_DIR = 'moodle-dir';

    /**
     * Option help: Moodle directory.
     *
     * @var string
     */
    const ARGUMENT_MOODLE_DIR_HELP = 'The directory in which to find the Moodle installation';

    /**
     * Option: destination.
     *
     * @var string
     */
    const OPTION_PACKAGE_DESTINATION = 'package-destination';

    /**
     * Option help: destination.
     *
     * @var string
     */
    const OPTION_PACKAGE_DESTINATION_HELP = 'Destination of the resulting package';

    /**
     * Option: script name.
     *
     * @var string
     */
    const ARGUMENT_SCRIPT = 'script';

    /**
     * Option help: script name.
     *
     * @var string
     */
    const ARGUMENT_SCRIPT_HELP = 'Name of the script to execute';
}
