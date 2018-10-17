<?php

namespace Tests\Api;

use LastFmApi\Api\GeoApi;

/**
 * Tests geo api calls
 *
 * @author Marcos Peña
 */
class GeoTest extends BaseNotAuthenticatedApiTest
{

    private $geoApi;

    const COUNTRY_NAME = 'Spain';

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        if (!$this->isApiInitiated()) {
            $this->initiateApi();
        }
        parent::__construct($name, $data, $dataName);
        $this->geoApi = new GeoApi($this->authentication);
    }

    public function testTopArtist()
    {
        $result = $this->geoApi->getTopArtists(array(
            'country' => self::COUNTRY_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }

    public function testTopTracks()
    {
        $result = $this->geoApi->getTopTracks(array(
            'country' => self::COUNTRY_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }

}
