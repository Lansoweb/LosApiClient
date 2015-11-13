# Api Client for Restful API

[![Build Status](https://travis-ci.org/lansoweb/los-api-client.svg?branch=master)](https://travis-ci.org/lansoweb/los-api-client)

LosApiClient is a library to consume Restful APIs using Hal (json or xml) like [Apigility](http://apigility.org).

Can be used with pure PHP or any PHP framework, like ZendFramework 2 and Zend-Expressive. 

## Requirements

* PHP >= 5.6
* Zend Http >= 2.4
* Zend ModuleManager >= 2.5
* Zend ServiceManager >= 2.5
* nocarrier/hal >= 0.9

## Installation
### Using composer (recommended)

```bash
php composer.phar require los/los-api-client
```

### Configuration
You need to configure at least the Api URI.

If using a framework that implements `container-interopt`, you can use the following configuration:

Copy the los-api-client.global.php.dist from this module to your application's config folder and make the necessary changes.

For more information about the http-client options, please check the official documentation at
[Zend\Http\Client options](http://framework.zend.com/manual/current/en/modules/zend.http.client.html#configuration).  

```php
'los_api_client' => [
    'uri' => 'https://localhost:8000',
    'depth' => 0,
    'http_client' => [
        'options' => [
            'timeout'       => 60,
            'sslverifypeer' => false,
            'keepalive'     => true,
            'adapter'       => 'Zend\Http\Client\Adapter\Socket',
        ],
    ],
    'headers' => [
        'Accept'       => 'application/hal+json',
        'Content-Type' => 'application/json',
    ],
]
```

## Usage

### Creating the client
You can use the `LosApiClient\Api\ClientFactory` usign the above configuration or manually:
```php
$httpClient = new Zend\Http\Client('http://127.0.0.1', []);
$client = new LosapiClient\Apt\Client($httpClient, 10);
```

## Injecting a Cerberus Circuit Breaker
You can use the client with a circuit breaker to control failures and success and avoid uncessary attempts. 

More information about cerberus on it's [own repository](https://github.com/mt-olympus/cerberus).

```php
$httpClient = new Zend\Http\Client('http://127.0.0.1', []);
$storage = Zend\Cache\StorageFactory\StorageFactory::factory([
            'adapter' => [
                'name' => 'memory',
                'options' => [
                    'namespace' => 'my-service',
                ],
            ],
            'plugins' => [
                'exception_handler' => [
                    'throw_exceptions' => false,
                ],
            ],
        ]);
$cerberus = new Cerberus($storage, 2, 2);

// Create a new client
$client = new LosapiClient\Apt\Client($httpClient, 10, $cerberus, 'my-service);

// Or add it to a previously created client 
$client->setCircuitBreaker($cerberus);
$client->setServiceName('my-service');
```

### Single resource
```php
/* @var \LosApiClient\Api\Client $client */
$client = $this->getServiceLocator()->get('los.api.client');
/* @var \LosApiClient\Resource\Resource $ret */
$ret = $client->get('/album/1');

// $data is an array with all data and resources (_embedded) from the response
$data = $ret->getData();

// $data is an array only with data from the response
$data = $ret->getData(false);
```

### Collection
```php
/* @var \LosApiClient\Api\Client $client */
$client = $this->getServiceLocator()->get('los.api.client');

// Setting depth of _embedded resources to 10
$client->setDepth(10);

/* @var \LosApiClient\Resource\Resource $ret */
$ret = $client->get('/album',['year' => 2015]);

// $data is an array with all data and resources (_embedded) from the response
$data = $ret->getData();

// $data is an array with the first album resource from the response
$data = $ret->getFirstResource('album');

// $data is an array with the all album resources from the response
$data = $ret->getResources('album');

// $data is an array with the all resources from the response
$data = $ret->getResources();
```

### Paginator

This module provides a paginator helper.

```php
/* @var \LosApiClient\Api\Client $client */
$client = $this->getServiceLocator()->get('los.api.client');
/* @var \LosApiClient\Resource\Resource $ret */
$ret = $client->get('/album',['year' => 2015]);

// Returns how many items a page can have
$ret->getPaginator()->getPageSize();

// Returns how many pages the response has
$ret->getPaginator()->getPageCount();

// Returns how many items the response has (across all pages)
$ret->getPaginator()->getTotalItems();

// Returns the current page
$ret->getPaginator()->getPage();
```

You can easily loop through the pages:
```php
/* @var \LosApiClient\Api\Client $client */
$client = $this->getServiceLocator()->get('los.api.client');
$page = 1;
do {
    /* @var \LosApiClient\Resource\Resource $ret */
    $ret = $client->get('/album',[
        'year' => 2015,
        'page' => $page;
    ]);
    $data = $ret->getData();
    $page++;
} while ($ret->getPaginator()->hasMorePages());
```
