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
use ComponentManager\PackageFormat\PackageFormatFactory;
use ComponentManager\PackageRepository\PackageRepository;
use ComponentManager\PackageRepository\PackageRepositoryFactory;
use ComponentManager\PackageSource\PackageSourceFactory;
use ComponentManager\PlatformUtil;
use ComponentManager\Project\Project;
use ComponentManager\Project\ProjectFile;
use ComponentManager\Project\ProjectLockFile;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Project-aware command trait.
 *
 * Provides helpful utility methods for accessing the project in the currrent
 * working directory. Import this into command implementations to reduce
 * duplication.
 */
abstract class ProjectAwareCommand extends Command {
    /**
     * Logger.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Moodle bridge.
     *
     * @var \ComponentManager\Moodle
     */
    protected $moodle;

    /**
     * Package format factory.
     *
     * @var \ComponentManager\PackageFormat\PackageFormatFactory
     */
    protected $packageFormatFactory;

    /**
     * Package repository factory.
     *
     * @var \ComponentManager\PackageRepository\PackageRepositoryFactory
     */
    protected $packageRepositoryFactory;

    /**
     * Package source factory.
     *
     * @var \ComponentManager\PackageSource\PackageSourceFactory
     */
    protected $packageSourceFactory;

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
     * Initialiser.
     *
     * @param \ComponentManager\PackageFormat\PackageFormatFactory         $packageFormatFactory
     * @param \ComponentManager\PackageRepository\PackageRepositoryFactory $packageRepositoryFactory
     * @param \ComponentManager\PackageSource\PackageSourceFactory         $packageSourceFactory
     * @param \Psr\Log\LoggerInterface                                     $logger
     */
    public function __construct(PackageRepositoryFactory $packageRepositoryFactory,
                                PackageSourceFactory $packageSourceFactory,
                                PackageFormatFactory $packageFormatFactory,
                                LoggerInterface $logger) {
        $this->packageRepositoryFactory = $packageRepositoryFactory;
        $this->packageSourceFactory     = $packageSourceFactory;
        $this->packageFormatFactory     = $packageFormatFactory;

        $this->logger = $logger;

        parent::__construct();
    }

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

            $this->logger->info('Parsing project file', [
                'filename' => $projectFilename,
            ]);
            $this->project = new Project(
                    new ProjectFile($projectFilename),
                    new ProjectLockFile($projectLockFilename),
                    $this->packageRepositoryFactory,
                    $this->packageSourceFactory,
                    $this->packageFormatFactory);
        }

        return $this->project;
    }
}
