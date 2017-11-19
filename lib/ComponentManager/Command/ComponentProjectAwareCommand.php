<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Command;

use ComponentManager\Platform\Platform;
use ComponentManager\Project\ComponentProjectFile;
use Symfony\Component\Console\Command\Command;

/**
 * Component project-aware command.
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
     * @var ComponentProjectFile
     */
    protected $componentProjectFile;

    /**
     * Platform support library.
     *
     * @var Platform
     */
    protected $platform;

    /**
     * Initialiser.
     *
     * @param Platform $platform
     */
    public function __construct(Platform $platform) {
        $this->platform = $platform;

        parent::__construct();
    }

    /**
     * Get component project file.
     *
     * @return ComponentProjectFile
     */
    protected function getComponentProjectFile() {
        if ($this->componentProjectFile === null) {
            $this->componentProjectFile = new ComponentProjectFile(
                    ComponentProjectFile::FILENAME);
        }

        return $this->componentProjectFile;
    }
}
