<?php

namespace Fotoweb\Tests\Representation;

use Fotoweb\Tests\FotowebTestWrapper;
use GuzzleHttp\Command\ResultInterface;

/**
 * Tests the Asset response model.
 *
 * @package Fotoweb\Tests\Representation
 * @see     \Fotoweb\Representation\RenditionRequest
 */
class RenditionRequestTest extends FotowebTestWrapper
{

    /**
     * Tests to retrieve the Rendition Request
     */
    public function testGetRenditionRequest()
    {
        // TODO: We need to wait, until the test server fixes an internal server error.
        $this->markTestIncomplete();

        $href = getenv('ASSET_HREF');
        $rendition_service = '/fotoweb/services/renditions';

        $response = $this->client->getAsset(['href' => $href]);
        $renditionHref = $response->offsetGet('renditions')[0]['href'];

        $response = $this->client->getRenditionRequest([
          'rendition_service' => $rendition_service,
          'href'              => $renditionHref,
        ]);
        $this->assertInstanceOf(ResultInterface::class, $response,
          'The response is not a proper Guzzle result.');
        $this->assertEquals($href, $response->getHref(),
          'The response should return the href of the requested rendition.');
    }

}
