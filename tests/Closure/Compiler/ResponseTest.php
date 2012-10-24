<?php
/**
 * ClosureCompilerPHP
 * 
 * @link      http://github.com/neeckeloo/ClosureCompilerPHP
 * @copyright Copyright (c) 2012 Nicolas Eeckeloo
 */
namespace Closure\Compiler;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Response
     */
    protected $response;
    
    public function setUp()
    {
        $this->response = new Response();
    }

    public function testSetCompiledCode()
    {
        $this->response->setCompiledCode('foo');
        $this->assertEquals('foo', $this->response->getCompiledCode());
    }

    public function testSetWarnings()
    {
        $this->response->setWarnings(array('foo' => 123));
        $this->assertCount(1, $this->response->getWarnings());
    }

    public function testSetErrors()
    {
        $this->response->setErrors(array('foo' => 123));
        $this->assertCount(1, $this->response->getErrors());
    }

    public function testSetOriginalSize()
    {
        $this->response->setOriginalSize(123);
        $this->assertEquals(123, $this->response->getOriginalSize());
    }

    public function testSetOriginalGzipSize()
    {
        $this->response->setOriginalGzipSize(123);
        $this->assertEquals(123, $this->response->getOriginalGzipSize());
    }

    public function testSetCompressedSize()
    {
        $this->response->setCompressedSize(123);
        $this->assertEquals(123, $this->response->getCompressedSize());
    }

    public function testSetCompressedGzipSize()
    {
        $this->response->setCompressedGzipSize(123);
        $this->assertEquals(123, $this->response->getCompressedGzipSize());
    }

    public function testSetCompileTime()
    {
        $this->response->setCompileTime(123);
        $this->assertEquals(123, $this->response->getCompileTime());
    }
}