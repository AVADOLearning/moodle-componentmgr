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
use Psr\Log\LoggerInterface;

/**
 * Commit the project lock file to the disk.
 */
class CommitProjectLockFileStep implements Step {
    /**
     * Project lock file.
     *
     * @var \ComponentManager\Project\ProjectLockFile
     */
    protected $projectLockFile;

    /**
     * Initialiser.
     *
     * @param \ComponentManager\Project\ProjectLockFile $projectLockFile
     */
    public function __construct(ProjectLockFile $projectLockFile) {
        $this->projectLockFile = $projectLockFile;
    }

    /**
     * @override \ComponentManager\Task\Step
     *
     * @param \ComponentManager\Task\InstallTask $task
     */
    public function execute($task, LoggerInterface $logger) {
        $logger->info('Writing project lock file');

        foreach ($task->getResolvedComponentVersions() as $resolvedComponentVersion) {
            $this->projectLockFile->addResolvedComponentVersion($resolvedComponentVersion);
        }

        $this->projectLockFile->commit();
    }
}

