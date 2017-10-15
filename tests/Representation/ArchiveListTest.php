<?php

namespace Fotoweb\Tests\Representation;

use Fotoweb\Tests\FotowebTestWrapper;
use GuzzleHttp\Command\ResultInterface;

/**
 * Tests the ArchiveList response model.
 *
 * @package Fotoweb\Tests\Representation
 * @see \Fotoweb\Representation\ArchiveList
 */
class ArchiveListTest extends FotowebTestWrapper
{

    /**
     * Tests, that the ArchiveList request returns a valid response.
     */
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