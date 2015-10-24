<?php
namespace LosApiClient\Api;

use Zend\Http\Client as ZendHttpClient,
    Zend\Http\Exception\RuntimeException as ZendHttpRuntimeException;

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

    public function __construct(ZendHttpClient $client = null)
    {
        $client = ($client instanceof ZendHttpClient) ? $client : new ZendHttpClient();

        $this->setZendClient($client);
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
     * Get the Zend\Http\Client instance
     *
     * @return Zend\Http\Client
     */
    public function getZendClient()
    {
        return $this->zendClient;
    }

    /**
     * Perform the request to api server
     *
     * @param String $path Example: "/v1/endpoint"
     * @param Array $headers
     */
    private function doRequest($path, $headers = array())
    {
        $this->zendClient->getUri()->setPath($path);

        $this->zendClient->getRequest()->getHeaders()->addHeaders($headers);

        $zendHttpResponse = $this->zendClient->send();

        try {
            $response = new Response($this->zendClient, $zendHttpResponse);
            $content = $response->getContent();
        } catch (ZendHttpRuntimeException $e) {
	    throw $e;
        }

        return $content;
    }

    public function get($path, array $data = array(), array $headers = array())
    {
        $this->zendClient->setMethod('GET')
                         ->setParameterGet($data);

        return $this->doRequest($path, $headers);
    }

    public function post($path, array $data, array $headers = array())
    {
        $this->zendClient->setMethod('POST')
                         ->setRawBody(json_encode($data));

        return $this->doRequest($path, $headers);
    }

    public function put($path, array $data, array $headers = array())
    {
        $this->zendClient->setMethod('PUT')
                         ->setRawBody(json_encode($data));

        return $this->doRequest($path, $headers);
    }

    public function patch($path, array $data, array $headers = array())
    {
        $this->zendClient->setMethod('PATCH')
                         ->setRawBody(json_encode($data));

        return $this->doRequest($path, $headers);
    }

    public function delete($path, array $headers = array())
    {
        $this->zendClient->setMethod('DELETE');

        return $this->doRequest($path, $headers);
    }

}
