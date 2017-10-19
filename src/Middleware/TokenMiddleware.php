<?php

namespace Fotoweb\Middleware;

use Psr\Http\Message\RequestInterface;

class TokenMiddleware
{

    protected $config;

    /**
     * TokenMiddleware constructor.
     *
     * @param $config
     *   Holds the configuration to initialize the service client.
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Invokes the token application.
     *
     * @param callable $handler
     *
     * @return \Closure
     */
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use (&$handler) {
            $request = $this->applyToken($request);
            return $handler($request, $options);
        };
    }

    /**
     * Returns the token from the configuration.
     *
     * @return string|null
     */
    public function getToken()
    {
        if (!empty($this->config['apiToken'])) {
            return $this->config['apiToken'];
        } else {
            return null;
        }
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
    public function validateToken($token)
    {
        if (empty($token)) {
            throw new \InvalidArgumentException('A apiToken must be provided.');
        }
        if (!is_string($token)) {
            throw new \InvalidArgumentException('The provided token is not a string.');
        }
        if (strlen($token) < 4) {
            throw new \InvalidArgumentException('The provided token must be longer than 3 characters.');
        }
        return true;
    }

    /**
     * Applies the apiToken to the request.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return static
     */
    protected function applyToken(RequestInterface $request)
    {
        $token = $this->getToken();
        $this->validateToken($token);
        return $request->withHeader('FWAPITOKEN', $this->getToken());
    }
}
