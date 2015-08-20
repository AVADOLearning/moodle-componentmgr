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
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

/**
 * Zip package source.
 */
class ZipPackageSource extends AbstractPackageSource
        implements PackageSource {
    const ARCHIVE_FILENAME_FORMAT = '%s-%s.zip';

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
     * @override \ComponentManager\PackageSource\PackageSource
     */
    public function obtainPackage($tempDirectory,
                                  Component $component,
                                  ComponentVersion $version,
                                  LoggerInterface $logger) {
        $sources = $version->getSources();

        foreach ($sources as $source) {
            if ($source instanceof ZipComponentSource) {
                $archiveFilename = $tempDirectory
                    . PlatformUtil::directorySeparator()
                    . $this->getArchiveFilename($component, $version);

                $logger->debug('Trying zip source', [
                    'archiveFilename' => $archiveFilename,
                    'archiveUri'      => $source->getArchiveUri(),
                    'md5Checksum'     => $source->getMd5Checksum(),
                ]);

                $this->download($source->getArchiveUri(), $archiveFilename);

                $checksum = $source->getMd5Checksum();
                if (!$this->verifyChecksum($archiveFilename, $checksum)) {
                    throw new InstallationFailureException(
                            "{$archiveFilename} didn't match checksum {$checksum}",
                            InstallationFailureException::CODE_INVALID_SOURCE_CHECKSUM);
                }
            } else {
                $logger->debug('Cannot accept component source; skipping', [
                    'componentSource' => $source,
                ]);
            }
        }

        throw new InstallationFailureException(
                'no zip component sources found',
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
}
