<?php

namespace Fotoweb\Authentication\Authenticator;

use \Fotoweb\FotowebAPI;

class Authenticator
{

    const MIME_TYPE = 'application/vnd.fotoware.full-api-descriptor+json';

    public function authenticate(FotowebAPI $fotowebAPI)
    {
        $request = new Request(
          'GET', '/fotoweb/me', [
            'Accept' => self::MIME_TYPE,
            'FWAPITOKEN' => $fotowebAPI->getApiToken(),
          ]
        );

        return $this->request($request);
    }
}
