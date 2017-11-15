<?php

namespace Fotoweb;

use Fotoweb\Middleware\TokenMiddleware;
use Fotoweb\Response\FotowebResult;
use GuzzleHttp\Client;
use GuzzleHttp\Command\CommandInterface;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\HandlerStack;
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
     * @param array $config
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

        $stack = $this->initializeClientHandlerStack($config);

        // Ensure, that a baseUrl was provided.
        if (empty($config['baseUrl'])) {
            throw new \InvalidArgumentException('A baseUrl must be provided.');
        }

        // Create a Guzzle client.
        $client = new Client(['base_uri' => $config['baseUrl'], 'handler' => $stack]);

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
            ['baseUrl' => $config['baseUrl']]
            + (array)json_decode(
                file_get_contents(__DIR__ . '/../service.json'),
                true
            )
        );

        return $description;
    }

    /**
     * Initializes the basic client handler stack.
     *
     * @param array $config
     *   Holds the configuration to initialize the service client.
     *
     * @return \GuzzleHttp\HandlerStack
     */
    private function initializeClientHandlerStack(array $config)
    {
        $stack = HandlerStack::create();

        // Adds the TokenMiddleware.
        $stack->push(new TokenMiddleware($config));

        return $stack;
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

    public function getRendition($href) {
        return $this->getHttpClient()->get($href);
    }
}
