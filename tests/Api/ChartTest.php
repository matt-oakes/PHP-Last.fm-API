<?php

namespace Tests\Api;

use LastFmApi\Api\ChartApi;

/**
 * Tests chart api calls
 *
 * @group notAuthenticated
 * @author Marcos Peña
 */
class ChartTest extends BaseNotAuthenticatedApiTest
{

    private $chartApi;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        if (!$this->isApiInitiated()) {
            $this->initiateApi();
        }
        parent::__construct($name, $data, $dataName);
        $this->chartApi = new ChartApi($this->authentication);
    }

    public function testTopArtists()
    {
        $result = $this->chartApi->getTopArtists(array(
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }

    public function testTopTags()
    {
        $result = $this->chartApi->getTopTags(array(
                'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }

    public function testTopTracks()
    {
        $result = $this->chartApi->getTopTracks(array(
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }

}
