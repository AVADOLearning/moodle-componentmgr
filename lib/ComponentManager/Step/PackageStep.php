<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Step;

use ComponentManager\Project\Project;
use Psr\Log\LoggerInterface;

/**
 * Package step.
 *
 * Assembles a Component Manager package in the designated format.
 */
class PackageStep implements Step {
    /**
     * Component Manager project.
     *
     * @var \ComponentManager\Project\Project
     */
    protected $project;

    /**
     * Package format name.
     *
     * @var string
     */
    protected $format;

    /**
     * Moodle source directory.
     *
     * @var string
     */
    protected $source;

    /**
     * Package destination.
     *
     * @var string
     */
    protected $destination;

    /**
     * Initialiser.
     *
     * @param \ComponentManager\Project\Project $project
     * @param string                            $source
     * @param string                            $format
     * @param string                            $destination
     */
    public function __construct(Project $project, $source, $format,
                                $destination) {
        $this->project     = $project;
        $this->format      = $format;
        $this->source      = $source;
        $this->destination = $destination;
    }

    /**
     * @override \ComponentManager\Task\Step
     */
    public function execute($task, LoggerInterface $logger) {
        $packageFormat = $this->project->getPackageFormat($this->format);

        $packageFormat->package(
                $this->source, $this->destination,
                $this->project->getProjectFile(),
                $this->project->getProjectLockFile(), $logger);
    }
}
