<?php
require_once __DIR__ . '/BaseNotAuthenticatedApiTest.php';

/**
 * Tests geo api calls
 *
 * @author Marcos Peña
 */
class GeoTest extends BaseNotAuthenticatedApiTest
{

    private $geoPackage;

    const COUNTRY_NAME = 'Spain';

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        if (!$this->isApiInitiated()) {
            $this->initiateApi();
        }
        $this->geoPackage = $this->lastFmApi->getPackage($this->authentication, 'geo');

        parent::__construct($name, $data, $dataName);
    }

    public function testTopArtist()
    {
        $result = $this->geoPackage->getTopArtists(array(
            'country' => self::COUNTRY_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }    

    public function testTopTracks()
    {
        $result = $this->geoPackage->getTopTracks(array(
            'country' => self::COUNTRY_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }    

}
