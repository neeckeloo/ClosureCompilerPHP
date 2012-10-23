<?php
/**
 * ClosureCompilerPHP
 * 
 * @link      http://github.com/neeckeloo/ClosureCompilerPHP
 * @copyright Copyright (c) 2012 Nicolas Eeckeloo
 */
namespace Closure;

class RemoteCompilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RemoteCompiler
     */
    protected $compiler;
    
    public function setUp()
    {
        $this->compiler = $this->getMockForAbstractClass('Closure\RemoteCompiler');
    }

    public function testSetRequestHandler()
    {
        $this->assertInstanceOf(
            'Closure\HttpRequestHandler',
            $this->compiler->getRequestHandler()
        );

        $this->compiler->setRequestHandler(new HttpRequestHandler());
        $this->assertInstanceOf(
            'Closure\HttpRequestHandler',
            $this->compiler->getRequestHandler()
        );
    }
}