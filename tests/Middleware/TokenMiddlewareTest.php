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
class FotowebClientTest extends FotowebTestWrapper
{

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Dataprovider providing invalid tokens.
     *
     * @return array
     */
    public function invalidTokens()
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
    public function validTokens()
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
     * @expectedException InvalidArgumentException
     */
    public function testFotowebClientCreationRaisesExceptionOnInvalidToken($token)
    {
        $middleware = new TokenMiddleware();
        $middleware->validateToken($token);
    }

    /**
     * Tests, that valid tokens are considered to be valid.
     *
     * @dataProvider validTokens
     */
    public function testFotowebClientCreationSucceedsOnValidToken($token)
    {
        $middleware = new TokenMiddleware();
        $this->assertTrue($middleware->validateToken($token), 'The token must be valid.');
    }
}
