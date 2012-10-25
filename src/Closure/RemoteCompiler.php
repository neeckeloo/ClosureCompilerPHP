<?php
/**
 * ClosureCompilerPHP
 *
 * @link      http://github.com/neeckeloo/ClosureCompilerPHP
 * @copyright Copyright (c) 2012 Nicolas Eeckeloo
 */
namespace Closure;

use Closure\Compiler\Response as CompilerResponse;
use Closure\Compiler\Response\Error as CompilerResponseError;

class RemoteCompiler extends AbstractCompiler
{
    /**
     * @var string 
     */
    protected $url = 'http://closure-compiler.appspot.com/compile';

    /**
     * @var integer
     */
    protected $port = 80;

    /**
     * @var string
     */
    protected $method = HttpRequestHandler::METHOD_POST;

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

        $this->requestHandler->setUrl($this->url)
            ->setPort($this->port)
            ->setMethod($this->method);

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
     * Parse response xml
     *
     * @param \SimpleXMLElement $xml
     * @return array
     */
    protected function parseXml($xml)
    {
        $data = array();
        
        foreach ($xml->children() as $name => $child) {
            if (count($child->children()) > 0) {
                $value = $this->parseXml($child);
            } else {
                $value = (string) $child;
            }

            $node = array(
                'tag'   => $name,
                'value' => $value
            );

            foreach ($child->attributes() as $name => $value) {
                $node['attributes'][$name] = (string) $value[0];
            }
            
            $data[] = $node;
        }

        return $data;
    }

    /**
     * Build response object from compiler response data
     *
     * @param array $data
     * @return array
     */
    protected function buildResponse($data)
    {
        $response = $this->getCompilerResponse();

        foreach ($data as $item) {
            if (!isset($item['tag']) && !isset($item['value'])) {
                continue;
            }

            if (isset($item['tag']) && ($item['tag'] == 'errors' || $item['tag'] == 'warnings')) {
                foreach ($item['value'] as $error) {
                    $attributes = $error['attributes'];
                    
                    $error = new CompilerResponseError($error['value'], array(
                        'type' => $attributes['type'],
                        'file' => $attributes['file'],
                        'line' => $attributes['lineno'],
                        'char' => $attributes['charno'],
                        'code' => $attributes['line'],
                    ));

                    if ($item['tag'] == 'errors') {
                        $response->addError($error);
                    } else {
                        $response->addWarning($error);
                    }
                }
            } elseif (is_array($item['value'])) {
                $this->buildResponse($item['value']);
            } else {
                $method = 'set' . ucfirst($item['tag']);
                if (method_exists($response, $method)) {
                    call_user_func_array(array($response, $method), array($item['value']));
                }
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
        $data = $this->parseXml($xml);

        return $this->buildResponse($data);
    }
}