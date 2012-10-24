<?php
/**
 * ClosureCompilerPHP
 *
 * @link      http://github.com/neeckeloo/ClosureCompilerPHP
 * @copyright Copyright (c) 2012 Nicolas Eeckeloo
 */
namespace Closure;

use Closure\Compiler\Response as CompilerResponse;

class RemoteCompiler extends AbstractCompiler
{
    /**
     * @var HttpRequestHandler
     */
    protected $requestHandler;

    /**
     *
     * @var CompilerResponse
     */
    protected $response;

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
     * Sets compiler response
     *
     * @param CompilerResponse $response
     * @return RemoteCompiler
     */
    public function setCompilerResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Returns compiler response
     *
     * @return CompilerResponse
     */
    public function getCompilerResponse()
    {
        if (!isset($this->response)) {
            $this->setCompilerResponse(new CompilerResponse());
        }

        return $this->response;
    }

    /**
     * Build response object parsing xml contained in the compiler response
     *
     * @param \SimpleXMLElement $xml
     * @return array
     */
    function buildResponse($xml)
    {
        $response = $this->getCompilerResponse();

        foreach ($xml->children() as $name => $child) {
            if (count($child->children()) > 0) {
                $this->buildResponse($child);
                continue;
            }

            $value = (string) $child;

            $method = 'set' . ucfirst($name);
            if (method_exists($response, $method)) {
                call_user_func_array(array($response, $method), array($value));
            }
        }

        return $response;
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

        $responseData = $requestHandler->sendRequest();

        $xml = new \SimpleXMLElement($responseData);

        return $this->buildResponse($xml);
    }
}