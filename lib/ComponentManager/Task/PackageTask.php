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
use ComponentManager\MoodleApi;
use ComponentManager\MoodleVersion;
use ComponentManager\Project\Project;
use ComponentManager\Step\BuildComponentsStep;
use ComponentManager\Step\CommitProjectLockFileStep;
use ComponentManager\Step\InstallComponentsStep;
use ComponentManager\Step\ObtainMoodleSourceStep;
use ComponentManager\Step\PackageStep;
use ComponentManager\Step\ResolveComponentVersionsStep;
use ComponentManager\Step\ResolveMoodleVersionStep;
use ComponentManager\Step\VerifyPackageRepositoriesCachedStep;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Package task.
 *
 * Performs all of the operations performed by the installation task, but
 * performs a clean Moodle installation before installing components and
 * finishes builds by packaging up the resulting source into an archive.
 */
class PackageTask extends InstallTask implements Task {
    /**
     * Moodle version.
     *
     * @var \ComponentManager\MoodleVersion
     */
    protected $moodleVersion;

    /**
     * Initialiser.
     *
     * @param \ComponentManager\MoodleApi              $moodleApi
     * @param \ComponentManager\Project\Project        $project
     * @param string                                   $moodleArchive
     * @param string                                   $moodleDestination
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     * @param \ComponentManager\Moodle                 $moodle
     * @param string                                   $packageFormat
     * @param string                                   $packageDestination
     */
    public function __construct(MoodleApi $moodleApi, Project $project,
                                $moodleArchive, $moodleDestination,
                                Filesystem $filesystem, Moodle $moodle,
                                $packageFormat, $packageDestination) {
        /* Because we're reordering the installation steps, we don't want to
         * call InstallTask's constructor. */
        AbstractTask::__construct();

        $this->resolvedComponentVersions = [];

        $this->addStep(new VerifyPackageRepositoriesCachedStep(
                $project->getPackageRepositories()));
        $this->addStep(new ResolveMoodleVersionStep(
                $moodleApi, $project->getProjectFile()->getMoodleVersion()));
        $this->addStep(new ResolveComponentVersionsStep($project));
        $this->addStep(new ObtainMoodleSourceStep(
                $moodleArchive, dirname($moodleDestination)));
        $this->addStep(new InstallComponentsStep(
                $project, $moodle, $filesystem));
        $this->addStep(new BuildComponentsStep($moodle, $filesystem));
        $this->addStep(new CommitProjectLockFileStep(
                $project->getProjectLockFile()));
        $this->addStep(new PackageStep(
                $project, $moodleDestination, $packageFormat,
                $packageDestination));
    }

    /**
     * Get the Moodle version.
     *
     * @return \ComponentManager\MoodleVersion
     */
    public function getMoodleVersion() {
        return $this->moodleVersion;
    }

    /**
     * Set the Moodle version.
     *
     * @param \ComponentManager\MoodleVersion $moodleVersion
     *
     * @return void
     */
    public function setMoodleVersion(MoodleVersion $moodleVersion) {
        $this->moodleVersion = $moodleVersion;
    }
}
