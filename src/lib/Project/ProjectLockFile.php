<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\Project;

/**
 * Project lock file.
 *
 * The lock file pins components at specific package versions to allow for
 * reproducible project builds.
 */
class ProjectLockFile extends JsonFile {
}
