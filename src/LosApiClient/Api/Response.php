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
     *
     * @var Zend\Http\Client
     */
    private $httpClient;

    /**
     *
     * @var Zend\Http\Response
     */
    private $httpResponse;

    /**
     *
     * @var \LosApiClient\Resource\Resource
     */
    private $content;

    /**
     * Construtor
     *
     * @param Zend\Http\Client $client
     * @param Zend\Http\Response $response
     */
    public function __construct(ZendHttpClient $client, ZendHttpResponse $response)
    {
        $this->httpClient = $client;
        $this->httpResponse = $response;

        if (! $this->httpResponse->isSuccess()) {
            $error = json_decode($this->httpResponse->getBody());

            throw new RuntimeException(sprintf('Error "%s/%s": %s', $error->status, $error->title, $error->detail));
        }

        $contentType = $this->httpResponse->getHeaders()
            ->get('Content-Type')
            ->getFieldValue();

        if ($contentType == 'application/hal+json') {
            $this->content = new Resource(Hal::fromJson($this->httpResponse->getBody(),100));
        } elseif ($contentType == 'application/hal+xml') {
            $this->content = new Resource(Hal::fromXml($this->httpResponse->getBody(),100));
        } else {
            throw new RuntimeException("Invalid content type during for response: $contentType.");
        }
    }

    /**
     * Get the content
     *
     * @return \LosApiClient\Resource\Resource
     */
    public function getContent()
    {
        return $this->content;
    }

}
