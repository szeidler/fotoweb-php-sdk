<?php

// @codingStandardsIgnoreFile

namespace Fotoweb\Authentication;

/**
 * Class LoginTokenGenerator
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
        $utf8String = utf8_encode($token);
        return base64_encode($utf8String);
    }

    private function CreateStringMac( $data )
    {
        $data = $data . 'es=' . $this->_es;
        $dataAsUtf8 = utf8_encode($data);
        $mac = md5($dataAsUtf8, true);
        return base64_encode($mac);
    }
}
