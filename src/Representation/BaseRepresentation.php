<?php

namespace Fotoweb\Representation;

use Fotoweb\Response\FotowebResult;

/**
 * Defines an abstract representation for non-lists with own href property.
 *
 * @package Fotoweb\Representation
 */
abstract class BaseRepresentation extends FotowebResult
{

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
