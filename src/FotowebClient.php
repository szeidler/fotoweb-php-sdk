<?php

namespace Fotoweb;

use Fotoweb\Response\FotowebResult;
use GuzzleHttp\Client;
use GuzzleHttp\Command\CommandInterface;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FotowebClient extends GuzzleClient
{


    /**
     * FotowebClient constructor.
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

        $client = new Client(
          [
            'headers' => [
              'FWAPITOKEN' => $config['apiToken'],
            ],
          ]
        );

        return $client;
    }

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

    private function responseToResultTransformer()
    {
        return function (ResponseInterface $response, RequestInterface $request, CommandInterface $command) {
            $commandName = $command->getName();
            $model = self::getDescription()->getOperation($commandName)->getResponseModel();
            $data = \GuzzleHttp\json_decode($response->getBody(), true);
            parse_str($request->getBody(), $data['_request']);

            if (class_exists($model)) {
                $responseModel = new $model($data);

                return $responseModel;
            }

            return new FotowebResult($data);
        };
    }

}