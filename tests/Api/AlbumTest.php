<?php

namespace Tests\Api;

use LastFmApi\Api\AlbumApi;

/**
 * Tests album api calls
 *
 * @author Marcos Peña
 */
class AlbumTest extends BaseNotAuthenticatedApiTest
{

    private $albumApi;

    const ALBUM_TITLE = 'Milo Goes to College';
    const ALBUM_ARTIST = 'Descendents';

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        if (!$this->isApiInitiated()) {
            $this->initiateApi();
        }
        parent::__construct($name, $data, $dataName);
        $this->albumApi = new AlbumApi($this->authentication);
    }

    public function testGetExistingInfo()
    {
        $albumInfo = $this->albumApi->getInfo(array(
            'album' => self::ALBUM_TITLE,
            'artist' => self::ALBUM_ARTIST));
        // Assert
        $this->assertArrayHasKey('name', $albumInfo);
    }

    public function testTracksInInfo()
    {
        $albumInfo = $this->albumApi->getInfo(array(
            'album' => self::ALBUM_TITLE,
            'artist' => self::ALBUM_ARTIST));
        // Assert
        $this->assertArrayHasKey('tracks', $albumInfo);
    }

    public function testGetNonExistingInfo()
    {
        $albumInfo = $this->albumApi->getInfo(array(
            'album' => 'afadsffadfadf',
            'artist' => 'daqfadfaldfa'));
        // Assert
        $this->assertFalse($albumInfo);
    }

    public function testSearch()
    {
        $searchResults = $this->albumApi->search(array(
            'album' => self::ALBUM_TITLE)
        );
        $this->assertArrayHasKey('results', $searchResults);
    }

}
