<?php

namespace Fotoweb\Tests\Authentication;

use \PHPUnit\Framework\TestCase;
use Fotoweb\Authentication\LoginTokenGenerator;

class LoginTokenGeneratorTest extends TestCase
{

    public function testLoginTokenGeneration()
    {
        $tokenGenerator = new LoginTokenGenerator('1234567abcdef', true);
        $loginToken = $tokenGenerator->CreateLoginToken('user1');
        $this->assertNotEmpty($loginToken,
          'The created login token must be non empty.');

        // Decode the token to be able to validate it.
        $decodedToken = base64_decode($loginToken);

        // Remove the trailing token semicolon to be able to explode them.
        $decodedToken = substr($decodedToken, 0, -1);

        // Split up the token string into its parts.
        $partial = explode(';', $decodedToken);
        $tokenParts = array();
        array_walk($partial, function($val, $key) use(&$tokenParts){
            list($key, $value) = explode('=', $val);
            $tokenParts[$key] = $value;
        });

        // Test if the token consists of the required parts: start and end time
        // of the token, username and signature.
        $this->assertArrayHasKey('s', $tokenParts, 'The token must include the start time token part.');
        $this->assertArrayHasKey('e', $tokenParts, 'The token must include the end time token part.');
        $this->assertArrayHasKey('u', $tokenParts, 'The token must include the user token part.');
        $this->assertArrayHasKey('m', $tokenParts, 'The token must include the signature token part.');

        // Test the parsed start date.
        $parsedDate = date_parse($tokenParts['s']);
        $this->assertNotFalse($parsedDate, 'The parsed start date must be not false. Otherwise the date was not valid.');
        $this->assertEquals(0, $parsedDate['error_count'], 'The parsed start date must not return any error.');

        // Test the parsed end date.
        $parsedDate = date_parse($tokenParts['e']);
        $this->assertNotFalse($parsedDate, 'The parsed end date must be not false. Otherwise the date was not valid.');
        $this->assertEquals(0, $parsedDate['error_count'], 'The parsed end date must not return any error.');
    }
}
