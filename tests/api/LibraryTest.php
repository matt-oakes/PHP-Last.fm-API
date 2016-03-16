<?php
require_once __DIR__ . '/BaseNotAuthenticatedApiTest.php';

/**
 * Tests library api calls
 *
 * @author Marcos Peña
 */
class LibraryTest extends BaseNotAuthenticatedApiTest
{

    private $libraryPackage;

    const USERNAME_NAME = 'devilcius';

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        if (!$this->isApiInitiated()) {
            $this->initiateApi();
        }
        $this->libraryPackage = $this->lastFmApi->getPackage($this->authentication, 'library');

        parent::__construct($name, $data, $dataName);
    } 

    public function testArtists()
    {
        $result = $this->libraryPackage->getArtists(array(
            'user' => self::USERNAME_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }    

}
