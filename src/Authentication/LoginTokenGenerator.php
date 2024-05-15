<?php

// @codingStandardsIgnoreFile

namespace Fotoweb\Authentication;

/**
 * Generates login tokens for authenticated requests the Fotoweb API.
 *
 * @package Fotoweb\Authentication
 * @see https://bitbucket.org/fotoware/phplogintokengenerator
 */
class LoginTokenGenerator {

    private $_vp;
    private $_useForWidgets;
    private $_es;


    public function __construct ($encryption_secret, $useForWidgets) {
        $this->_useForWidgets = $useForWidgets;
        $this->_es = $encryption_secret;
    }

    public function CreateLoginToken( $username )
    {
        $startTime = gmdate('Y-m-d H:i:s', strtotime("-1 minutes"));
        $endTime = gmdate("Y-m-d H:i:s", strtotime("+30 minutes"));
        $token = sprintf('s=%s;e=%s;w=%s;u=%s;', $startTime, $endTime, $this->_useForWidgets ? 'true' : 'false', $username);
        $token .= 'm=' . $this->CreateStringMac($token) . ';';
        return base64_encode($token);
    }

    private function CreateStringMac( $data )
    {
        $data = $data . 'es=' . $this->_es;
        $mac = md5($data, true);
        return base64_encode($mac);
    }
}
