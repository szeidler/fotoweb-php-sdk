<?php

namespace Fotoweb\Response;

use GuzzleHttp\Command\ResultInterface;

/**
 * Defines an interface for dealing with all Fotoweb responses.
 *
 * @package Fotoweb\Response
 */
interface FotowebResultInterface extends ResultInterface
{

    /**
     * Gets the data of a FotowebResult.
     *
     * @return array
     */
    public function getData();

    /**
     * Sets the data of a FotowebResult.
     *
     * @param array $data
     */
    public function setData(array $data);
}
