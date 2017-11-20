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
 * Directory package format.
 *
 * Allows copying a packaged instance directly to the target location.
 */
class DirectoryPackageFormat extends AbstractPackageFormat
        implements PackageFormat {
    /**
     * @inheritdoc PackageFormat
     */
    public function package($moodleDir, $destination,
                            ProjectFile $projectFile,
                            ProjectLockFile $projectLockFile,
                            LoggerInterface $logger) {
        $this->filesystem->mirror($moodleDir, $destination);
    }
}
