<?php

use \PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Exception\BadResponseException;
use Fotoweb\Request\GuzzleRequestHandler;

class AuthenticationTest extends TestCase
{
    protected $requestHandler;

    public function setUp()
    {
        parent::setUp();
        $mock = new MockHandler(
          [
            new Response(200, ['X-Foo' => 'Bar']),
            new Response(202, ['Content-Length' => 0]),
            new BadResponseException('Bad response.Bad response.', new Request('GET', '/')),
          ]
        );
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $this->requestHandler = new GuzzleRequestHandler($client);
    }

    public function testHandle()
    {
        $request = new Request('GET', '/');
        $response = $this->requestHandler->handle($request);
        $this->assertEquals(200, $response->getStatusCode(), 'Response status code was not 200.');

        $request = new Request('GET', '/');
        $response = $this->requestHandler->handle($request);
        $this->assertEquals(202, $response->getStatusCode(), 'Response status code was not 202.');

        // Check, that RequestExceptions are being catched.
        $request = new Request('GET', '/');
        $response = $this->requestHandler->handle($request);
        $this->assertNull($response, 'Response with expected exception was not null.');
    }

}
