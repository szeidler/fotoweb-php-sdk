<?php

namespace Fotoweb\Tests\Middleware;

use Fotoweb\Middleware\TokenMiddleware;
use Fotoweb\Tests\FotowebTestWrapper;

/**
 * Tests the TokenMiddleware class.
 *
 * @package Fotoweb\Tests\Middleware
 * @see     \Fotoweb\Middleware\TokenMiddleware
 */
class TokenMiddlewareTest extends FotowebTestWrapper
{

    public function setUp(): void {
        parent::setUp();
    }

    /**
     * Test, that the token getter returns the initialized token or null, if not provided.
     */
    public function testTokenGetter()
    {
        $token = 'mytoken';
        $middleware = new TokenMiddleware(['apiToken' => $token]);
        $this->assertEquals($token, $middleware->getToken(), 'The token getter must return the right token');

        $middleware = new TokenMiddleware();
        $this->assertNull($middleware->getToken(),
          'The token getter must return null, when it was initialized without a token.');
    }

    /**
     * Dataprovider providing invalid tokens.
     *
     * @return array
     */
    public static function invalidTokens()
    {
        return [
          'empty'        => [''],
          'a'            => ['a'],
          'ab'           => ['ab'],
          'abc'          => ['abc'],
          'digit'        => [1],
          'double-digit' => [12],
          'triple-digit' => [123],
          'bool'         => [true],
          'array'        => [['token']],
        ];
    }

    /**
     * Dataprovider providing valid tokens.
     *
     * @return array
     */
    public static function validTokens()
    {
        return [
          'token'      => ['token'],
          'short-hash' => ['123456789'],
          'full-hash'  => ['akrwejhtn983z420qrzc8397r4'],
        ];
    }


    /**
     * Tests, that the client throws an exception on invalid tokens.
     *
     * @dataProvider invalidTokens
     *
     */
    public function testTokenMiddlewareRaisesExceptionOnInvalidToken($token)
    {
      $this->expectException(\InvalidArgumentException::class);
      $middleware = new TokenMiddleware();
        $middleware->validateToken($token);
    }

    /**
     * Tests, that valid tokens are considered to be valid.
     *
     * @dataProvider validTokens
     */
    public function testTokenMiddlewareValidatesSuccessfullyOnValidToken($token)
    {
        $middleware = new TokenMiddleware();
        $this->assertTrue($middleware->validateToken($token), 'The token must be valid.');
    }
}
