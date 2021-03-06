<?php

namespace LosApiClient\Api;

use Zend\Http\Client as ZendHttpClient;
use Zend\Http\Response as ZendHttpResponse;
use LosApiClient\Exception\RuntimeException;
use Nocarrier\Hal;
use LosApiClient\Resource\Resource;

final class Response
{
    /**
     * @var Zend\Http\Client
     */
    private $httpClient;

    /**
     * @var Zend\Http\Response
     */
    private $httpResponse;

    /**
     * @var \LosApiClient\Resource\Resource
     */
    private $content;

    /**
     * Construtor.
     *
     * @param Zend\Http\Client   $client
     * @param Zend\Http\Response $response
     */
    public function __construct(ZendHttpClient $client, ZendHttpResponse $response, $depth = 0)
    {
        $this->httpClient = $client;
        $this->httpResponse = $response;

        if (!$this->httpResponse->isSuccess()) {
            $error = json_decode($this->httpResponse->getBody());
            if (empty($error)) {
                $error = new \stdClass();
                $error->status = $this->httpResponse->getStatusCode();
                $error->title = $this->httpResponse->getReasonPhrase();
                $error->detail = '';
            }

            throw new RuntimeException($error->detail, $error->status);
        }

        if (!$this->httpResponse->getHeaders()->has('Content-Type')) {
            throw new RuntimeException("Missing 'Content-Type' header.", 500);
        }

        $contentType = $this->httpResponse->getHeaders()
            ->get('Content-Type')
            ->getFieldValue();

        $pos = strpos($contentType, ';');
        if ($pos !== false) {
            $contentType = substr($contentType, 0, $pos);
        }

        if ($contentType == 'application/hal+json' || $contentType == 'application/json') {
            $this->content = new Resource(Hal::fromJson($this->httpResponse->getBody(), $depth));
        } elseif ($contentType == 'application/hal+xml' || $contentType == 'application/xml') {
            $this->content = new Resource(Hal::fromXml($this->httpResponse->getBody(), $depth));
        } elseif (empty($this->httpResponse->getBody())) {
            $this->content = null;
        } else {
            throw new RuntimeException("Invalid content type during for response: $contentType.", 500);
        }
    }

    /**
     * Get the content.
     *
     * @return \LosApiClient\Resource\Resource
     */
    public function getContent()
    {
        return $this->content;
    }
}
