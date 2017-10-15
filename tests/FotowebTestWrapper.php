<?php

namespace Fotoweb\Tests;

use PHPUnit\Framework\TestCase;
use Fotoweb\FotowebClient;

class FotowebTestWrapper extends TestCase
{

    protected $client;

    public function setUp()
    {
        parent::setUp();

        $this->client = new FotowebClient(
          [
            'baseUrl'  => getenv('BASE_URL'),
            'apiToken' => getenv('FULLAPI_KEY'),
          ]
        );
    }
}
