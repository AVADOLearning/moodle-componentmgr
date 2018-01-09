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
use ComponentManager\Exception\RetryablePackageFailureException;
use ComponentManager\Exception\VersionControlException;
use ComponentManager\ResolvedComponentVersion;
use ComponentManager\VersionControl\Git\GitRemote;
use ComponentManager\VersionControl\Git\GitVersionControl;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessTimedOutException;

/**
 * Version control package source.
 */
class GitPackageSource extends AbstractPackageSource
        implements PackageSource {
    /**
     * @inheritdoc PackageSource
     */
    public function getId() {
        return 'Git';
    }

    /**
     * @inheritdoc PackageSource
     */
    public function getName() {
        return 'Git repository';
    }

    /**
     * @inheritdoc PackageSource
     */
    public function obtainPackage($tempDirectory, $timeout,
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

                $repository = new GitVersionControl(
                        $this->platform->getExecutablePath('git'),
                        $repositoryPath, $timeout);
                $repository->init();
                $repository->addRemote(new GitRemote('origin', $repositoryUri));

                $refsFetchOutput = $refsFetchErrors = '';
                $tagsFetchOutput = $tagsFetchErrors = '';
                try {
                    $refsFetchProcess = $repository->fetch();
                    $refsFetchOutput  = $refsFetchProcess->getOutput();
                    $refsFetchErrors  = $refsFetchProcess->getErrorOutput();

                    $tagsFetchProcess = $repository->fetchTags();
                    $refsFetchOutput  = $tagsFetchProcess->getOutput();
                    $refsFetchErrors  = $tagsFetchProcess->getErrorOutput();
                } catch (ProcessTimedOutException $e) {
                    $logger->debug('Fetch stdout', [
                        $refsFetchOutput,
                        $tagsFetchOutput,
                    ]);
                    $logger->debug('Fetch stderr', [
                        $refsFetchErrors,
                        $tagsFetchErrors,
                    ]);
                    $filesystem->remove($paths);
                    throw new RetryablePackageFailureException($e);
                } catch (VersionControlException $e) {
                    $filesystem->remove($paths);
                    throw new RetryablePackageFailureException($e);
                }

                $repository->checkout($installRef);
                $repository->checkoutIndex(
                        $indexPath . $this->platform->getDirectorySeparator());
                $resolvedComponentVersion->setFinalVersion(trim(
                        $repository->parseRevision($installRef)->getOutput()));

                return $indexPath;
            } else {
                $logger->debug('Cannot accept component source; skipping', [
                    'componentSource' => $source,
                ]);
            }
        }
    }
}
