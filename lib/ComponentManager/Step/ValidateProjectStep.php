<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Step;

use ComponentManager\ComponentSpecification;
use ComponentManager\Exception\InvalidProjectException;
use ComponentManager\PackageRepository\PackageRepository;
use ComponentManager\Project\Project;
use Psr\Log\LoggerInterface;

/**
 * Validate the project file.
 */
class ValidateProjectStep implements Step {
    /**
     * Project to validate.
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
     */
    public function execute($task, LoggerInterface $logger) {
        $componentSpecifications = $this->project->getProjectFile()->getComponentSpecifications();
        $packageRepositories = $this->project->getPackageRepositories();

        $result = true;

        if (!$this->project->getProjectFile()->getMoodleVersion()) {
            $result = false;
            $logger->warn('No moodle.version key; package operations will fail');
        }

        foreach ($componentSpecifications as $componentSpecification) {
            if (!$this->isValidComponentName($componentSpecification)) {
                $result = false;
                $logger->error('An invalid component name was specified', [
                    'componentName' => $componentSpecification->getName(),
                ]);
            }

            if (!$this->isComponentSpecificationComplete($componentSpecification)) {
                $result = false;
                $logger->error('An incomplete component specification was specified', [
                    'componentSpecification' => $componentSpecification,
                ]);
            }

            if (!$this->isValidPackageRepository($componentSpecification, $packageRepositories)) {
                $result = false;
                $logger->error('Component uses undeclared package repository', [
                    'componentName'         => $componentSpecification->getName(),
                    'packageRepositoryName' => $componentSpecification->getPackageRepository(),
                ]);
            }

            if (!$result) {
                throw new InvalidProjectException(
                        'The supplied project file is invalid',
                        InvalidProjectException::CODE_VALIDATION_FAILED);
            }
        }
    }

    /**
     * Does the supplied component have a valid name?
     *
     * @param ComponentSpecification $componentSpecification
     *
     * @return boolean
     */
    protected function isValidComponentName(ComponentSpecification $componentSpecification) {
        $parts = explode('_', $componentSpecification->getName(), 2);
        return count($parts) === 2;
    }

    /**
     * Is the supplied component's package repository known to us?
     *
     * @param ComponentSpecification $componentSpecification
     * @param PackageRepository[]    $packageRepositories
     *
     * @return boolean
     */
    protected function isValidPackageRepository(ComponentSpecification $componentSpecification, $packageRepositories) {
        return array_key_exists(
                $componentSpecification->getPackageRepository(), $packageRepositories);
    }

    /**
     * Is the supplied component's specification complete?
     *
     * @param ComponentSpecification $componentSpecification
     *
     * @return boolean
     */
    protected function isComponentSpecificationComplete(ComponentSpecification $componentSpecification) {
        return !!$componentSpecification->getPackageRepository()
                && !!$componentSpecification->getPackageSource();
    }
}
