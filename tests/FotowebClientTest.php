<?php

namespace Fotoweb\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Command\ResultInterface;
use GuzzleHttp\Command\Guzzle\Description;
use Fotoweb\FotowebClient;

/**
 * Tests the FotowebClient class.
 *
 * @package Fotoweb\Tests
 * @see     \Fotoweb\FotowebClient
 */
class FotowebClientTest extends FotowebTestWrapper
{

    protected $client;

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Tests, that a http client can be injected via the config array.
     */
    public function testCustomClientFromConfig()
    {
        $base_uri = 'http://httpbin.org/';

        // Create a custom client.
        $custom_client = new Client(['base_uri' => $base_uri]);

        // Inject the custom client as configuration into the FotowebClient.
        $client = new FotowebClient(
          [
            'client'   => $custom_client,
            'baseUrl'  => getenv('BASE_URL'),
            'apiToken' => getenv('FULLAPI_KEY'),
          ]
        );

        $this->assertEquals($custom_client, $client->getHttpClient(),
          'The FotowebClient must return the base_uri of the injected Client.');
    }

    /**
     * Tests, that a service description can be injected via the config array.
     */
    public function testCustomDescriptiontFromConfig()
    {
        $description = new Description([
          'baseUri'    => 'http://httpbin.org/',
          'operations' => [
            'testing' => [
              'httpMethod'    => 'GET',
              'uri'           => '/get{?foo}',
              'responseModel' => 'getResponse',
              'parameters'    => [
                'foo' => [
                  'type'     => 'string',
                  'location' => 'uri',
                ],
                'bar' => [
                  'type'     => 'string',
                  'location' => 'query',
                ],
              ],
            ],
          ],
          'models'     => [
            'getResponse' => [
              'type'                 => 'object',
              'additionalProperties' => [
                'location' => 'json',
              ],
            ],
          ],
        ]);

        // Inject the custom description as configuration into the FotowebClient.
        $client = new FotowebClient(
          [
            'description' => $description,
            'baseUrl'     => getenv('BASE_URL'),
            'apiToken'    => getenv('FULLAPI_KEY'),
          ]
        );

        $this->assertEquals($description, $client->getDescription(),
          'The description must return the injected description.');

        // Our custom description doesn't provide a custom ResponseModel class,
        // so it should fallback to Fotoweb\Response\FotowebResult.
        $this->assertInstanceOf('Fotoweb\Response\FotowebResult',
          $client->testing(['foo' => 'bar', 'bar' => 'foo']),
          'The response must be instance of FotowebResult.');
    }

    /**
     * Tests, that a missing baseUrl throws an exception.
     *
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
}
