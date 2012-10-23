<?php
/**
 * ClosureCompilerPHP
 *
 * @link      http://github.com/neeckeloo/ClosureCompilerPHP
 * @copyright Copyright (c) 2012 Nicolas Eeckeloo
 */
namespace Closure;

use Closure\Exception;

class HttpRequestHandler
{
    CONST METHOD_GET = 'GET';
    CONST METHOD_POST = 'POST';

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var string
     */
    protected $url;

    /**
     * @var integer
     */
    protected $port;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $headers = array(
        'js'   => 'Content-type: text/javascript',
        'xml'  => 'Content-type: text/xml',
        'json' => 'Content-type: application/json',
    );

    /**
     * @var string
     */
    protected $responseHeader;

    /**
     * @var string
     */
    protected $responseContent;

    /**
     * Constructor
     * 
     * @param array $data
     * @param array $settings
     */
    public function __construct(array $data, array $settings = array())
    {
        $this->setData($data);

        foreach ($settings as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method) && $key != 'data') {
                $this->{$method}($value);
            }
        }
    }

    /**
     * Sets data that will be passed with the request
     *
     * @param array $data
     * @return HttpRequestHandler
     * @throws Exception\InvalidArgumentException
     */
    public function setData(array $data)
    {
        if (empty($data)) {
            throw new Exception\InvalidArgumentException('The request arguments are not valid.');
        }

        $this->data = $data;
        
        return $this;
    }

    /**
     * Returns specified request data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Sets the url the request will use
     *
     * @param string $url
     * @return HttpRequestHandler
     */
    public function setUrl($url)
    {
        $this->url = (string) $url;

        return $this;
    }

    /**
     * Returns url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the TCP port the request will use
     *
     * @param integer $port
     * @return HttpRequestHandler
     */
    public function setPort($port)
    {
        $this->port = (int) $port;

        return $this;
    }

    /**
     * Returns port
     *
     * @return integer
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Sets the request method (GET or POST)
     *
     * @param string $method
     * @return HttpRequestHandler
     * @throws Exception\InvalidArgumentException
     */
    public function setMethod($method = self::METHOD_GET)
    {
        $method = strtoupper($method);

        if ($method != self::METHOD_GET && $method != self::METHOD_POST) {
            throw new Exception\InvalidArgumentException(sprtinf(
                'The request method "%s" is not valid.',
                $method
            ));
        }

        $this->method = (string) $method;
        
        return $this;
    }

    /**
     * Returns the request method
     * 
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Add a new request header
     * 
     * @param string $key
     * @param string $header
     * @return HttpRequestHandler
     */
    public function addHeader($key, $header)
    {
        $key = strtolower($key);

        if (!isset($this->headers[$key])) {
            $this->headers[$key] = $header;
        }
        
        return $this;
    }

    /**
     * Remove a specified request header
     *
     * @param string $key
     * @return HttpRequestHandler
     */
    public function removeHeader($key)
    {
        $key = strtolower($key);
        
        if (isset($this->headers[$key])) {
            unset($this->headers[$key]);
        }

        return $this;
    }

    /**
     * Returns a specified request header
     *
     * @param string $key
     * @return string
     */
    public function getHeader($key)
    {
        $key = strtolower($key);

        if (isset($this->headers[$key])) {
            return $this->headers[$key];
        }
    }

    /**
     * Returns the header included in the response
     *
     * @return string
     */
    public function getResponseHeader()
    {
        return $this->responseHeader;
    }

    /**
     * Returns the content included in the response
     *
     * @return string
     */
    public function getReponseContent()
    {
        return $this->responseContent;
    }

    /**
     * Parse and encode data
     *
     * @return string
     */
    protected function encodeData()
    {
        $data = array();
        foreach ($this->data as $key => $value) {
            $key = preg_replace('/_[0-9]$/', '', $key);
            $data[] = '&' . $key . '=' . urlencode($value);
        }

        return implode('&', $data);
    }

    /**
     * Send an HTTP request to the specified URL and TCP port
     *
     * @return string
     * @throws HttpRequestHandlerException
     */
    public function sendRequest()
    {
        // Parse the given URL
        $url = parse_url($this->url);
        if (!isset($url['host']) || !isset($url['path'])) {
            throw new Exception\InvalidArgumentException('No host or path specified.');
        }

        $host = $url['host'];
        $path = $url['path'];

        // Open a socket connection on the specified TCP port
        if (!$fp = fsockopen($host, $this->port)) {
            throw new Exception\RuntimeException(sprtinf(
                'Error opening socket connection to the url "%s" on port "%s".',
                $this->port,
                $this->url
            ));
        }
        
        // Parse and urlencode the request data
        $data = $this->encodeData();

        fputs($fp, "$this->method $path HTTP/1.0\r\n");
        fputs($fp, "Host: $host");
        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-length: ". strlen($data) . "\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $data);

        $response = '';
        while(!feof($fp)) {
            $response .= fgets($fp, 128);
        }

        // Close the socket connection
        fclose($fp);

        // Process the response
        $responseParts = explode("\r\n\r\n", $response, 2);
        
        $this->responseHeader = $responseParts[0];
        $this->responseContent = $responseParts[1];

        return $this->responseContent;
    }

    /**
     * Send the specified header
     * 
     * @param string $key
     */
    public function sendHeader($key)
    {
        $header = $this->getHeader($key);
        if ($header) {
            header($header);
        }
    }
}