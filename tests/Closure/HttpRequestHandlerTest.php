<?php
/**
 * ClosureCompilerPHP
 * 
 * @link      http://github.com/neeckeloo/ClosureCompilerPHP
 * @copyright Copyright (c) 2012 Nicolas Eeckeloo
 */
namespace Closure;

class HttpRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HttpRequestHandler
     */
    protected $requestHandler;
    
    public function setUp()
    {
        $this->requestHandler = new HttpRequestHandler();
    }

    public function testSetMethod()
    {
        $this->requestHandler->setMethod('post');
        $this->assertEquals('POST', $this->requestHandler->getMethod());
    }

    /**
     * @expectedException Closure\Exception\InvalidArgumentException
     */
    public function testSetMethodWithInvalidParam()
    {
        $this->requestHandler->setMethod('foo');
    }
}