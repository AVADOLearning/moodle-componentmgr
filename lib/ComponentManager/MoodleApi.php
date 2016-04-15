<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager;

use ComponentManager\Exception\MoodleApiException;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface to the Moodle.org API.
 * 
 * Provides access to Moodle release and plugin information.
 */
class MoodleApi {
    /**
     * Update check endpoint URI.
     *
     * @var string
     */
    const URL_UPDATES = 'http://download.moodle.org/api/1.3/updates.php';

    /**
     * Perform a GET request to the specified URI with the specified parameters.
     *
     * @param string  $uri
     * @param mixed[] $queryParams
     *
     * @return ResponseInterface
     *
     * @throws MoodleApiException
     */
    protected function get($uri, $queryParams) {
        $client = new Client();

        $response = $client->get($uri, [
            'query' => $queryParams,
        ]);

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new MoodleApiException(
                    "Request failed to \"{$uri}\" failed",
                    MoodleApiException::CODE_REQUEST_FAILED);
        }
        
        return $response;
    }

    /**
     * Get available Moodle versions.
     *
     * @return \ComponentManager\MoodleVersion[]
     *
     * @throws \ComponentManager\Exception\MoodleApiException
     */
    public function getMoodleVersions() {
        $responseBody = json_decode($this->get(static::URL_UPDATES, [
            'branch'  => '',
            'version' => '',
        ])->getBody());

        $result = [];
        foreach ($responseBody->updates->core as $version) {
            $result[] = new MoodleVersion(
                    $version->version, $version->release, $version->branch,
                    $version->maturity, $version->download);
        }
        
        return $result;
    }
}
