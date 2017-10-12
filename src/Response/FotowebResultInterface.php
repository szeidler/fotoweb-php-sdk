<?php

namespace Fotoweb\Response;

use GuzzleHttp\Command\ResultInterface;

interface FotowebResultInterface extends ResultInterface
{

    public function getData();

    public function setData(array $data);

}