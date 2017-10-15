<?php

namespace Fotoweb;

use Fotoweb\Response\FotowebResult;
use GuzzleHttp\Client;
use GuzzleHttp\Command\CommandInterface;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Main Client, that invokes the service description and handles all requests.
 *
 * @package Fotoweb
 */
class FotowebClient extends GuzzleClient
{

    /**
     * FotowebClient constructor.
     *
     * @param array $data
     *   Holds the configuration to initialize the service client.
     */
    public function __construct(array $config = [])
    {
        parent::__construct(
          $this->getClientFromConfig($config),
          $this->getServiceDescriptionFromConfig($config),
          null,
          $this->responseToResultTransformer(),
          null,
          $config
        );
    }

    /**
     * Returns the service client.
     *
     * The service client will be returned based on a injected client object
     * or created with a default configuration.
     *
     * @param array $config
     *   Holds the configuration to initialize the service client.
     *
     * @return \GuzzleHttp\Client
     */
    private function getClientFromConfig(array $config)
    {
        // If a client was provided, return it.
        if (isset($config['client'])) {
            return $config['client'];
        }

        // Ensure, that a apiToken was provided.
        if (empty($config['apiToken'])) {
            throw new \InvalidArgumentException('A apiToken must be provided.');
        }

        // Ensure, that the apiToken is valid.
        self::validateToken($config['apiToken']);

        // Create a Guzzle client based on the default configuration.
        $client = new Client(
          [
            'headers' => [
              'FWAPITOKEN' => $config['apiToken'],
            ],
          ]
        );

        return $client;
    }

    /**
     * Returns the service description.
     *
     * The service description will be returned based on a injected
     * configuration object or created based on the general service description
     * file.
     *
     * @param array $config
     *    Holds the configuration to initialize the service client.
     *
     * @return \GuzzleHttp\Command\Guzzle\Description
     */
    private function getServiceDescriptionFromConfig(array $config)
    {
        // If a description was provided, return it.
        if (isset($config['description'])) {
            return $config['description'];
        }

        // Ensure, that a baseUrl was provided.
        if (empty($config['baseUrl'])) {
            throw new \InvalidArgumentException('A baseUrl must be provided.');
        }

        // Create new description based of the stored JSON definition.
        $description = new Description(
          ['baseUrl' => $config['baseUrl']] + (array) json_decode(file_get_contents(__DIR__ . '/../service.json'), true)
        );

        return $description;
    }

    /**
     * Validates the token used for the API authentication.
     *
     * @param string $token
     *   FWAPIToken for a Full Server-to-server API Authentication.
     *
     * @see https://learn.fotoware.com/02_FotoWeb_8.0/Developing_with_the_FotoWeb_API/01_The_FotoWeb_RESTful_API/03_API_Authentication
     *
     * @return bool
     *   True if the provided token is valid.
     */
    private static function validateToken($token)
    {
        if (!is_string($token)) {
            throw new \InvalidArgumentException('The provided token is not a string.');
        }
        if (strlen($token) < 4) {
            throw new \InvalidArgumentException('The provided token must be longer than 3 characters.');
        }
        return true;
    }

    /**
     * Negotiates the response model for an arbitrary API request.
     *
     * @return \Closure
     */
    private function responseToResultTransformer()
    {
        return function (ResponseInterface $response, RequestInterface $request, CommandInterface $command) {
            $commandName = $command->getName();
            $model = self::getDescription()->getOperation($commandName)->getResponseModel();
            $data = \GuzzleHttp\json_decode($response->getBody(), true);
            parse_str($request->getBody(), $data['_request']);

            // Use a specific response model class, if available.
            if (class_exists($model)) {
                $responseModel = new $model($data);

                return $responseModel;
            }

            // Or build a common FotowebResult object based on the response data.
            return new FotowebResult($data);
        };
    }
}
