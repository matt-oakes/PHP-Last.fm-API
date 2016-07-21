<?php

namespace Tests\Api;

use LastFmApi\Api\UserApi;

/**
 * Tests user api calls
 *
 * @author Marcos Peña
 */
class UserTest extends BaseNotAuthenticatedApiTest
{

    private $userApi;

    const USERNAME_NAME = 'RJ';

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        if (!$this->isApiInitiated()) {
            $this->initiateApi();
        }
        parent::__construct($name, $data, $dataName);
        $this->userApi = new UserApi($this->authentication);
    }

    public function testFriends()
    {
        $result = $this->userApi->getFriends(array(
            'user' => self::USERNAME_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }

    public function testLovedTracks()
    {
        $result = $this->userApi->getLovedTracks(array(
            'user' => self::USERNAME_NAME)
        );
        $this->assertNotEmpty($result);
    }

    public function testRecentTracks()
    {
        $result = $this->userApi->getRecentTracks(array(
            'user' => self::USERNAME_NAME)
        );
        $this->assertNotEmpty($result);
    }

    public function testTopAlbums()
    {
        $result = $this->userApi->getTopAlbums(array(
            'user' => self::USERNAME_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }

    public function testTopArtists()
    {
        $result = $this->userApi->getTopArtists(array(
            'user' => self::USERNAME_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }

    public function testTopTags()
    {
        $result = $this->userApi->getTopTags(array(
            'user' => self::USERNAME_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }

    public function testTopTracks()
    {
        $result = $this->userApi->getTopTracks(array(
            'user' => self::USERNAME_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }

    public function testWeeklyAlbumChart()
    {
        $result = $this->userApi->getWeeklyAlbumChart(array(
            'user' => self::USERNAME_NAME)
        );
        $this->assertNotEmpty($result);
    }

    public function testWeeklyChartList()
    {
        $result = $this->userApi->getWeeklyChartList(array(
            'user' => self::USERNAME_NAME)
        );
        $this->assertNotEmpty($result);
    }

    public function testWeeklyTrackChart()
    {
        $result = $this->userApi->getWeeklyTrackChart(array(
            'user' => self::USERNAME_NAME)
        );
        $this->assertNotEmpty($result);
    }

}
