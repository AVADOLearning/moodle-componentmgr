<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\Command;

use ComponentManager\Moodle;
use ComponentManager\PlatformUtil;
use ComponentManager\Project\Project;
use ComponentManager\Project\ProjectFile;
use ComponentManager\Project\ProjectLockFile;

/**
 * Project-aware command trait.
 *
 * Provides helpful utility methods for accessing the project in the currrent
 * working directory. Import this into command implementations to reduce
 * duplication.
 */
trait ProjectAwareCommandTrait {
    /**
     * Moodle bridge.
     *
     * @var \ComponentManager\Moodle
     */
    protected $moodle;

    /**
     * Project.
     *
     * Lazily loaded -- be sure to call getProject() in order to ensure the
     * value is defined.
     *
     * @var \ComponentManager\Project
     */
    protected $project;

    /**
     * Get the Moodle bridge.
     *
     * @return \ComponentManager\Moodle
     */
    protected function getMoodle() {
        if ($this->moodle === null) {
            $this->moodle = new Moodle(PlatformUtil::workingDirectory());
        }

        return $this->moodle;
    }

    /**
     * Get project.
     *
     * @return \ComponentManager\Project\Project
     */
    protected function getProject() {
        if ($this->project === null) {
            $projectFilename = PlatformUtil::workingDirectory()
                      . PlatformUtil::directorySeparator()
                      . 'componentmgr.json';
            $projectLockFilename = PlatformUtil::workingDirectory()
                                 . PlatformUtil::directorySeparator()
                                 . 'componentmgr.lock.json';

            $packageRepositoryFactory = $this->container->get(
                    'package_repository.package_repository_factory');
            $packageSourceFactory = $this->container->get(
                    'package_source.package_source_factory');

            $this->logger->info('Parsing project file', [
                'filename' => $projectFilename,
            ]);
            $this->project = new Project(new ProjectFile($projectFilename),
                                         new ProjectLockFile($projectLockFilename),
                                         $packageRepositoryFactory,
                                         $packageSourceFactory);
        }

        return $this->project;
    }
}
