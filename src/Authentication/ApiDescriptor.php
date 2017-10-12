<?php

namespace Fotoweb\Authentication;

use Fotoweb\Response\FotowebResult;

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