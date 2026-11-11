<?php

namespace Fotoweb\Tests;

use Fotoweb\FotowebClient;
use Fotoweb\OAuth2\GrantType\AuthorizationCodeWithPkce;
use kamermans\OAuth2\Signer\ClientCredentials\PostFormData;
use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * Exercises request serialization and response parsing without a live API.
 */
class GuzzleCompatibilityTest extends TestCase
{
    public function testServiceCommandWithMockedResponse(): void
    {
        $handler = new MockHandler([new Response(200, [], '{"value":"ok"}')]);
        $client = new FotowebClient([
            'client' => new Client(['handler' => HandlerStack::create($handler)]),
            'description' => new Description([
                'baseUri' => 'https://example.com/',
                'operations' => [
                    'testCommand' => [
                        'httpMethod' => 'GET',
                        'uri' => '/test',
                        'responseModel' => 'testResult',
                    ],
                ],
                'models' => ['testResult' => ['type' => 'object']],
            ]),
        ]);

        $result = $client->testCommand();
        self::assertSame('ok', $result['value']);
        self::assertSame([], $result['_request']);
    }

    public function testPkceTokenRequestWithMockedResponse(): void
    {
        $handler = new MockHandler([
            new Response(200, [], '{"access_token":"offline-token","token_type":"bearer"}'),
        ]);
        $client = new Client([
            'base_uri' => 'https://example.com/token',
            'handler' => HandlerStack::create($handler),
        ]);
        $grant = new AuthorizationCodeWithPkce($client, [
            'client_id' => 'offline-client',
            'client_secret' => 'offline-secret',
            'code' => 'offline-code',
            'code_verifier' => 'offline-verifier',
        ]);

        $data = $grant->getRawData(new PostFormData());
        self::assertSame('offline-token', $data['access_token']);
        parse_str((string) $handler->getLastRequest()->getBody(), $request);
        self::assertSame('offline-verifier', $request['code_verifier']);
        self::assertSame('authorization_code', $request['grant_type']);
    }

}
