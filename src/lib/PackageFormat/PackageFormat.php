<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\PackageFormat;

use ComponentManager\Project\ProjectFile;
use ComponentManager\Project\ProjectLockFile;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Package format.
 */
interface PackageFormat {
    /**
     * Package a Moodle site.
     *
     * @param string                                    $moodleDir
     * @param string                                    $destination
     * @param \ComponentManager\Project\ProjectFile     $projectFile
     * @param \ComponentManager\Project\ProjectLockFile $projectLockFile
     * @param \Psr\Log\LoggerInterface                  $logger
     *
     * @return void
     */
    public function package($moodleDir, $destination,
                            ProjectFile $projectFile,
                            ProjectLockFile $projectLockFile,
                            LoggerInterface $logger);
}
