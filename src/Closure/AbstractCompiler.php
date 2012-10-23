<?php
/**
 * ClosureCompilerPHP
 *
 * @link      http://github.com/neeckeloo/ClosureCompilerPHP
 * @copyright Copyright (c) 2012 Nicolas Eeckeloo
 */
namespace Closure;

use Closure\Compiler\FormattingOptions;

abstract class AbstractCompiler implements Compiler\CompilerInterface
{
    const MODE_WHITESPACE_ONLY = 'WHITESPACE_ONLY';
    const MODE_SIMPLE_OPTIMIZATIONS = 'SIMPLE_OPTIMIZATIONS';
    const MODE_ADVANCED_OPTIMIZATIONS = 'ADVANCED_OPTIMIZATIONS';

    const OUTPUT_FORMAT_XML = 'xml';
    const OUTPUT_FORMAT_JSON = 'json';
    const OUTPUT_FORMAT_TEXT = 'text';

    const WARNING_LEVEL_DEFAULT = 'default';
    const WARNING_LEVEL_QUIET = 'quiet';
    const WARNING_LEVEL_VERBOSE = 'verbose';

    /**
     * @var string
     */
    protected $mode = self::MODE_WHITESPACE_ONLY;

    /**
     * Available modes
     *
     * @var array
     */
    protected $availableModes = array(
        self::MODE_WHITESPACE_ONLY,
        self::MODE_SIMPLE_OPTIMIZATIONS,
        self::MODE_ADVANCED_OPTIMIZATIONS,
    );

    /**
     * @var string
     */
    protected $outputFormat = self::FORMAT_XML;

    /**
     * Available output formats
     *
     * @var array
     */
    protected $availableOutputFormats = array(
        self::OUTPUT_FORMAT_XML,
        self::OUTPUT_FORMAT_JSON,
        self::OUTPUT_FORMAT_TEXT,
    );

    /**
     * @var string
     */
    protected $warningLevel = self::WARNING_LEVEL_DEFAULT;

    /**
     * Available warning levels
     *
     * @var array
     */
    protected $availableWarningLevels = array(
        self::WARNING_LEVEL_DEFAULT,
        self::WARNING_LEVEL_QUIET,
        self::WARNING_LEVEL_VERBOSE,
    );

    /**
     * @var FormattingOptions
     */
    protected $formattingOptions;

    /**
     * @var array
     */
    protected $files = array();

    /**
     * @var array
     */
    protected $scripts = array();

    /**
     * Sets mode
     *
     * @param string $mode
     * @return RemoteCompiler
     */
    public function setMode($mode = self::MODE_WHITESPACE_ONLY)
    {
        if (!in_array($mode, $this->availableModes)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'The mode "%s" is not available.',
                $mode
            ));
        }

        $this->mode = (string) $mode;

        return $this;
    }

    /**
     * Returns mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Sets output format
     *
     * @param string $format
     * @return RemoteCompiler
     */
    public function setOutputFormat($format = self::OUTPUT_FORMAT_XML)
    {
        if (!in_array($format, $this->availableOutputFormats)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'The output format "%s" is not available.',
                $format
            ));
        }

        $this->outputFormat = (string) $format;

        return $this;
    }

    /**
     * Returns output format
     *
     * @return string
     */
    public function getOutputFormat()
    {
        return $this->outputFormat;
    }

    /**
     * Sets warning level
     *
     * @param string $level
     * @return RemoteCompiler
     */
    public function setWarningLevel($level = self::WARNING_LEVEL_DEFAULT)
    {
        if (!in_array($level, $this->availableWarningLevels)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'The warning level "%s" is not available.',
                $level
            ));
        }

        $this->warningLevel = (string) $level;

        return $this;
    }

    /**
     * Returns warning level
     *
     * @return string
     */
    public function getWarningLevel()
    {
        return $this->warningLevel;
    }

    /**
     * Sets formatting options
     *
     * @param  FormattingOptions $options
     * @return AbstractCompiler
     */
    public function setFormattingOptions(FormattingOptions $options)
    {
        $this->formattingOptions = $options;

        return $this;
    }

    /**
     * Returns formatting options
     *
     * @return FormattingOptions
     */
    public function getFormattingOptions()
    {
        if (!isset($this->formattingOptions)) {
            $this->setFormattingOptions(new FormattingOptions());
        }

        return $this->formattingOptions;
    }

    /**
     * Add script
     *
     * @param string $script
     * @return AbstractCompiler
     */
    public function addScript($script)
    {
        $this->scripts[] = (string) $script;

        return $this;
    }

    /**
     * Add local javascript file
     *
     * @param string $file
     * @return AbstractCompiler
     */
    public function addLocalFile($file)
    {
        if (!file_exists($file)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'The file "%s" does not exists.',
                $file
            ));
        }

        $this->scripts[] = file_get_contents($file);

        return $this;
    }

    /**
     * Add remote javascript file
     *
     * @param string $url
     * @return AbstractCompiler
     */
    public function addRemoteFile($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'The url "%s" is not valid.',
                $url
            ));
        }

        $this->files[] = (string) $url;

        return $this;
    }

    /**
     * Returns compiler params
     *
     * @return array
     */
    public function getParams()
    {
        $params = array(
            'compilation_level' => $this->getMode(),
            'output_format'     => $this->getOutputFormat(),
            'warning_level'     => $this->getWarningLevel(),
            'output_info_1'     => 'compiled_code',
            'output_info_2'     => 'statistics',
            'output_info_3'     => 'warnings',
            'output_info_4'     => 'errors',
        );

        $formattingOptions = $this->getFormattingOptions();

        if ($formattingOptions->getPrettyPrintEnabled()) {
            $params['formatting'] = 'pretty_print';
        }

        if ($formattingOptions->getPrintInputDelimiterEnabled()) {
            $params['formatting'] = 'print_input_delimiter';
        }

        if (count($this->scripts) > 0) {
            $params['js_code'] = implode("\n\n", $this->scripts);
        }

        foreach ($this->files as $key => $file) {
            $params['code_url_' . $key] = $file;
        }

        return $params;
    }

    /**
     * Compile Javascript code
     *
     * @return string
     * @throws Exception\RuntimeException
     */
    public function compile()
    {
        throw new Exception\RuntimeException('Compile method not implemented.');
    }
}