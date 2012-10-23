<?php
/**
 * ClosureCompilerPHP
 *
 * @link      http://github.com/neeckeloo/ClosureCompilerPHP
 * @copyright Copyright (c) 2012 Nicolas Eeckeloo
 */
namespace Closure;

class RemoteCompiler extends AbstractCompiler
{
    /**
     * @var HttpRequestHandler
     */
    protected $requestHandler;

    /**
     * Sets request handler
     *
     * @param HttpRequestHandler $handler
     * @return RemoteCompiler
     */
    public function setRequestHandler($handler)
    {
        $this->requestHandler = $handler;

        $this->requestHandler->setUrl('http://closure-compiler.appspot.com/compile')
            ->setPort(80)
            ->setMethod(HttpRequestHandler::METHOD_POST);

        return $this;
    }

    /**
     * Returns request handler
     * 
     * @return HttpRequestHandler
     */
    public function getRequestHandler()
    {
        if (!isset($this->requestHandler)) {
            $this->setRequestHandler(new HttpRequestHandler());
        }

        return $this->requestHandler;
    }

    /**
     * Compile Javascript code
     * 
     * @return string
     */
    public function compile()
    {
        $requestHandler = $this->getRequestHandler();
        $requestHandler->setData($this->getParams());

        return $requestHandler->sendRequest();
    }
}