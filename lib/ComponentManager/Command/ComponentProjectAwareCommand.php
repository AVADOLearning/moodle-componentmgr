<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Command;

use ComponentManager\Component;
use ComponentManager\Moodle;
use ComponentManager\PlatformUtil;
use ComponentManager\Project\ComponentProjectFile;
use ComponentManager\Project\Project;
use ComponentManager\Project\ProjectFile;
use ComponentManager\Project\ProjectLockFile;
use Symfony\Component\Console\Command\Command;

/**
 * Project-aware command trait.
 *
 * Provides helpful utility methods for accessing the project in the currrent
 * working directory. Import this into command implementations to reduce
 * duplication.
 */
abstract class ComponentProjectAwareCommand extends Command {
    /**
     * Component project file.
     *
     * Lazily loaded -- be sure to call getProject() in order to ensure the
     * value is defined.
     *
     * @var \ComponentManager\Project\ComponentProjectFile
     */
    protected $componentProjectFile;

    /**
     * Get component project file.
     *
     * @return \ComponentManager\Project\ComponentProjectFile
     */
    protected function getComponentProjectFile() {
        if ($this->componentProjectFile === null) {
            $this->componentProjectFile = new ComponentProjectFile(
                    ComponentProjectFile::FILENAME);
        }

        return $this->componentProjectFile;
    }
}
