<?php

use Fotoweb\FotowebClient;
use GuzzleHttp\Command\ResultInterface;
use \PHPUnit\Framework\TestCase;

class FotowebClientTest extends TestCase
{

    protected $client;

    public function setUp()
    {
        parent::setUp();

        $this->client = FotowebClient::create(
          [
            'baseUrl' => getenv('BASE_URL'),
            'apiToken' => getenv('FULLAPI_KEY'),
          ]
        );
    }

    public function testGetApiDescriptor()
    {
        $response = $this->client->getApiDescriptor();
        $this->assertInstanceOf(ResultInterface::class, $response, 'The response is not a proper Guzzle result.');
        $this->assertArrayHasKey('server', $response->toArray(), 'The response misses the server property.');
        $this->assertArrayHasKey('version', $response->toArray(), 'The response misses the version property.');
    }
}