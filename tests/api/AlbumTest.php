<?php
require_once __DIR__ . '/BaseNotAuthenticatedApiTest.php';


/**
 * Tests album api calls
 *
 * @author Marcos Peña
 */
class AlbumTest extends BaseNotAuthenticatedApiTest
{

    private $albumPackage;

    const ALBUM_TITLE = 'Milo Goes to College';
    const ALBUM_ARTIST = 'Descendents';

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        if (!$this->isApiInitiated()) {
            $this->initiateApi();
        }
        $this->albumPackage = $this->lastFmApi->getPackage($this->authentication, 'album');

        parent::__construct($name, $data, $dataName);
    }

    public function testGetExistingInfo()
    {
        $albumInfo = $this->albumPackage->getInfo(array(
            'album' => self::ALBUM_TITLE,
            'artist' => self::ALBUM_ARTIST));
        // Assert
        $this->assertArrayHasKey('name', $albumInfo);
    }

    public function testTracksInInfo()
    {
        $albumInfo = $this->albumPackage->getInfo(array(
            'album' => self::ALBUM_TITLE,
            'artist' => self::ALBUM_ARTIST));
        // Assert
        $this->assertArrayHasKey('tracks', $albumInfo);
    }

    public function testGetNonExistingInfo()
    {
        $albumInfo = $this->albumPackage->getInfo(array(
            'album' => 'afadsffadfadf',
            'artist' => 'daqfadfaldfa'));
        // Assert
        $this->assertFalse($albumInfo);
    }

    public function testSearch()
    {
        $searchResults = $this->albumPackage->search(array(
            'album' => self::ALBUM_TITLE)
        );
        $this->assertArrayHasKey('results', $searchResults);
    }
}
