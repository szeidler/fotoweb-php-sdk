<?php

namespace Fotoweb\Response;

use GuzzleHttp\Command\HasDataTrait;

/**
 * Defines a result class as a data storage for all responses.
 *
 * @package Fotoweb\Response
 */
class FotowebResult implements FotowebResultInterface
{

    use HasDataTrait;

    /**
     * FotowebResult constructor.
     *
     * @param array $data
     *   Array of data provided by a http response.
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }
}
