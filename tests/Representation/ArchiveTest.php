<?php

namespace Fotoweb\Tests\Representation;

use Fotoweb\Tests\FotowebTestWrapper;
use GuzzleHttp\Command\ResultInterface;

/**
 * Tests the Archive response model.
 *
 * @package Fotoweb\Tests\Representation
 * @see \Fotoweb\Representation\\Archive
 */
class ArchiveTest extends FotowebTestWrapper
{

    /**
     * Tests, that the Archive request returns a valid response.
     */
    public function testGetArchive()
    {
        $href = getenv('ARCHIVE_HREF');

        $response = $this->client->getArchive(['href' => $href]);
        $this->assertInstanceOf(ResultInterface::class, $response,
          'The response is not a proper Guzzle result.');
        $this->assertEquals($href, $response->offsetGet('data'),
          'The response should return the requested href as a data property.');
        $this->assertNotEmpty($response->offsetGet('name'),
          'The response should return a name.');
    }
}
