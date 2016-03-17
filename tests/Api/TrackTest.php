<?php

namespace Tests\Api;

use LastFmApi\Api\TrackApi;

/**
 * Tests track api calls
 *
 * @author Marcos Peña
 */
class TrackTest extends BaseNotAuthenticatedApiTest
{

    private $trackApi;

    const TRACK_NAME = 'When I get the time';
    const ARTIST_NAME = 'Descendents';

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        if (!$this->isApiInitiated()) {
            $this->initiateApi();
        }
        parent::__construct($name, $data, $dataName);
        $this->trackApi = new TrackApi($this->authentication);
    }

    public function testInfo()
    {
        $result = $this->trackApi->getInfo(array(
            'artist' => self::ARTIST_NAME,
            'track' => self::TRACK_NAME)
        );
        $this->assertNotEmpty($result);
    }

    public function testSimilar()
    {
        $result = $this->trackApi->getSimilar(array(
            'track' => self::TRACK_NAME,
            'artist' => self::ARTIST_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }

    public function testTopTags()
    {
        $result = $this->trackApi->getTopTags(array(
            'track' => self::TRACK_NAME,
            'artist' => self::ARTIST_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }

    public function testSearch()
    {
        $result = $this->trackApi->search(array(
            'track' => self::TRACK_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }

}
