<?php

use \PHPUnit\Framework\TestCase;

class BaseRepresentationTest extends TestCase
{

    protected $baseRepresentation;
    protected $data;

    public function setUp()
    {
        parent::setUp();

        $this->data = array('key1' => 'value1', 'key2' => 'value2');
        $stub = $this->getMockForAbstractClass('Fotoweb\Representation\BaseRepresentation', array($this->data));
        $this->baseRepresentation = $stub;
    }

    public function testGetData()
    {
        $this->assertEquals($this->data, $this->baseRepresentation->getData(), 'The representation should return the initial data.');
    }

    public function testSetData()
    {
        $data = array('key3' => 'value3', 'key4' => 'value4');
        $this->baseRepresentation->setData($data);
        $this->assertEquals($data, $this->baseRepresentation->getData(), 'The representation should return the input data.');
    }
}