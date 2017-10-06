<?php

namespace Fotoweb;

use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;

class FotowebClient extends GuzzleClient
{

    public static function create($configuration = [])
    {
        // Load the service description file.
        $service_description = new Description(
          ['baseUrl' => $configuration['baseUrl']] + (array) json_decode(file_get_contents(__DIR__ . '/../service.json'), true)
        );

        // Creates the client and sets the default request headers.
        $client = new Client(
          [
            'headers' => [
              'FWAPITOKEN' => $configuration['apiToken'],
            ],
          ]
        );

        return new static($client, $service_description, null, null, null, $configuration);
    }

}