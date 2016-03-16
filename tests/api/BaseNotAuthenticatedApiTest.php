<?php
\Composer\Autoload\includeFile(__DIR__ . '/../../lastfmapi/lastfmapi.php');
/**
 * Base class for api tests
 *
 * @author Marcos Peña
 */
abstract class BaseNotAuthenticatedApiTest extends \PHPUnit_Framework_TestCase
{
    protected $lastFmApi;
    protected $authentication;
    private $isApiInitiated = false;
    
    public function initiateApi()
    {
        $apiKey = '';
        if(empty($apiKey)) {
            throw new \Exception("You must provide a valid apiKey!");
        }
        $this->authentication = new lastfmApiAuth('setsession', array('apiKey' => $apiKey));
        $this->lastFmApi = new lastfmApi();
        $this->isApiInitiated = true;
    }
    
    public function isApiInitiated()
    {
        return $this->isApiInitiated;
    }
}
