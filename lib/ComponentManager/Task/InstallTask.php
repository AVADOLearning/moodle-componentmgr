<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Task;

use ComponentManager\Moodle;
use ComponentManager\Project\Project;
use ComponentManager\ResolvedComponentVersion;
use Symfony\Component\Filesystem\Filesystem;

class InstallTask extends AbstractTask implements Task {
    /**
     * Resolved component versions.
     *
     * @var \ComponentManager\ResolvedComponentVersion[]
     */
    protected $resolvedComponentVersions;

    /**
     * Initialiser.
     *
     * @param \ComponentManager\Project\Project        $project
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     * @param \ComponentManager\Moodle                 $moodle
     */
    public function __construct(Project $project, Filesystem $filesystem,
                                Moodle $moodle) {
        parent::__construct();

        $this->addStep(new VerifyPackageRepositoriesCachedStep(
                $project->getPackageRepositories()));
        $this->addStep(new ResolveComponentVersionsStep($project));
        $this->addStep(new InstallComponentsStep(
                $project, $moodle, $filesystem));
        $this->addStep(new CommitProjectLockFileStep(
                $project->getProjectLockFile()));
    }

    /**
     * Add a resolved component version.
     *
     * @param \ComponentManager\ResolvedComponentVersion $resolvedComponentVersion
     */
    public function addResolvedComponentVersion(ResolvedComponentVersion $resolvedComponentVersion) {
        $this->resolvedComponentVersions[] = $resolvedComponentVersion;
    }

    /**
     * Get resolved component versions.
     *
     * @return \ComponentManager\ResolvedComponentVersion[]
     */
    public function getResolvedComponentVersions() {
        return $this->resolvedComponentVersions;
    }
}
