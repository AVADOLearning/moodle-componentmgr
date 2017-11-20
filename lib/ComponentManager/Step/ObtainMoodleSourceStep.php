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
use ComponentManager\HttpClient;
use ComponentManager\Task\PackageTask;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * HTTP client.
     *
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * Initialiser.
     *
     * @param HttpClient $httpClient
     * @param string     $archive
     * @param string     $destination
     */
    public function __construct(HttpClient $httpClient, $archive,
                                $destination) {
        $this->httpClient = $httpClient;

        $this->archive     = $archive;
        $this->destination = $destination;
    }

    /**
     * @inheritdoc Step
     *
     * @param PackageTask $task
     */
    public function execute($task, LoggerInterface $logger) {
        $uri = $task->getMoodleVersion()->getDownloadUri();

        $logger->info('Downloading Moodle', [
            'uri'     => $uri,
            'archive' => $this->archive,
        ]);

        $message = $this->httpClient->createRequest(Request::METHOD_GET, $uri);
        $response = $this->httpClient->sendRequest($message);
        file_put_contents($this->archive, $response->getBody());

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
