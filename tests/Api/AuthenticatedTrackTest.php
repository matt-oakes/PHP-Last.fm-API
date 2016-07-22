<?php

namespace Tests\Api;

use LastFmApi\Api\TrackApi;

/**
 * Description of AuthenticatedTrackTest
 *
 * @author Marcos Peña
 */
class AuthenticatedTrackTest extends BaseAuthenticatedApiTest
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

    public function testLoveTrack()
    {
        $loved = $this->trackApi->love(array(
            'artist' => self::ARTIST_NAME,
            'track' => self::TRACK_NAME
        ));
        
        $this->assertTrue($loved);
    }
}
