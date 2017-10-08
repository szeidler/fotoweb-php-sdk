<?php

namespace Fotoweb\Tests\Authentication;

use Fotoweb\Tests\FotowebTestWrapper;
use GuzzleHttp\Command\ResultInterface;

class ApiDescriptorTest extends FotowebTestWrapper
{

    public function testGetApiDescriptor()
    {
        $response = $this->client->getApiDescriptor();
        $this->assertInstanceOf(ResultInterface::class, $response,
          'The response is not a proper Guzzle result.');
        $this->assertEquals('/fotoweb/me', $response->getHref(),
          'The response should return the href of the resource.');
    }

}