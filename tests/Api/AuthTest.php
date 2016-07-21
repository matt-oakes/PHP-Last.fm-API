<?php

namespace Tests\Api;

use LastFmApi\Api\AuthApi;
use LastFmApi\Exception\ApiFailedException;

/**
 * Tests geo api calls
 *
 * @author Marcos Peña
 */
class AuthTest extends \PHPUnit_Framework_TestCase
{

    private $apiKey = '';
    private $apiSecret = '';
    private $token;
    protected $authentication;

    public function testGetToken()
    {
        $this->setToken();
        $this->assertNotEmpty($this->token);
    }

    /**
     * Token authentication is broken as july 2016
     */
    public function testGetSession()
    {

        $this->setToken();
        try {
            new AuthApi('getsession', array(
                'apiKey' => $this->apiKey,
                'apiSecret' => $this->apiSecret,
                'token' => $this->token
            ));
            $this->fail("Good news: expected token authentication to be broken!");
        } catch (ApiFailedException $error) {            
            $this->assertEquals(14, $error->getCode());
            $this->assertEquals("Unauthorized Token - This token has not been authorized", $error->getMessage());
        }
    }

    private function setToken()
    {
        if (empty($this->apiKey) || empty($this->apiSecret)) {

            $this->fail("You must provide a valid apiKey and a valid apiToken!");
        }
        $authentication = new AuthApi('gettoken', array(
            'apiKey' => $this->apiKey,
            'apiSecret' => $this->apiSecret
        ));

        $this->token = $authentication->token;
    }

}
