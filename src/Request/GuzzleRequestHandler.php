<?php

namespace Fotoweb\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;

class GuzzleRequestHandler implements RequestHandler
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function handle(RequestInterface $request)
    {
        try {
            return $this->client->send($request);
        } catch (RequestException $e) {
            return $e->getResponse();
        }
    }

}
