<?php

namespace Fotoweb\Tests\Representation;

use Fotoweb\Tests\FotowebTestWrapper;
use GuzzleHttp\Command\ResultInterface;

class AssetListTest extends FotowebTestWrapper
{

    public function testGetAssetList()
    {
        $href = getenv('ASSET_LIST_HREF');

        $response = $this->client->getAssetList(['href' => $href]);
        $this->assertInstanceOf(ResultInterface::class, $response,
          'The response is not a proper Guzzle result.');
        $this->assertGreaterThan(0, $response->offsetGet('data'),
          'The asset response should include a data property.');
        $this->assertNotEmpty($response->offsetGet('paging'),
          'The asset response should include a paging property.');
    }

}