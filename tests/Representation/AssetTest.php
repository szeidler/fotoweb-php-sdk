<?php

namespace Fotoweb\Tests\Representation;

use Fotoweb\Tests\FotowebTestWrapper;
use GuzzleHttp\Command\ResultInterface;

/**
 * Tests the Asset response model.
 *
 * @package Fotoweb\Tests\Representation
 * @see     \Fotoweb\Representation\Asset
 */
class AssetTest extends FotowebTestWrapper
{

    /**
     * Tests, that the Asset request returns a valid response.
     */
    public function testGetAsset()
    {
        $href = getenv('ASSET_HREF');

        $response = $this->client->getAsset(['href' => $href]);
        $this->assertInstanceOf(ResultInterface::class, $response,
          'The response is not a proper Guzzle result.');
        $this->assertEquals($href, $response->getHref(),
          'The response should return the href of the given resource.');
        $this->assertGreaterThan(0, $response->offsetGet('filesize'),
          'The asset response should include a filesize.');
        $this->assertNotEmpty($response->offsetGet('previews'),
          'The asset response should have previews.');
    }

    public function testUpdateMetadata()
    {
        $href = getenv('ASSET_HREF');

        // Append a random string, to be sure, that metadata is really updated.
        $randomMetadataString = 'Updated metadata: ' . hash('sha256', time());
        $metadata = [40 => ['value' => $randomMetadataString]];
        $response = $this->client->updateMetadata(['href' => $href, 'metadata' => $metadata]);

        // Check, that we get a valid updated asset response back.
        $this->assertInstanceOf(ResultInterface::class, $response,
          'The response is not a proper Guzzle result.');
        $this->assertEquals($href, $response->getHref(),
          'The response should return the href of the given resource, that was updated.');
        $this->assertEquals($metadata, $response->offsetGet('metadata'),
          'Updated metadat we saved.');

        // Double check, that the metadata was properly updated by fetching the asset again.
        $response = $this->client->getAsset(['href' => $href]);
        $this->assertEquals($metadata, $response->offsetGet('metadata'),
          'Updated metadata must match the metadata we saved.');
    }
}
