<?php
return [
    'service_manager' => [
        'aliases' => [
            'los.api.client' => 'LosApiClient\Api\Client'
        ],
        'factories' => [
            LosApiClient\Api\Client::class => LosApiClient\Api\ClientFactory::class
        ]
    ],
    'los_api_client' => [
        'uri' => 'http://localhost:8000',
        'headers' => array(
            'Accept' => 'application/hal+json',
            'Content-Type' => 'application/json'
        ),
        'http_client' => [
            'options' => []
        ]
    ]
];
