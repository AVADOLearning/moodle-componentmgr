<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\PackageSource;

use ComponentManager\Component;
use ComponentManager\ComponentSource\ZipComponentSource;
use ComponentManager\ComponentVersion;
use ComponentManager\Exception\InstallationFailureException;
use ComponentManager\PlatformUtil;
use ComponentManager\ResolvedComponentVersion;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use ZipArchive;

/**
 * Zip package source.
 *
 * Obtains a zip archive from the specified URL and extracts the archive to a
 * temporary directory.
 */
class ZipPackageSource extends AbstractPackageSource
        implements PackageSource {
    /**
     * Archive filename format.
     *
     * @var string
     */
    const ARCHIVE_FILENAME_FORMAT = '%s-%s.zip';

    /**
     * Target directory format.
     *
     * @var string
     */
    const TARGET_DIRECTORY_FORMAT = '%s-%s';

    /**
     * Download the specified file to the specified local filename.
     *
     * @param string $uri
     * @param string $filename
     *
     * @return void
     */
    protected function download($uri, $filename) {
        $client = new Client();
        $client->get($uri, ['sink' => $filename]);
    }

    /**
     * @override \ComponentManager\PackageSource\PackageSource
     */
    public function getId() {
        return 'Zip';
    }

    /**
     * @override \ComponentManager\PackageSource\PackageSource
     */
    public function getName() {
        return 'Zip';
    }

    /**
     * Get archive filename.
     *
     * @param \ComponentManager\Component        $component
     * @param \ComponentManager\ComponentVersion $version
     *
     * @return string
     */
    protected function getArchiveFilename(Component $component,
                                          ComponentVersion $version) {
        return sprintf(static::ARCHIVE_FILENAME_FORMAT, $component->getName(),
                       $version->getVersion());
    }

    /**
     * Get target directory.
     *
     * @param \ComponentManager\Component        $component
     * @param \ComponentManager\ComponentVersion $version
     *
     * @return string
     */
    protected function getTargetDirectory(Component $component,
                                          ComponentVersion $version) {
        return sprintf(static::TARGET_DIRECTORY_FORMAT, $component->getName(),
                       $version->getVersion());
    }

    /**
     * @override \ComponentManager\PackageSource\PackageSource
     */
    public function obtainPackage($tempDirectory,
                                  ResolvedComponentVersion $resolvedComponentVersion,
                                  Filesystem $filesystem,
                                  LoggerInterface $logger) {
        $component = $resolvedComponentVersion->getComponent();
        $version   = $resolvedComponentVersion->getVersion();
        $sources   = $version->getSources();

        $finalVersion = $resolvedComponentVersion->getFinalVersion();
        if ($finalVersion !== null) {
            $source = new ZipComponentSource(
                    $finalVersion->archiveUri, $finalVersion->md5Checksum);
            $logger->info('Installing pinned version', [
                'archiveUri'  => $finalVersion->archiveUri,
                'md5Checksum' => $finalVersion->md5Checksum,
            ]);

            $moduleRootDirectory = $this->trySource($tempDirectory, $logger, $component, $version, $source);
        } else {
            foreach ($sources as $source) {
                if ($source instanceof ZipComponentSource) {
                    $moduleRootDirectory = $this->trySource($tempDirectory, $logger, $component, $version, $source);

                    $resolvedComponentVersion->setFinalVersion((object) [
                        'archiveUri'  => $source->getArchiveUri(),
                        'md5Checksum' => $source->getMd5Checksum(),
                    ]);

                    return $moduleRootDirectory;
                } else {
                    $logger->debug('Cannot accept component source; skipping', [
                        'componentSource' => $source,
                    ]);
                }
            }
        }

        throw new InstallationFailureException(
                'No zip component sources found',
                InstallationFailureException::CODE_SOURCE_UNAVAILABLE);
    }

    /**
     * Verify that the specified file has the specified checksum.
     *
     * @param string $archiveFilename
     * @param string $checksum
     *
     * @return boolean
     */
    protected function verifyChecksum($archiveFilename, $checksum) {
        return strtolower(md5_file($archiveFilename)) === strtolower($checksum);
    }

    /**
     * Try the given source.
     *
     * @param string                                               $tempDirectory
     * @param \Psr\Log\LoggerInterface                             $logger
     * @param \ComponentManager\Component                          $component
     * @param \ComponentManager\ComponentVersion                   $version
     * @param \ComponentManager\ComponentSource\ZipComponentSource $source
     *
     * @return string
     * @throws InstallationFailureException
     */
    protected function trySource($tempDirectory, LoggerInterface $logger,
                                 Component $component, ComponentVersion $version,
                                 ZipComponentSource $source) {
        $archiveFilename = $tempDirectory
                         . PlatformUtil::directorySeparator()
                         . $this->getArchiveFilename($component, $version);
        $targetDirectory = $tempDirectory
                         . PlatformUtil::directorySeparator()
                         . $this->getTargetDirectory($component, $version);

        $logger->debug('Trying zip source', [
            'archiveFilename' => $archiveFilename,
            'archiveUri'      => $source->getArchiveUri(),
            'md5Checksum'     => $source->getMd5Checksum(),
            'targetDirectory' => $targetDirectory,
        ]);

        try {
            $this->download($source->getArchiveUri(), $archiveFilename);
        } catch (GuzzleException $e) {
            throw new InstallationFailureException(
                    $e->getMessage(),
                    InstallationFailureException::CODE_SOURCE_UNAVAILABLE,
                    $e);
        }

        $checksum = $source->getMd5Checksum();
        if (!$this->verifyChecksum($archiveFilename, $checksum)) {
            throw new InstallationFailureException(
                "{$archiveFilename} didn't match checksum {$checksum}",
                InstallationFailureException::CODE_INVALID_SOURCE_CHECKSUM);
        }

        $archive = new ZipArchive();
        $archive->open($archiveFilename);
        if (!$archive->extractTo($targetDirectory)) {
            throw new InstallationFailureException(
                "Unable to extract archive {$archiveFilename} to {$targetDirectory}",
                InstallationFailureException::CODE_EXTRACTION_FAILED);
        }

        $moduleRootDirectory = $targetDirectory
                             . PlatformUtil::directorySeparator()
                             . $component->getPluginName();
        if (!is_dir($moduleRootDirectory)) {
            throw new InstallationFailureException(
                    "Module directory {$moduleRootDirectory} did not exist",
                    InstallationFailureException::CODE_SOURCE_MISSING);
        }
        return $moduleRootDirectory;
    }
}
