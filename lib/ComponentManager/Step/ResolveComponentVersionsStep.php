<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Step;

use ComponentManager\Exception\InvalidProjectException;
use ComponentManager\Project\Project;
use ComponentManager\ResolvedComponentVersion;
use ComponentManager\Task\InstallTask;
use Psr\Log\LoggerInterface;

/**
 * Resolve component version specifications to available versions.
 */
class ResolveComponentVersionsStep implements Step {
    /**
     * Project.
     *
     * @var Project
     */
    protected $project;

    /**
     * Initialiser.
     *
     * @param Project $project
     */
    public function __construct(Project $project) {
        $this->project = $project;
    }

    /**
     * @override Step
     *
     * @param InstallTask $task
     */
    public function execute($task, LoggerInterface $logger) {
        $componentSpecifications = $this->project->getProjectFile()->getComponentSpecifications();

        foreach ($componentSpecifications as $componentSpecification) {
            $packageRepository = $this->project->getPackageRepository(
                    $componentSpecification->getPackageRepository());

            $logger->info('Resolving component version', [
                'component'         => $componentSpecification->getName(),
                'packageRepository' => $componentSpecification->getPackageRepository(),
                'version'           => $componentSpecification->getVersion(),
            ]);

            $componentName         = $componentSpecification->getName();
            $componentVersion      = $componentSpecification->getVersion();
            $packageRepositoryName = $componentSpecification->getPackageRepository();

            if (!$component = $packageRepository->getComponent($componentSpecification)) {
                throw new InvalidProjectException(
                        "The component \"{$componentName}\" could not be found within repository \"{$packageRepositoryName}\"",
                        InvalidProjectException::CODE_MISSING_COMPONENT);
            }

            /* Note that even at this late stage, we still might not have a final
             * version for the component:
             * -> If the package repository provides us with the Moodle
             *    $plugin->version value, we'll be using it here.
             * -> If the package repository is a version control system, the version
             *    will contain the name of a branch or tag and will need to be
             *    resolved to an individual commit. */
            $version = $component->getVersion($componentVersion);

            $task->addResolvedComponentVersion(new ResolvedComponentVersion(
                    $componentSpecification, $packageRepository, $component,
                    $version));
        }
    }
}
