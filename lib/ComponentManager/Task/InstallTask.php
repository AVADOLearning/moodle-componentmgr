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
     * @var ResolvedComponentVersion[]
     */
    protected $resolvedComponentVersions;

    /**
     * Initialiser.
     *
     * @param Project    $project
     * @param Platform   $platform
     * @param Filesystem $filesystem
     * @param Moodle     $moodle
     * @param integer    $attempts
     */
    public function __construct(Project $project, Platform $platform,
                                Filesystem $filesystem, Moodle $moodle,
                                $attempts) {
        parent::__construct();

        $this->addStep(new ValidateProjectStep($project));
        $this->addStep(new VerifyPackageRepositoriesCachedStep(
                $project->getPackageRepositories()));
        $this->addStep(new ResolveComponentVersionsStep($project));
        $this->addStep(new InstallComponentsStep(
                $project, $moodle, $platform, $filesystem, $attempts));
        $this->addStep(new BuildComponentsStep(
                $moodle, $platform, $filesystem));
        $this->addStep(new CommitProjectLockFileStep(
                $project->getProjectLockFile()));
        $this->addStep(new RemoveTempDirectoriesStep($platform));
    }

    /**
     * Add a resolved component version.
     *
     * @param ResolvedComponentVersion $resolvedComponentVersion
     */
    public function addResolvedComponentVersion(ResolvedComponentVersion $resolvedComponentVersion) {
        $this->resolvedComponentVersions[] = $resolvedComponentVersion;
    }

    /**
     * Get resolved component versions.
     *
     * @return ResolvedComponentVersion[]
     */
    public function getResolvedComponentVersions() {
        return $this->resolvedComponentVersions;
    }
}
