<?php

namespace Fotoweb;

use Fotoweb\Request\RequestHandler;

class FotowebAPI
{

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $apiToken;

    /**
     * @var RequestHandler
     */
    private $requestHandler;

    public function __construct(RequestHandler $requestHandler, $baseUrl, $apiToken)
    {
        $this->requestHandler = $requestHandler;
        $this->baseUrl = $baseUrl;
        $this->apiToken = $apiToken;
    }

    public function sendRequest(Request $request)
    {
        $response = $this->requestHandler->handle($request);

        return $response;
    }

}