<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Step;

use ComponentManager\Exception\UnsatisfiedVersionException;
use ComponentManager\MoodleApi;
use Psr\Log\LoggerInterface;

/**
 * Resolve a version specification to Moodle release.
 */
class ResolveMoodleVersionStep implements Step {
    /**
     * Moodle.org API client.
     *
     * @var \ComponentManager\MoodleApi
     */
    protected $api;

    /**
     * Moodle version specification.
     *
     * @var string
     */
    protected $specification;

    /**
     * Initialiser.
     *
     * @param \ComponentManager\MoodleApi $api
     * @param string                      $specification
     */
    public function __construct(MoodleApi $api, $specification) {
        $this->api           = $api;
        $this->specification = $specification;
    }

    /**
     * @override \ComponentManager\Task\Step
     *
     * @param \ComponentManager\Task\PackageTask $task
     */
    public function execute($task, LoggerInterface $logger) {
        $versions = $this->api->getMoodleVersions();

        $logger->info('Resolving Moodle version from specification', [
            'specification'     => $this->specification,
            'availableVersions' => count($versions),
        ]);

        $scores = [];
        foreach ($versions as $index => $version) {
            if ($score = $version->satisfies($this->specification)) {
                $scores[$score] = $version;
            }
        }

        if (!count($scores)) {
            throw new UnsatisfiedVersionException(
                "Unable to satisfy Moodle version \"{$this->specification}\"",
                UnsatisfiedVersionException::CODE_UNKNOWN_VERSION);
        }

        ksort($scores);
        /** @var \ComponentManager\MoodleVersion $version */
        $version = end($scores);

        $logger->info('Selected Moodle release', [
            'build'   => $version->getBuild(),
            'release' => $version->getRelease(),
            'score'   => key($scores),
        ]);

        $task->setMoodleVersion($version);
    }
}

