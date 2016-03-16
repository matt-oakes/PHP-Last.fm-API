<?php
require_once __DIR__ . '/BaseNotAuthenticatedApiTest.php';

/**
 * Tests artist api calls
 *
 * @author Marcos Peña
 */
class ArtistTest extends BaseNotAuthenticatedApiTest
{

    private $artistPackage;

    const ARTIST_NAME = 'Descendents';

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        if (!$this->isApiInitiated()) {
            $this->initiateApi();
        }
        $this->artistPackage = $this->lastFmApi->getPackage($this->authentication, 'artist');

        parent::__construct($name, $data, $dataName);
    }

    public function testGetExistingInfo()
    {
        $albumInfo = $this->artistPackage->getInfo(array(
            'artist' => self::ARTIST_NAME));
        // Assert
        $this->assertArrayHasKey('name', $albumInfo);
    }

    public function testGetNonExistingInfo()
    {
        $albumInfo = $this->artistPackage->getInfo(array(
            'artist' => 'daqfadfaldfa'));
        // Assert
        $this->assertFalse($albumInfo);
    }

    public function testSearch()
    {
        $searchResults = $this->artistPackage->search(array(
            'artist' => self::ARTIST_NAME)
        );
        $this->assertArrayHasKey('results', $searchResults);
    }
    
    public function testGetImages()
    {
        $result = $this->artistPackage->getImages(array(
            'artist' => self::ARTIST_NAME)
        );
        $this->assertArrayHasKey('image', $result);
    }
    
    public function testSimilar()
    {
        $result = $this->artistPackage->getSimilar(array(
            'artist' => self::ARTIST_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }
    
    public function testTopAlbums()
    {
        $result = $this->artistPackage->getTopAlbums(array(
            'artist' => self::ARTIST_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }
    
    public function testTopTags()
    {
        $result = $this->artistPackage->getTopTags(array(
            'artist' => self::ARTIST_NAME)
        );
        
        $this->assertNotEmpty($result);
    }
    
    public function testTopTracks()
    {
        $result = $this->artistPackage->getTopTracks(array(
            'artist' => self::ARTIST_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }    

}
