<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Helper;

use ComponentManager\Exception\UnsatisfiedVersionException;
use ComponentManager\Moodle;
use ComponentManager\MoodleVersion;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

/**
 * Packaging helper.
 */
class PackageHelper {
    /**
     * Update check endpoint URI.
     *
     * @var string
     */
    const UPDATE_URL = 'http://download.moodle.org/api/1.3/updates.php';

    /**
     * Logger.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Initialiser.
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * Download a Moodle distribution.
     *
     * @param string $uri
     * @param string $filename
     *
     * @return void
     */
    public function downloadMoodle($uri, $filename) {
        $client = new Client();
        $client->get($uri, [
            'sink' => $filename,
        ]);
    }

    /**
     * Get available Moodle versions.
     *
     * @return \ComponentManager\MoodleVersion[]
     */
    public function getMoodleVersions() {
        $client = new Client();
        $response = $client->get(static::UPDATE_URL, [
            'query' => [
                'branch'  => '',
                'version' => '',
            ],
        ]);

        $responseBody = json_decode($response->getBody());
        $result       = [];
        foreach ($responseBody->updates->core as $version) {
            $result[] = new MoodleVersion(
                    $version->version, $version->release, $version->branch,
                    $version->maturity, $version->download);
        }

        return $result;
    }

    /**
     * Resolve the Moodle version.
     *
     * @param string $specification
     *
     * @return \ComponentManager\MoodleVersion
     *
     * @throws \ComponentManager\Exception\UnsatisfiedVersionException
     */
    public function resolveMoodleVersion($specification) {
        $versions = $this->getMoodleVersions();

        $scores = [];
        foreach ($versions as $index => $version) {
            if ($score = $version->satisfies($specification)) {
                $scores[$score] = $version;
            }
        }

        if (!$scores) {
            throw new UnsatisfiedVersionException(
                "Unable to satisfy Moodle version \"{$specification}\"",
                UnsatisfiedVersionException::CODE_UNKNOWN_VERSION);
        }

        ksort($scores);

        $bestMatch = end($scores);
        $this->logger->info('Selected Moodle version', [
            'build'   => $bestMatch->getBuild(),
            'release' => $bestMatch->getRelease(),
            'score'   => key($scores),
        ]);

        return $bestMatch;
    }
}
