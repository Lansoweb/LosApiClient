<?php
namespace LosApiClient\Api;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use LosApiClient\Api\Client;

class ClientFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        $clientConfig = $config['los-api-client'];

        $client = new \Zend\Http\Client($clientConfig['uri'], $clientConfig['http-client']['options']);
        $client->getRequest()->getHeaders()->addHeaders($clientConfig['headers']);

        return new Client($client);
    }
}