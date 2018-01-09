<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\PackageSource;

use ComponentManager\ComponentSource\DirectoryComponentSource;
use ComponentManager\Exception\InstallationFailureException;
use ComponentManager\ResolvedComponentVersion;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Directory package source.
 */
class DirectoryPackageSource extends AbstractPackageSource
        implements PackageSource {
    /**
     * @inheritdoc PackageSource
     */
    public function getId() {
        return 'Directory';
    }

    /**
     * @inheritdoc PackageSource
     */
    public function getName() {
        return 'Directory';
    }

    /**
     * @inheritdoc PackageSource
     */
    public function obtainPackage($tempDirectory, $timeout,
                                  ResolvedComponentVersion $resolvedComponentVersion,
                                  Filesystem $filesystem,
                                  LoggerInterface $logger) {
        $version = $resolvedComponentVersion->getVersion();
        $sources = $version->getSources();

        foreach ($sources as $source) {
            if ($source instanceof DirectoryComponentSource) {
                return $source->getDirectory();
            } else {
                $logger->debug('Cannot accept component source; skipping', [
                    'componentSource' => $source,
                ]);
            }
        }

        throw new InstallationFailureException(
                'No directory component sources found',
                InstallationFailureException::CODE_SOURCE_UNAVAILABLE);
    }
}
