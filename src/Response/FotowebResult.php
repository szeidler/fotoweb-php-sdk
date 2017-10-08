<?php

namespace Fotoweb\Response;

use GuzzleHttp\Command\HasDataTrait;

class FotowebResult implements FotowebResultInterface
{
    use HasDataTrait;

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * Provide an easier accessor for the href property of each representation.
     *
     * @return string|null
     */
    public function getHref()
    {
        return $this->offsetGet('href');
    }

}