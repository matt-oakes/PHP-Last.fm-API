<?php
require_once __DIR__ . '/BaseNotAuthenticatedApiTest.php';
/**
 * Tests track api calls
 *
 * @author Marcos Peña
 */
class TrackTest extends BaseNotAuthenticatedApiTest
{

    private $trackPackage;

    const TRACK_NAME= 'When I get the time';
    const ARTIST_NAME= 'Descendents';

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        if (!$this->isApiInitiated()) {
            $this->initiateApi();
        }
        $this->trackPackage = $this->lastFmApi->getPackage($this->authentication, 'track');

        parent::__construct($name, $data, $dataName);
    }

    public function testInfo()
    {
        $result = $this->trackPackage->getInfo(array(
            'artist' => self::ARTIST_NAME,
            'track' => self::TRACK_NAME)
        );
        $this->assertNotEmpty($result);
    }    

    public function testSimilar()
    {
        $result = $this->trackPackage->getSimilar(array(
            'track' => self::TRACK_NAME,
            'artist' => self::ARTIST_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }      

    public function testTopTags()
    {
        $result = $this->trackPackage->getTopTags(array(
            'track' => self::TRACK_NAME,
            'artist' => self::ARTIST_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }    

    public function testSearch()
    {
        $result = $this->trackPackage->search(array(
            'track' => self::TRACK_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }    

}
