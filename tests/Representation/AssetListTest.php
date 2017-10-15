<?php

namespace Fotoweb\Tests\Representation;

use Fotoweb\Tests\FotowebTestWrapper;
use GuzzleHttp\Command\ResultInterface;

/**
 * Tests the AssetList response model.
 *
 * @package Fotoweb\Tests\Representation
 * @see \Fotoweb\Representation\AssetList
 */
class AssetListTest extends FotowebTestWrapper
{

    /**
     * Tests, that the AssetList request returns a valid response.
     */
    public function testGetAssetList()
    {
        $href = getenv('ASSET_LIST_HREF');

        $response = $this->client->getAssetList(['href' => $href]);
        $this->assertInstanceOf(ResultInterface::class, $response,
          'The response is not a proper Guzzle result.');
        $this->assertGreaterThan(0, $response->offsetGet('data'),
          'The asset response should include a data property.');
        $this->assertArrayHasKey('paging', $response->getData(),
          'The asset response should include a paging property.');
    }
}
