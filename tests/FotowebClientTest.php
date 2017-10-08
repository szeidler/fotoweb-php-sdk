<?php

use Fotoweb\FotowebClient;
use GuzzleHttp\Client;
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

    public function testCustomClientFromConfig()
    {
        $base_uri = 'http://httpbin.org/';

        // Create a custom client.
        $custom_client = new Client(['base_uri' => $base_uri]);

        // Inject the custom client as configuration into the FotowebClient.
        $client = new FotowebClient(
          [
            'client' => $custom_client,
            'baseUrl' => getenv('BASE_URL'),
          ]
        );

        $this->assertEquals($base_uri, $client->getHttpClient()->getConfig('base_uri'), 'The FotowebClient must return the base_uri of the injected Client.');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMissingBaseUrlInClientConfiguration()
    {
        $client = new FotowebClient(
          [
            'apiToken' => getenv('FULLAPI_KEY'),
          ]
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testMissingTokenInClientConfiguration()
    {
        $client = new FotowebClient(
          [
            'baseUrl' => getenv('BASE_URL'),
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