<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\VersionControl;

/**
 * Version control.
 *
 * The version control API allows Component Manager to source components from a
 * range of version control systems.
 */
interface VersionControl {
    /**
     * Checkout a branch.
     *
     * @param string $branch
     *
     * @return void
     */
    public function checkoutBranch($branch);

    /**
     * Checkout a tag.
     *
     * @param string $tag
     *
     * @return void
     */
    public function checkoutTag($tag);
}
