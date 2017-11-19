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
use ComponentManager\PackageRepository\PackageRepositoryFactory;
use ComponentManager\PackageSource\PackageSourceFactory;
use ComponentManager\Platform\Platform;
use ComponentManager\Project\Project;
use ComponentManager\Project\ProjectFile;
use ComponentManager\Project\ProjectLockFile;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Project-aware command.
 *
 * Provides helpful utility methods for accessing the project in the current
 * working directory. Commands requiring interaction with the Moodle instance
 * being processed should extend this class.
 */
abstract class ProjectAwareCommand extends Command {
    /**
     * Project filename.
     *
     * @var string
     */
    const PROJECT_FILENAME = 'componentmgr.json';

    /**
     * Project lock filename.
     *
     * @var string
     */
    const PROJECT_LOCK_FILENAME = 'componentmgr.lock.json';

    /**
     * Filesystem.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Logger.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Moodle bridge.
     *
     * @var Moodle
     */
    protected $moodle;

    /**
     * Package format factory.
     *
     * @var PackageFormatFactory
     */
    protected $packageFormatFactory;

    /**
     * Package repository factory.
     *
     * @var PackageRepositoryFactory
     */
    protected $packageRepositoryFactory;

    /**
     * Package source factory.
     *
     * @var PackageSourceFactory
     */
    protected $packageSourceFactory;

    /**
     * Project.
     *
     * Lazily loaded -- be sure to call getProject() in order to ensure the
     * value is defined.
     *
     * @var Project
     */
    protected $project;

    /**
     * Platform support library.
     *
     * @var Platform
     */
    protected $platform;

    /**
     * Initialiser.
     *
     * @param PackageRepositoryFactory $packageRepositoryFactory
     * @param PackageSourceFactory     $packageSourceFactory
     * @param PackageFormatFactory     $packageFormatFactory
     * @param Platform                 $platform
     * @param Filesystem               $filesystem
     * @param LoggerInterface          $logger
     */
    public function __construct(PackageRepositoryFactory $packageRepositoryFactory,
                                PackageSourceFactory $packageSourceFactory,
                                PackageFormatFactory $packageFormatFactory,
                                Platform $platform, Filesystem $filesystem,
                                LoggerInterface $logger) {
        $this->packageRepositoryFactory = $packageRepositoryFactory;
        $this->packageSourceFactory     = $packageSourceFactory;
        $this->packageFormatFactory     = $packageFormatFactory;

        $this->filesystem = $filesystem;
        $this->platform   = $platform;

        $this->logger = $logger;

        parent::__construct();
    }

    /**
     * Get the Moodle bridge.
     *
     * @param string|null $moodleDirectory
     *
     * @return Moodle
     */
    protected function getMoodle($moodleDirectory=null) {
        if ($this->moodle === null) {
            $moodleDirectory = ($moodleDirectory === null)
                    ? $this->platform->getWorkingDirectory() : $moodleDirectory;

            $this->moodle = new Moodle($moodleDirectory, $this->platform);
        }

        return $this->moodle;
    }

    /**
     * Get project.
     *
     * @param string|null $projectFilename
     * @param string|null $projectLockFilename
     *
     * @return Project
     */
    protected function getProject($projectFilename=null, $projectLockFilename=null) {
        $workingDirectory = $this->platform->getWorkingDirectory();

        if ($this->project === null) {
            if ($projectFilename === null) {
                $projectFilename = $this->platform->joinPaths([
                    $workingDirectory,
                    static::PROJECT_FILENAME,
                ]);
            } else {
                $projectFilename = $this->platform->expandPath(
                        $projectFilename);
            }

            if ($projectLockFilename === null) {
                $projectLockFilename = $this->platform->joinPaths([
                    $workingDirectory,
                    static::PROJECT_LOCK_FILENAME,
                ]);
            } else {
                $projectLockFilename = $this->platform->expandPath(
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
