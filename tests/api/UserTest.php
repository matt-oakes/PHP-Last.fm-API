<?php
require_once __DIR__ . '/BaseNotAuthenticatedApiTest.php';

/**
 * Tests user api calls
 *
 * @author Marcos Peña
 */
class UserTest extends BaseNotAuthenticatedApiTest
{

    private $userPackage;

    const USERNAME_NAME = 'RJ';

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        if (!$this->isApiInitiated()) {
            $this->initiateApi();
        }
        $this->userPackage = $this->lastFmApi->getPackage($this->authentication, 'user');

        parent::__construct($name, $data, $dataName);
    } 

    public function testFriends()
    {
        $result = $this->userPackage->getFriends(array(
            'user' => self::USERNAME_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }    

    public function testLovedTracks()
    {
        $result = $this->userPackage->getLovedTracks(array(
            'user' => self::USERNAME_NAME)
        );
        $this->assertNotEmpty($result);
    }    

    public function testRecentTracks()
    {
        $result = $this->userPackage->getRecentTracks(array(
            'user' => self::USERNAME_NAME)
        );
        $this->assertNotEmpty($result);
    }    

    public function testTopAlbums()
    {
        $result = $this->userPackage->getTopAlbums(array(
            'user' => self::USERNAME_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }    

    public function testTopArtists()
    {
        $result = $this->userPackage->getTopArtists(array(
            'user' => self::USERNAME_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }    
    
    /**
     *  AS of march 15 is returning an empty array no matter what tag is passed
     */
    public function testTopTags()
    {
        $result = $this->userPackage->getTopTags(array(
            'user' => self::USERNAME_NAME,
            'limit' => 1)
        );
        $this->assertFalse($result);
    }
    
    public function testTopTracks()
    {
        $result = $this->userPackage->getTopTracks(array(
            'user' => self::USERNAME_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }      
    
    public function testWeeklyAlbumChart()
    {
        $result = $this->userPackage->getWeeklyAlbumChart(array(
            'user' => self::USERNAME_NAME)
        );
        $this->assertNotEmpty($result);
    }      
    
    public function testWeeklyChartList()
    {
        $result = $this->userPackage->getWeeklyChartList(array(
            'user' => self::USERNAME_NAME)
        );
        $this->assertNotEmpty($result);
    }      
    public function testWeeklyTrackChart()
    {
        $result = $this->userPackage->getWeeklyTrackChart(array(
            'user' => self::USERNAME_NAME)
        );
        $this->assertNotEmpty($result);
    }      

}
