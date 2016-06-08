<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Step;

use ComponentManager\Exception\ComponentProjectException;
use ComponentManager\Moodle;
use ComponentManager\Platform\Platform;
use ComponentManager\Project\ComponentProject;
use ComponentManager\Project\ComponentProjectFile;
use ComponentManager\ResolvedComponentVersion;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Execute component build steps.
 */
class BuildComponentsStep implements Step {
    /**
     * Moodle instance.
     *
     * @var \ComponentManager\Moodle
     */
    protected $moodle;

    /**
     * Filesystem.
     *
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * Platform support library.
     *
     * @var \ComponentManager\Platform\Platform
     */
    protected $platform;

    /**
     * Initialiser.
     *
     * @param \ComponentManager\Moodle                 $moodle
     * @param \ComponentManager\Platform\Platform      $platform
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(Moodle $moodle, Platform $platform,
                                Filesystem $filesystem) {
        $this->moodle = $moodle;

        $this->platform   = $platform;
        $this->filesystem = $filesystem;
    }

    /**
     * @override \ComponentManager\Step\Step
     *
     * @param \ComponentManager\Task\InstallTask|\ComponentManager\Task\PackageTask $task
     */
    public function execute($task, LoggerInterface $logger) {
        $resolvedComponentVersions = $task->getResolvedComponentVersions();

        foreach ($resolvedComponentVersions as $resolvedComponentVersion) {
            $this->tryComponent($resolvedComponentVersion, $logger);
        }
    }

    /**
     * Attempt to build an individual component.
     *
     * @param \ComponentManager\ResolvedComponentVersion $resolvedComponentVersion
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return void
     *
     * @throws \ComponentManager\Exception\ComponentProjectException
     */
    protected function tryComponent(ResolvedComponentVersion $resolvedComponentVersion,
                                    LoggerInterface $logger) {
        $component = $resolvedComponentVersion->getComponent();
        $typeDirectory = $this->moodle->getPluginTypeDirectory(
                $component->getPluginType());

        $targetDirectory = $this->platform->joinPaths([
            $typeDirectory,
            $component->getPluginName(),
        ]);
        $componentProjectFilename = $this->platform->joinPaths([
            $typeDirectory,
            $component->getPluginName(),
            ComponentProjectFile::FILENAME,
        ]);

        $logContext = [
            'component'                => $component->getName(),
            'componentProjectFilename' => $componentProjectFilename,
        ];

        if (!$this->filesystem->exists($componentProjectFilename)) {
            $logger->debug(
                'Component project file not found; skipping build',
                $logContext);
            return;
        }
        $componentProjectFile = new ComponentProjectFile(
            $componentProjectFilename);

        try {
            $buildScript = $componentProjectFile->getScript(
                    ComponentProjectFile::SCRIPT_BUILD);
        } catch (ComponentProjectException $e) {
            $logger->debug(
                    'Component project file doesn\'t contain a build script; skipping build',
                    $logContext);
            return;
        }

        $process = new Process($buildScript, $targetDirectory);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ComponentProjectException(
                    $process->getErrorOutput(),
                    ComponentProjectException::CODE_SCRIPT_FAILED);
        }
    }
}
