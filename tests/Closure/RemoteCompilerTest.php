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

    public function testCompile()
    {
        $this->compiler->addScript('vr console = function() { alert(\'toto\'); }');
        $response = $this->compiler->compile();

        var_dump($response->getWarnings());
        var_dump($response->getErrors());

        $this->assertInstanceOf('Closure\Compiler\Response', $response);

        $this->assertTrue(is_string($response->getCompiledCode()));
        $this->assertTrue(is_array($response->getWarnings()));
        $this->assertTrue(is_array($response->getErrors()));
        $this->assertTrue(is_integer($response->getCompileTime()));
        $this->assertTrue(is_integer($response->getOriginalSize()));
        $this->assertTrue(is_integer($response->getOriginalGzipSize()));
        $this->assertTrue(is_integer($response->getCompressedSize()));
        $this->assertTrue(is_integer($response->getCompressedGzipSize()));
    }
}