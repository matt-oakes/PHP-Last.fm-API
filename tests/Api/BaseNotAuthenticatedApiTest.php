<?php

namespace Tests\Api;

use LastFmApi\Api\AuthApi;

/**
 * Base class for api tests
 *
 * @author Marcos Peña
 */
abstract class BaseNotAuthenticatedApiTest extends \PHPUnit_Framework_TestCase
{

    protected $authentication;
    private $isApiInitiated = false;

    public function initiateApi()
    {
        $apiKey = '';
        if (empty($apiKey)) {
            throw new \Exception("You must provide a valid apiKey!");
        }
        $this->authentication = new AuthApi('setsession', array('apiKey' => $apiKey));
        $this->isApiInitiated = true;
    }

    public function isApiInitiated()
    {
        return $this->isApiInitiated;
    }

}
