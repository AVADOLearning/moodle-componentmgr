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

/**
 * Project-aware command trait.
 *
 * Provides helpful utility methods for accessing the project in the currrent
 * working directory. Import this into command implementations to reduce
 * duplication.
 *
 * @property \Symfony\Component\DependencyInjection\ContainerInterface $container
 * @property \Psr\Log\LoggerInterface                                  $logger
 */
trait ComponentAwareCommandTrait {
    /**
     * Component.
     *
     * Lazily loaded -- be sure to call getProject() in order to ensure the
     * value is defined.
     *
     * @var \ComponentManager\Project\ComponentProjectFile
     */
    protected $componentProjectFile;

    /**
     * Get project.
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
