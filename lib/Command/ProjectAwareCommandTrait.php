<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
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
 *
 * @property \Symfony\Component\DependencyInjection\ContainerInterface $container
 * @property \Psr\Log\LoggerInterface                                  $logger
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
     * @var \ComponentManager\Project\Project
     */
    protected $project;

    /**
     * Get the Moodle bridge.
     *
     * @param string|null $moodleDirectory
     *
     * @return \ComponentManager\Moodle
     */
    protected function getMoodle($moodleDirectory=null) {
        if ($this->moodle === null) {
            $moodleDirectory = ($moodleDirectory === null)
                    ? PlatformUtil::workingDirectory() : $moodleDirectory;

            $this->moodle = new Moodle($moodleDirectory);
        }

        return $this->moodle;
    }

    /**
     * Get project.
     *
     * @param string|null $projectFilename
     * @param string|null $projectLockFilename
     *
     * @return \ComponentManager\Project\Project
     */
    protected function getProject($projectFilename=null, $projectLockFilename=null) {
        if ($this->project === null) {
            if ($projectFilename === null) {
                $projectFilename = PlatformUtil::workingDirectory()
                                 . PlatformUtil::directorySeparator()
                                 . 'componentmgr.json';
            } else {
                $projectFilename = PlatformUtil::expandPath($projectFilename);
            }

            if ($projectLockFilename === null) {
                $projectLockFilename = PlatformUtil::workingDirectory()
                                     . PlatformUtil::directorySeparator()
                                     . 'componentmgr.lock.json';
            } else {
                $projectLockFilename = PlatformUtil::expandPath(
                        $projectLockFilename);
            }

            $packageRepositoryFactory = $this->container->get(
                    'package_repository.package_repository_factory');
            $packageSourceFactory = $this->container->get(
                    'package_source.package_source_factory');
            $packageFormatFactory = $this->container->get(
                    'package_format.package_format_factory');

            $this->logger->info('Parsing project file', [
                'filename' => $projectFilename,
            ]);
            $this->project = new Project(
                    new ProjectFile($projectFilename),
                    new ProjectLockFile($projectLockFilename),
                    $packageRepositoryFactory, $packageSourceFactory,
                    $packageFormatFactory);
        }

        return $this->project;
    }
}
