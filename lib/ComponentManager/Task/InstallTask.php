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
use ComponentManager\Platform\Platform;
use ComponentManager\Project\Project;
use ComponentManager\ResolvedComponentVersion;
use ComponentManager\Step\BuildComponentsStep;
use ComponentManager\Step\CommitProjectLockFileStep;
use ComponentManager\Step\InstallComponentsStep;
use ComponentManager\Step\RemoveTempDirectoriesStep;
use ComponentManager\Step\ResolveComponentVersionsStep;
use ComponentManager\Step\ValidateProjectStep;
use ComponentManager\Step\VerifyPackageRepositoriesCachedStep;
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
     * @param \ComponentManager\Platform\Platform      $platform
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     * @param \ComponentManager\Moodle                 $moodle
     */
    public function __construct(Project $project, Platform $platform,
                                Filesystem $filesystem, Moodle $moodle) {
        parent::__construct();

        $this->addStep(new ValidateProjectStep($project));
        $this->addStep(new VerifyPackageRepositoriesCachedStep(
                $project->getPackageRepositories()));
        $this->addStep(new ResolveComponentVersionsStep($project));
        $this->addStep(new InstallComponentsStep(
                $project, $moodle, $platform, $filesystem));
        $this->addStep(new BuildComponentsStep(
                $moodle, $platform, $filesystem));
        $this->addStep(new CommitProjectLockFileStep(
                $project->getProjectLockFile()));
        $this->addStep(new RemoveTempDirectoriesStep($platform));
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
