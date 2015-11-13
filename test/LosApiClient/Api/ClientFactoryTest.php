<?php
namespace LosApiClient\Api;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Config;

class ClientFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var ClientFactory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ClientFactory();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {}

    /**
     * @covers LosApiClient\Api\ClientFactory::createService
     */
    public function testCreateService()
    {
        $sm = new ServiceManager(new Config([]));
        $sm->setService('config', [
            'los_api_client' => [
                'uri' => 'http://localhost:8000',
                'depth' => 0,
                'headers' => [],
                'http_client' => [
                    'options' => []
                    // 'timeout' => 60,
                    // 'sslverifypeer' => false,
                    // 'keepalive' => true,
                    // 'adapter' => 'Zend\Http\Client\Adapter\Socket',

                ]
            ]
        ]);
        $client = $this->object->createService($sm);
        $this->assertInstanceOf(Client::class, $client);
    }
}
