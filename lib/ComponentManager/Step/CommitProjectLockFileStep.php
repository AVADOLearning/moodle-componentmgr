<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Step;

use ComponentManager\Project\ProjectLockFile;
use ComponentManager\Task\InstallTask;
use Psr\Log\LoggerInterface;

/**
 * Commit the project lock file to the disk.
 */
class CommitProjectLockFileStep implements Step {
    /**
     * Project lock file.
     *
     * @var ProjectLockFile
     */
    protected $projectLockFile;

    /**
     * Initialiser.
     *
     * @param ProjectLockFile $projectLockFile
     */
    public function __construct(ProjectLockFile $projectLockFile) {
        $this->projectLockFile = $projectLockFile;
    }

    /**
     * @inheritdoc Step
     *
     * @param InstallTask $task
     */
    public function execute($task, LoggerInterface $logger) {
        $logger->info('Writing project lock file');

        foreach ($task->getResolvedComponentVersions() as $resolvedComponentVersion) {
            $this->projectLockFile->addResolvedComponentVersion($resolvedComponentVersion);
        }

        $this->projectLockFile->commit();
    }
}
