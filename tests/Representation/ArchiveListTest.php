<?php

namespace Fotoweb\Tests\Representation;

use Fotoweb\Tests\FotowebTestWrapper;
use GuzzleHttp\Command\ResultInterface;

class ArchiveListTest extends FotowebTestWrapper
{

    public function testGetArchiveList()
    {
        $href = '/fotoweb/me/archives/';

        $response = $this->client->getArchives(['href' => $href]);
        $this->assertInstanceOf(ResultInterface::class, $response,
          'The response is not a proper Guzzle result.');
        $this->assertGreaterThan(0, $response->offsetGet('data'),
          'The asset response should include a data property.');
        $this->assertArrayHasKey('paging', $response->getData(),
          'The asset response should include a paging property.');
    }

}