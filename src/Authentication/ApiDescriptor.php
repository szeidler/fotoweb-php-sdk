<?php

namespace Fotoweb\Authentication;

use Fotoweb\Response\FotowebResult;

/**
 * Defines the result model for an APIDescriptor request.
 *
 * @package Fotoweb\Authentication
 * @see     https://learn.fotoware.com/02_FotoWeb_8.0/Developing_with_the_FotoWeb_API/01_The_FotoWeb_RESTful_API/03_API_Authentication
 */
class ApiDescriptor extends FotowebResult
{

    /**
     * Provide access to the href property.
     *
     * @return string|null
     */
    public function getHref()
    {
        return $this->offsetGet('href');
    }
}
