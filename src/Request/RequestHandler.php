<?php

namespace Fotoweb\Request;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RequestHandler
{
    /**
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     */
    public function handle(RequestInterface $request);
}
