<?php

namespace LosApiClient\Api;

use Zend\Http\Client as ZendHttpClient;
use Zend\Http\Exception\RuntimeException as ZendHttpRuntimeException;
use Cerberus\CerberusInterface;
use LosApiClient\Exception\NotAvailableException;
use LosApiClient\Exception\RuntimeException;

final class Client
{
    /**
     * @const int Request timeout
     */
    const TIMEOUT = 60;

    /**
     * @var \Zend\Http\Client Instance
     */
    private $zendClient;

    /**
     * Depth to generate the response from the _embedded resources.
     *
     * @var int
     */
    private $depth;

    private $circuitBreaker;

    private $serviceName;

    public function __construct(
        ZendHttpClient $client = null,
        $depth = 0,
        CerberusInterface $circuitBreaker = null,
        $serviceName = null
    ) {
        $client = ($client instanceof ZendHttpClient) ? $client : new ZendHttpClient();

        $this->setZendClient($client);

        $this->depth = (int) $depth;

        $this->circuitBreaker = $circuitBreaker;
        $this->serviceName = $serviceName;
    }

    public function setZendClient(ZendHttpClient $client)
    {
        $host = $client->getUri()->getHost();

        if (empty($host)) {
            throw new ZendHttpRuntimeException('Undefined Host!');
        }

        $this->zendClient = $client;

        return $this;
    }

    /**
     * Get the Zend\Http\Client instance.
     *
     * @return Zend\Http\Client
     */
    public function getZendClient()
    {
        return $this->zendClient;
    }

    public function getResponse()
    {
        return $this->zendClient->getResponse();
    }

    private function isAvailable()
    {
        if ($this->circuitBreaker === null) {
            return true;
        }

        return $this->circuitBreaker->isAvailable($this->serviceName);
    }

    private function reportFailure()
    {
        if ($this->circuitBreaker === null) {
            return;
        }

        $this->circuitBreaker->reportFailure($this->serviceName);
    }

    private function reportSuccess()
    {
        if ($this->circuitBreaker === null) {
            return;
        }

        $this->circuitBreaker->reportSuccess($this->serviceName);
    }

    /**
     * Perform the request to api server.
     *
     * @param String $path    Example: "/v1/endpoint"
     * @param Array  $headers
     */
    private function doRequest($path, $headers = [])
    {
        if (!$this->isAvailable()) {
            throw new NotAvailableException('Service not available.');
        }

        $this->zendClient->getUri()->setPath($path);

        $this->zendClient->getRequest()->getHeaders()->addHeaders($headers);

        try {
            $zendHttpResponse = $this->zendClient->send();

            $response = new Response($this->zendClient, $zendHttpResponse, $this->depth);
            $this->reportSuccess();
        } catch (RuntimeException $ex) {
            $this->reportFailure();
            throw new RuntimeException($ex->getMessage(), $ex->getCode(), $ex);
        } catch (\Exception $ex) {
            $this->reportFailure();
            throw new RuntimeException($ex->getCode(), $ex);
        }
        $content = $response->getContent();

        return $content;
    }

    public function get($path, array $data = [], array $headers = [])
    {
        $this->zendClient->setMethod('GET')
                         ->setParameterGet($data);

        return $this->doRequest($path, $headers);
    }

    public function post($path, array $data, array $headers = [])
    {
        $this->zendClient->setMethod('POST')
                         ->setRawBody(json_encode($data));

        return $this->doRequest($path, $headers);
    }

    public function put($path, array $data, array $headers = [])
    {
        $this->zendClient->setMethod('PUT')
                         ->setRawBody(json_encode($data));

        return $this->doRequest($path, $headers);
    }

    public function patch($path, array $data, array $headers = [])
    {
        $this->zendClient->setMethod('PATCH')
                         ->setRawBody(json_encode($data));

        return $this->doRequest($path, $headers);
    }

    public function delete($path, array $headers = [])
    {
        $this->zendClient->setMethod('DELETE');

        return $this->doRequest($path, $headers);
    }

    public function getDepth()
    {
        return $this->depth;
    }

    public function setDepth($depth)
    {
        $this->depth = (int) $depth;

        return $this;
    }

    public function getCircuitBreaker()
    {
        return $this->circuitBreaker;
    }

    public function setCircuitBreaker(CerberusInterface $circuitBreaker)
    {
        $this->circuitBreaker = $circuitBreaker;

        return $this;
    }
}
