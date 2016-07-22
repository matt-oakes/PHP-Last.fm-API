<?php

namespace Tests\Api;

use LastFmApi\Api\AuthApi;

/**
 * Base class for api tests
 *
 * @author Marcos Peña
 */
abstract class BaseAuthenticatedApiTest extends BaseApiTest
{

    protected $authentication;
    private $isApiInitiated = false;

    public function initiateApi()
    {
        $this->setUp();
        if (empty($this->apiKey)) {
            $this->fail("You must provide a valid apiKey!");
        }
        $this->authentication = new AuthApi('setsession', array(
            'apiKey' => $this->apiKey,
            'apiSecret' => $this->apiSecret,
            'sessionKey' => $this->sessionKey,
            'username' => $this->username,
            'subscriber' => 0
                )
        );
        $this->isApiInitiated = true;
    }

    public function isApiInitiated()
    {
        return $this->isApiInitiated;
    }

}
