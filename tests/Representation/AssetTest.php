<?php

namespace Fotoweb\Tests\Representation;

use Fotoweb\Tests\FotowebTestWrapper;
use GuzzleHttp\Command\ResultInterface;

class AssetTest extends FotowebTestWrapper
{

    public function testGetAsset()
    {
        $href = getenv('ASSET_HREF');

        $response = $this->client->getAsset(['href' => $href]);
        $this->assertInstanceOf(ResultInterface::class, $response,
          'The response is not a proper Guzzle result.');
        $this->assertEquals($href, $response->getHref(),
          'The response should return the href of the given resource.');
    }

}