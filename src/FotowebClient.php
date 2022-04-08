<?php

namespace Fotoweb;

use Fotoweb\Middleware\TokenMiddleware;
use Fotoweb\OAuth2\GrantType\AuthorizationCodeWithPkce;
use Fotoweb\Response\FotowebResult;
use GuzzleHttp\Client;
use GuzzleHttp\Command\CommandInterface;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\HandlerStack;
use kamermans\OAuth2\GrantType\AuthorizationCode;
use kamermans\OAuth2\GrantType\ClientCredentials;
use kamermans\OAuth2\GrantType\RefreshToken;
use kamermans\OAuth2\OAuth2Middleware;
use kamermans\OAuth2\Persistence\TokenPersistenceInterface;
use kamermans\OAuth2\Signer\AccessToken\QueryString;
use kamermans\OAuth2\Signer\ClientCredentials\PostFormData;
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
   * The authentication middleware, if there is any.
   *
   * @var object
   */
  protected $authenticationMiddleware;

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

        // Initialize client config.
        $client_config = ['base_uri' => $config['baseUrl'], 'handler' => $stack];

        // Apply provided client configuration, if available.
        if (isset($config['client_config'])) {
            // Ensure, the client_config is an array.
            if (!is_array($config['client_config'])) {
                throw new \InvalidArgumentException('A client_config must be an array.');
            }
            $client_config += $config['client_config'];
        }

        // Create a Guzzle client.
        $client = new Client($client_config);

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

        // Provide backwards compatible authType.
        if (empty($config['authType'])) {
            $config['authType'] = 'token';
        }

        switch ($config['authType']) {
            case 'oauth2':
                $middleware = $this->getOAuth2Middleware($config);
                $stack->push($middleware);
                $this->authenticationMiddleware = $middleware;
                break;
            case 'token':
                $middleware = $this->getTokenMiddleware($config);
                $stack->push($middleware);
                $this->authenticationMiddleware = $middleware;
                break;
        }

        return $stack;
    }

  /**
   * Get the authentication middleware, if there is any.
   *
   * @return object
   *   The authentication middleware.
   */
    public function getAuthenticationMiddleware() {
      return $this->authenticationMiddleware;
    }

    /**
     * Build a legacy token middleware.
     *
     * @param array $config
     *   Holds the configuration to initialize the service client.
     *
     * @return \kamermans\OAuth2\OAuth2Middleware
     *   The legacy token middleware.
     */
    private function getTokenMiddleware($config)
    {
        if (empty($config['apiToken'])) {
            //throw new \InvalidArgumentException('A apiToken must be provided, when using the token auth type.');
        }
        return new TokenMiddleware($config);
    }

    /**
     * Build a oAuth2 middleware.
     *
     * @param array $config
     *   Holds the configuration to initialize the service client.
     *
     * @return \kamermans\OAuth2\OAuth2Middleware
     *   The oAuth2 middleware.
     */
    private function getOAuth2Middleware($config)
    {
        if (empty($config['clientId']) || empty($config['clientSecret'])) {
            throw new \InvalidArgumentException('A clientId and clientSecret must be provided, when using the oauth2 auth type.');
        }
        $reauthClient = new Client([
          'base_uri' => $config['baseUrl'] . '/fotoweb/oauth2/token',
        ]);
        $reauthConfig = [
          'client_id' => $config['clientId'],
          'client_secret' => $config['clientSecret'],
        ];

        // We use different grant types for API requests and user initiated
        // ones.
        if ($config['grantType'] === 'authorization_code') {
          $reauthConfig['code'] = $config['authorizationCode'] ?? NULL;
          $reauthConfig['code_verifier'] = $config['codeVerifier'] ?? NULL;
          $reauthConfig['redirect_uri'] = $config['redirectUri'] ?? NULL;
          $grantType = new AuthorizationCodeWithPkce($reauthClient, $reauthConfig);
          $refreshGrantType = new RefreshToken($reauthClient, $reauthConfig);
          $clientCredentialsSigner = new PostFormData();
          $middleware = new OAuth2Middleware($grantType, $refreshGrantType, $clientCredentialsSigner);
          if (isset($config['persistenceProvider'])) {
            $middleware->setTokenPersistence($config['persistenceProvider']);
          }
        }
        else {
          $grantType = new ClientCredentials($reauthClient, $reauthConfig);
          $refreshGrantType = new RefreshToken($reauthClient, $reauthConfig);
          $clientCredentialsSigner = new PostFormData();
          $accessTokenSigner = new QueryString();
          $middleware = new OAuth2Middleware($grantType, $refreshGrantType, $clientCredentialsSigner, $accessTokenSigner);
          if (isset($config['persistenceProvider'])) {
            $middleware->setTokenPersistence($config['persistenceProvider']);
          }
        }

        return $middleware;
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

    /**
     * Returns the rendition for a given renditionResource href.
     *
     * @param $href
     *   Rendition resource href.
     *
     * @return mixed
     */
    public function getRendition($href)
    {
        return $this->getHttpClient()->get($href);
    }
}
