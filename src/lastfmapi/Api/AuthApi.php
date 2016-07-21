<?php

namespace LastFmApi\Api;

use LastFmApi\Exception\InvalidArgumentException;

/**
 * File that stores api calls for getting authentication values
 */

/**
 * Allows access to the api requests relating to authentication
 */
class AuthApi extends BaseApi
{

    /**
     * Stores the api key
     * @access public
     * @var string
     */
    public $apiKey;

    /**
     * Stores the secret
     * @access public
     * @var string
     */
    public $apiSecret;

    /**
     * Stores the authenticated username
     * @access public
     * @var string
     */
    public $username;

    /**
     * Stores the session key
     * @access public
     * @var string
     */
    public $sessionKey;

    /**
     * Stores the users subscriber status
     * @access public
     * @var boolean
     */
    public $subscriber;

    /**
     * Stores the authentication token
     * @access private
     * @var string
     */
    public $token;

    /**
     * Run when the class is created
     * @param string $method <i>getsession</i> to get a new session, <i>setsession</i> to set a current session
     * @param array $vars An array of variables to pass to the class
     * @return void
     * @access public
     */
    public function __construct($method, $vars)
    {
        if ($method == 'getsession') {
            if (!empty($vars['apiKey']) && !empty($vars['apiSecret']) && !empty($vars['token'])) {
                $this->apiKey = $vars['apiKey'];
                $this->apiSecret = $vars['apiSecret'];
                $this->token = $vars['token'];
                $this->getSession();
            } else {
                throw new InvalidArgumentException('Must send an apiKey, token and a secret in the call for getsession');
            }
        } elseif ($method == 'gettoken') {
            if (!empty($vars['apiKey']) && !empty($vars['apiSecret'])) {
                $this->apiKey = $vars['apiKey'];
                $this->apiSecret = $vars['apiSecret'];
                $this->getToken();
            } else {
                throw new InvalidArgumentException('Must send an apiKey and a secret in the call for gettoken');
            }
        } elseif ($method == 'setsession') {
            if (!empty($vars['apiKey'])) {
                $this->apiKey = $vars['apiKey'];
                if (!empty($vars['apiSecret']) && !empty($vars['username']) && !empty($vars['sessionKey']) && isset($vars['subscriber'])) {
                    $this->apiSecret = $vars['apiSecret'];
                    $this->username = $vars['username'];
                    $this->sessionKey = $vars['sessionKey'];
                    $this->subscriber = $vars['subscriber'];
                }
            } else {
                throw new InvalidArgumentException('Must send an apiKey, secret, usernamne, subcriber and sessionKey in the call for setsession');
            }
        } else {
            throw new InvalidArgumentException('Incorrect use of method variable ("getsession" or "setsession")');
        }
    }

    /**
     * Internal method uses to get a new session via an api call
     * @return void
     * @access private
     */
    private function getSession()
    {
        $vars = array(
            'method' => 'auth.getSession',
            'api_key' => $this->apiKey,
            'token' => $this->token
        );
        $sig = $this->apiSig($this->apiSecret, $vars);
        $vars['api_sig'] = $sig;

        if ($call = $this->apiGetCall($vars)) {
            $this->username = (string) $call->session->name;
            $this->sessionKey = (string) $call->session->key;
            $this->subscriber = (string) $call->session->subscriber;
        } else {
            return false;
        }
    }

    /**
     * Internal method uses to get a new token via an api call
     * @return void
     * @access private
     */
    private function getToken()
    {
        $vars = array(
            'method' => 'auth.getToken',
            'api_key' => $this->apiKey
        );

        $sig = $this->apiSig($this->apiSecret, $vars);
        $vars['api_sig'] = $sig;

        if ($call = $this->apiGetCall($vars)) {
            $this->token = $call->token;
        } else {
            return false;
        }
    }

    /*
     * Generates the api signature for use in api calls that require write access
     * @access protected
     * @return string
     */

    protected function apiSig($apiSecret, $vars)
    {
        ksort($vars);

        $signature = '';
        foreach ($vars as $name => $value) {
            $signature .= $name . $value;
        }
        $signature .= $apiSecret;;
        $hashedSignature = md5($signature);

        return $hashedSignature;
    }

}
