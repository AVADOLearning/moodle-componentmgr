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
use ComponentManager\ComponentSource\GitComponentSource;
use ComponentManager\ComponentVersion;
use ComponentManager\Exception\VersionControlException;
use ComponentManager\PlatformUtil;
use ComponentManager\VersionControl\Git\GitRemote;
use ComponentManager\VersionControl\Git\GitVersionControl;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Version control package source.
 */
class GitPackageSource extends AbstractPackageSource
        implements PackageSource {
    /**
     * @override \ComponentManager\PackageSource\PackageSource
     */
    public function getId() {
        return 'Git';
    }

    /**
     * @override \ComponentManager\PackageSource\PackageSource
     */
    public function getName() {
        return 'Git repository';
    }

    /**
     * @override \ComponentManager\PackageSource\PackageSource
     */
    public function obtainPackage($tempDirectory,
                                  Component $component,
                                  ComponentVersion $version,
                                  Filesystem $filesystem,
                                  LoggerInterface $logger) {
        $sources = $version->getSources();

        foreach ($sources as $source) {
            if ($source instanceof GitComponentSource) {
                $repositoryPath = $tempDirectory
                                . PlatformUtil::directorySeparator() . 'repo';
                $indexPath      = $tempDirectory
                                . PlatformUtil::directorySeparator() . 'index';
                $repositoryUri  = $source->getRepositoryUri();
                $ref            = $source->getRef();

                // These paths must be removed in the event of a failure/retry
                $paths = [
                    $repositoryPath,
                    $indexPath,
                ];
                $filesystem->mkdir($paths);

                $logger->debug('Trying git repository source', [
                    'repositoryPath' => $repositoryPath,
                    'repositoryUri'  => $repositoryUri,
                    'ref'            => $ref,
                    'indexPath'      => $indexPath,
                ]);

                try {
                    $repository = new GitVersionControl($repositoryPath);
                    $repository->init();
                    $repository->addRemote(new GitRemote('origin', $repositoryUri));
                    $repository->fetch('origin');
                    $repository->checkout($ref);
                    $repository->checkoutIndex(
                            $indexPath . PlatformUtil::directorySeparator());

                    return $indexPath;
                } catch (VersionControlException $e) {
                    $logger->debug('Version control failed; skipping', [
                        'code'    => $e->getCode(),
                        'message' => $e->getMessage(),
                    ]);

                    $filesystem->remove($paths);
                }
            } else {
                $logger->debug('Cannot accept component source; skipping', [
                    'componentSource' => $source,
                ]);
            }
        }
    }
}
