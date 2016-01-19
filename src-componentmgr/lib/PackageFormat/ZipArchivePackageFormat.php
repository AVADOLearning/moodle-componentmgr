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
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

/**
 * Microsoft Web Deploy package format.
 */
class ZipArchivePackageFormat extends AbstractPackageFormat
        implements PackageFormat {
    /**
     * @override \ComponentManager\PackageFormat\PackageFormat
     */
    public function package($moodleDir, $destination,
                            ProjectFile $projectFile,
                            ProjectLockFile $projectLockFile,
                            LoggerInterface $logger) {
        $archive = new ZipArchive();
        $archive->open($destination, ZipArchive::CREATE);

        $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($moodleDir),
                RecursiveIteratorIterator::SELF_FIRST);
        foreach ($files as $file) {
            // Ignore directory (.) and parent (..) entries
            if (in_array(substr($file, strrpos($file, '/') + 1), ['.', '..'])) {
                continue;
            }

            if (is_dir($file)) {
                $archiveFile = str_replace("{$moodleDir}/", '', "{$file}/");
                $archive->addEmptyDir($archiveFile);
            } elseif (is_file($file)) {
                $archiveFile = str_replace("{$moodleDir}/", '', $file);
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
