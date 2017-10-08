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

        $this->client = new FotowebClient(
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
        $this->assertEquals('/fotoweb/me', $response->getHref(), 'The response should return the href of the resource.');
    }
}