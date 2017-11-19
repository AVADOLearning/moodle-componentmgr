<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\PackageFormat;

use ComponentManager\Project\ProjectFile;
use ComponentManager\Project\ProjectLockFile;
use Psr\Log\LoggerInterface;

/**
 * Package format.
 */
interface PackageFormat {
    /**
     * Package a Moodle site.
     *
     * @param string          $moodleDir
     * @param string          $destination
     * @param ProjectFile     $projectFile
     * @param ProjectLockFile $projectLockFile
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function package($moodleDir, $destination,
                            ProjectFile $projectFile,
                            ProjectLockFile $projectLockFile,
                            LoggerInterface $logger);
}
