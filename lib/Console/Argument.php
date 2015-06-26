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
     * Argument: component.
     *
     * @var string
     */
    const ARG_COMPONENT = 'component';

    /**
     * Option: source.
     *
     * @var string
     */
    const OPT_SOURCE = 'source';

    /**
     * Short option: source.
     *
     * @var string
     */
    const OPT_SOURCE_SHORT = 's';

    /**
     * Option: release.
     *
     * @var string
     */
    const OPT_RELEASE = 'release';

    /**
     * Short option: release.
     *
     * @var string
     */
    const OPT_RELEASE_SHORT = 'r';
}
