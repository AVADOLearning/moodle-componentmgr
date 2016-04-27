<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager\Step;

use ComponentManager\Exception\InstallationFailureException;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use ZipArchive;

/**
 * Obtain Moodle source code step.
 *
 * Downloads the specified Moodle version to the specified on-disk location.
 */
class ObtainMoodleSourceStep implements Step {
    /**
     * Moodle source archive path.
     *
     * @var string
     */
    protected $archive;

    /**
     * Target directory.
     *
     * @var string
     */
    protected $destination;

    /**
     * Initialiser.
     *
     * @param \ComponentManager\MoodleVersion $version
     * @param \Psr\Log\LoggerInterface        $logger
     * @param string                          $archive
     * @param string                          $destination
     */
    public function __construct($archive, $destination) {
        $this->archive     = $archive;
        $this->destination = $destination;
    }

    /**
     * @override \ComponentManager\Step\Step
     *
     * @param \ComponentManager\Task\PackageTask $task
     */
    public function execute($task, LoggerInterface $logger) {
        $uri = $task->getMoodleVersion()->getDownloadUri();

        $logger->info('Downloading Moodle', [
            'uri'     => $uri,
            'archive' => $this->archive,
        ]);

        $client = new Client();
        $client->get($uri, [
            'sink' => $this->archive,
        ]);

        $logger->info('Extracting Moodle archive', [
            'archive'     => $this->archive,
            'destination' => $this->destination,
        ]);

        $archive = new ZipArchive();
        if (!$archive->open($this->archive)) {
            throw new InstallationFailureException(
                    "Unable to open archive \"{$this->archive}\"",
                    InstallationFailureException::CODE_EXTRACTION_FAILED);
        }
        $archive->extractTo($this->destination);
        $archive->close();
    }
}
