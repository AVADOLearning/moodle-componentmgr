<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\PackageSource;

use ComponentManager\ComponentSource\GitComponentSource;
use ComponentManager\Exception\VersionControlException;
use ComponentManager\ResolvedComponentVersion;
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
                                  ResolvedComponentVersion $resolvedComponentVersion,
                                  Filesystem $filesystem,
                                  LoggerInterface $logger) {
        $componentVersion = $resolvedComponentVersion->getVersion();

        $sources = $componentVersion->getSources();
        foreach ($sources as $source) {
            if ($source instanceof GitComponentSource) {
                $repositoryPath = $this->platform->joinPaths([
                    $tempDirectory,
                    'repo',
                ]);
                $indexPath      = $this->platform->joinPaths([
                    $tempDirectory,
                    'index',
                ]);
                $repositoryUri  = $source->getRepositoryUri();

                $finalRef = $resolvedComponentVersion->getFinalVersion();
                $ref      = $source->getRef();
                if ($finalRef !== null) {
                    $logger->info('Installing pinned version', [
                        'ref'      => $ref,
                        'finalRef' => $finalRef,
                    ]);

                    $installRef = $finalRef;
                } else {
                    $installRef = $ref;
                }

                // These paths must be removed in the event of a failure/retry
                $paths = [
                    $repositoryPath,
                    $indexPath,
                ];
                $filesystem->mkdir($paths);

                $logger->debug('Trying git repository source', [
                    'repositoryPath' => $repositoryPath,
                    'repositoryUri'  => $repositoryUri,
                    'ref'            => $installRef,
                    'indexPath'      => $indexPath,
                ]);

                try {
                    $repository = new GitVersionControl(
                            $this->platform->getExecutablePath('git'),
                            $repositoryPath);
                    $repository->init();
                    $repository->addRemote(new GitRemote('origin', $repositoryUri));
                    $repository->fetch('origin');
                    $repository->checkout($installRef);
                    $repository->checkoutIndex(
                            $indexPath . $this->platform->getDirectorySeparator());
                    $resolvedComponentVersion->setFinalVersion(
                            $repository->parseRevision($installRef));

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
