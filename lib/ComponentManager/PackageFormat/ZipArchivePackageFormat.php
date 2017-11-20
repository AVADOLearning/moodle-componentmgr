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
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

/**
 * Zip archive package format.
 *
 * Requires that the PHP zip extension be installed for the use of the
 * ZipArchive class, and SPL for the two iterators used to walk the filesystem.
 */
class ZipArchivePackageFormat extends AbstractPackageFormat
        implements PackageFormat {
    /**
     * @inheritdoc PackageFormat
     */
    public function package($moodleDir, $destination,
                            ProjectFile $projectFile,
                            ProjectLockFile $projectLockFile,
                            LoggerInterface $logger) {
        $sep = $this->platform->getDirectorySeparator();

        $archive = new ZipArchive();
        $archive->open($destination, ZipArchive::CREATE);

        $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($moodleDir),
                RecursiveIteratorIterator::SELF_FIRST);
        foreach ($files as $file) {
            // Ignore directory (.) and parent (..) entries
            if (in_array(substr($file, strrpos($file, $sep) + 1), ['.', '..'])) {
                continue;
            }

            if (is_dir($file)) {
                $archiveFile = str_replace("{$moodleDir}{$sep}", '', $file);
                $archive->addEmptyDir($archiveFile);
            } elseif (is_file($file)) {
                $archiveFile = str_replace("{$moodleDir}{$sep}", '', $file);
                $archive->addFromString($archiveFile, file_get_contents($file));
            } else {
                $logger->warning('Skipping item; doesn\'t appear to be a file or directory', [
                    'file' => $file,
                ]);
            }
        }

        $archive->close();
    }
}
