<?php

namespace Fotoweb\Tests\Representation;

use \PHPUnit\Framework\TestCase;

/**
 * Tests the abstract BaseRepresentation model.
 *
 * @package Fotoweb\Tests\Representation
 * @see \Fotoweb\Representation\BaseRepresentation
 */
class BaseRepresentationTest extends TestCase
{

    protected $baseRepresentation;
    protected $data;

    public function setUp(): void {
        parent::setUp();

        $this->data = array('key1' => 'value1', 'key2' => 'value2');
        $stub = $this->getMockForAbstractClass('Fotoweb\Representation\BaseRepresentation', array($this->data));
        $this->baseRepresentation = $stub;
    }

    /**
     * Tests, that the data getter is getting the initialized data.
     */
    public function testGetData()
    {
        $this->assertEquals($this->data, $this->baseRepresentation->getData(), 'The representation should return the initial data.');
    }

    /**
     * Test that the data setter is setting the data.
     */
    public function testSetData()
    {
        $data = array('key3' => 'value3', 'key4' => 'value4');
        $this->baseRepresentation->setData($data);
        $this->assertEquals($data, $this->baseRepresentation->getData(), 'The representation should return the input data.');
    }
}
