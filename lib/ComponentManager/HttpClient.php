<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2016 Luke Carrier
 * @license GPL-3.0+
 */

namespace ComponentManager;

use Http\Client\HttpClient as HttplugClient;
use Http\Message\MessageFactory;
use Http\Message\UriFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Slightly sweeter HTTPlug.
 */
class HttpClient {
    /**
     * HTTP client.
     *
     * @var HttplugClient
     */
    protected $client;

    /**
     * HTTP message factory.
     *
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * HTTP URI factory.
     *
     * @var UriFactory
     */
    protected $uriFactory;

    /**
     * HttpClient constructor.
     *
     * @param HttplugClient $client
     * @param MessageFactory $messageFactory
     * @param UriFactory $uriFactory
     */
    public function __construct(HttplugClient $client, MessageFactory $messageFactory, UriFactory $uriFactory) {
        $this->client = $client;
        $this->messageFactory = $messageFactory;
        $this->uriFactory = $uriFactory;
    }

    /**
     * Send request.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    public function sendRequest(RequestInterface $request) {
        return $this->client->sendRequest($request);
    }

    /**
     * Create a request.
     *
     * @param string                               $method
     * @param UriInterface|string                  $uri
     * @param mixed[]                              $headers
     * @param resource|string|StreamInterface|null $body
     * @param string                               $protocolVersion
     *
     * @return RequestInterface
     */
    public function createRequest($method, $uri, $headers=[], $body=null,
                                  $protocolVersion='1.1') {
        return $this->messageFactory->createRequest(
                $method, $uri, $headers, $body, $protocolVersion);
    }

    /**
     * Create a URI.
     *
     * @param URIInterface|string $uri
     *
     * @return UriInterface
     */
    public function createUri($uri) {
        return $this->uriFactory->createUri($uri);
    }
}
