<?php
/**
 * ClosureCompilerPHP
 * 
 * @link      http://github.com/neeckeloo/ClosureCompilerPHP
 * @copyright Copyright (c) 2012 Nicolas Eeckeloo
 */
namespace Closure;

class AbstractCompilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractCompiler
     */
    protected $compiler;
    
    public function setUp()
    {
        $this->compiler = $this->getMockForAbstractClass('Closure\AbstractCompiler');
    }

    public function testSetMode()
    {
        $this->assertEquals(
            AbstractCompiler::MODE_WHITESPACE_ONLY,
            $this->compiler->getMode()
        );

        $this->compiler->setMode(AbstractCompiler::MODE_ADVANCED_OPTIMIZATIONS);
        $this->assertEquals(
            AbstractCompiler::MODE_ADVANCED_OPTIMIZATIONS,
            $this->compiler->getMode()
        );
    }

    public function testSetOutputFormat()
    {
        $this->assertEquals(
            AbstractCompiler::OUTPUT_FORMAT_XML,
            $this->compiler->getOutputFormat()
        );

        $this->compiler->setOutputFormat(AbstractCompiler::OUTPUT_FORMAT_JSON);
        $this->assertEquals(
            AbstractCompiler::OUTPUT_FORMAT_JSON,
            $this->compiler->getOutputFormat()
        );
    }

    public function testSetWarningLevel()
    {
        $this->assertEquals(
            AbstractCompiler::WARNING_LEVEL_DEFAULT,
            $this->compiler->getWarningLevel()
        );

        $this->compiler->setWarningLevel(AbstractCompiler::WARNING_LEVEL_VERBOSE);
        $this->assertEquals(
            AbstractCompiler::WARNING_LEVEL_VERBOSE,
            $this->compiler->getWarningLevel()
        );
    }

    public function testSetFormattingOptions()
    {
        $this->assertInstanceOf(
            'Closure\Compiler\FormattingOptions',
            $this->compiler->getFormattingOptions()
        );

        $this->compiler->setFormattingOptions(new Compiler\FormattingOptions);
        $this->assertInstanceOf(
            'Closure\Compiler\FormattingOptions',
            $this->compiler->getFormattingOptions()
        );
    }

    public function testGetParams()
    {
        $this->assertCount(7, $this->compiler->getParams());
    }
}