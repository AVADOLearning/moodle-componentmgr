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
use ComponentManager\MoodleVersion;
use ComponentManager\Task\PackageTask;
use Psr\Log\LoggerInterface;

/**
 * Resolve a version specification to Moodle release.
 */
class ResolveMoodleVersionStep implements Step {
    /**
     * Moodle.org API client.
     *
     * @var MoodleApi
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
     * @param MoodleApi $api
     * @param string    $specification
     */
    public function __construct(MoodleApi $api, $specification) {
        $this->api           = $api;
        $this->specification = $specification;
    }

    /**
     * @inheritdoc Step
     *
     * @param PackageTask $task
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
        /** @var MoodleVersion $version */
        $version = end($scores);

        $logger->info('Selected Moodle release', [
            'build'   => $version->getBuild(),
            'release' => $version->getRelease(),
            'score'   => key($scores),
        ]);

        $task->setMoodleVersion($version);
    }
}
