<?php

namespace LastFmApi\Api;

use LastFmApi\Exception\ApiFailedException;
use LastFmApi\Exception\CacheException;
use LastFmApi\Exception\ConnectionException;
use LastFmApi\Exception\InvalidArgumentException;
use LastFmApi\Lib\Socket;
use LastFmApi\Lib\Cache;
use LastFmApi\Lib\ApiUtils;
use \SimpleXMLElement;

/**
 * File that contains all base methods used by all api calls
 */

/**
 * Stores the methods used by all api calls
 */
class BaseApi
{
    use ApiUtils;
    /*
     * Stores error details
     * @access public
     * @var array has two elements: <i>code</i> and <i>desc</i> which stores the error code and description respectivly
     */

    public $error;
    /*
     * Stores the connection status
     * @access public
     * @var boolean
     */
    public $connected;

    /*
     * Stores the host name
     * @access private
     * @var string
     */
    private $host;
    /*
     * Stores the port number
     * @access private
     * @var string
     */
    private $port;
    /*
     * Stores the raw api call response
     * @access private
     * @var string
     */
    private $response;
    /*
     * Stores the socket class
     * @access private
     * @var class
     */
    private $socket;
    /*
     * Stores the cache class
     * @access private
     * @var class
     */
    private $cache;
    /*
     * Stores the config
     * @access private
     * @var class
     */
    protected $config;

    /**
     * Stores the auth variables used in all api calls
     * @var array
     */
    protected $auth;

    /**
     * States if the user has full authentication to use api requests that modify data
     * @var boolean
     */
    protected $fullAuth;

    public function __construct($auth, $config = array())
    {
        $this->config = $config;
        if (empty($this->config)) {
            $this->config = array(
                'enabled' => false
            );
        }

        if (is_object($auth)) {
            if (!empty($auth->apiKey) && !empty($auth->apiSecret) && !empty($auth->username) && !empty($auth->sessionKey) && ($auth->subscriber == 0 || $auth->subscriber == 1)) {
                $this->fullAuth = true;
            } elseif (!empty($auth->apiKey)) {
                $this->fullAuth = false;
            } else {
                throw new InvalidArgumentException('Invalid auth class was passed to lastfmApi. You need to have at least an apiKey set');
            }
            $this->auth = $auth;
        } else {
            throw new InvalidArgumentException('You need to pass a lastfmApiAuth class as the first variable to this class');
        }
    }

    /**
     * 
     * @return boolean
     */
    public function getFullAuth()
    {
        return $this->fullAuth;
    }

    /**
     * 
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * 
     * @return array
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /*
     * Setup the socket to get the raw api call return
     * @access private
     * @return boolean
     */

    private function setup()
    {
        $this->host = 'ws.audioscrobbler.com';
        $this->port = 80;
        $this->connected = 0;

        $this->socket = new Socket($this->host, $this->port);
        if (!$this->socket->error_number && !$this->socket->error_string) {
            $this->connected = 1;
            return true;
        } else {
            throw new ConnectionException($this->socket->error_string);
        }
    }

    /*
     * Turns the raw response into an xml object
     * @access private
     * @return object
     */

    private function processResponse()
    {
        $xmlstr = '';
        $record = 0;
        foreach ($this->response as $line) {
            if ($record == 1) {
                $xmlstr .= $line;
            } elseif (substr($line, 0, 1) == '<') {
                $record = 1;
            } elseif (preg_match('/^HTTP\/1.[0-9]{1} ([5-9]{1}[0-9]{2}.*)/', $line, $matches)) {

                throw new ConnectionException($this->host . ': Service not available (' . trim($matches[1]) . ')');
            }
        }
        try {
            libxml_use_internal_errors(true);
            $xml = new SimpleXMLElement($xmlstr);
        } catch (\Exception $error) {

            throw new ConnectionException($error->getMessage());
        }
        if((string)$xml->attributes()->status === 'failed' )
        {
            throw new ApiFailedException($xml->error, intval($xml->error['code']));
        }
        if (!isset($error)) {
            // All is well :)
            return $xml;
        }
    }

    /*
     * Used in api calls that do not require write access. Returns an xml object
     * @access protected
     * @return object
     */

    protected function apiGetCall($vars)
    {
        $this->setup();
        if ($this->connected == 1) {
            $this->cache = new Cache($this->config);
            if (!empty($this->cache->error)) {
                throw new CacheException($this->cache->error);
            } else {
                if ($cache = $this->cache->get($vars)) {
                    // Cache exists
                    $this->response = $cache;
                    return $this->processResponse();
                } else {
                    // Cache doesnt exist
                    $url = '/2.0/?';
                    foreach ($vars as $name => $value) {
                        $url .= trim(urlencode($name)) . '=' . trim(urlencode($value)) . '&';
                    }
                    $url = substr($url, 0, -1);
                    $url = str_replace(' ', '%20', $url);

                    $out = "GET " . $url . " HTTP/1.0\r\n";
                    $out .= "Host: " . $this->host . "\r\n";
                    $out .= "\r\n";
                    $this->response = $this->socket->send($out, 'array');
                    $processedResponse = $this->processResponse();
                    $this->cache->set($vars, $this->response);
                    
                    return $processedResponse;
                }
            }
        } else {
            return false;
        }
    }

    /*
     * Used in api calls that require write access. Returns an xml object
     * @access protected
     * @return object
     */

    protected function apiPostCall($vars)
    {
        $this->setup();
        if ($this->connected == 1) {
            $url = '/2.0/';

            $data = '';
            foreach ($vars as $name => $value) {
                $data .= trim($name) . '=' . trim(urlencode($value)) . '&';
            }
            $data = substr($data, 0, -1);

            $out = "POST " . $url . " HTTP/1.1\r\n";
            $out .= "Host: " . $this->host . "\r\n";
            $out .= "Content-Length: " . strlen($data) . "\r\n";
            $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $out .= "\r\n";
            $out .= $data . "\r\n";
            $this->response = $this->socket->send($out, 'array');

            return $this->processResponse();
        } else {
            return false;
        }
    }
}
