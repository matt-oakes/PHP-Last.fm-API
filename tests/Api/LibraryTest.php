<?php

namespace Tests\Api;

use LastFmApi\Api\LibraryApi;

/**
 * Tests library api calls
 *
 * @author Marcos Peña
 */
class LibraryTest extends BaseNotAuthenticatedApiTest
{

    private $libraryApi;

    const USERNAME_NAME = 'devilcius';

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        if (!$this->isApiInitiated()) {
            $this->initiateApi();
        }
        parent::__construct($name, $data, $dataName);
        $this->libraryApi = new LibraryApi($this->authentication);
    }

    public function testArtists()
    {
        $result = $this->libraryApi->getArtists(array(
            'user' => self::USERNAME_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }

}
