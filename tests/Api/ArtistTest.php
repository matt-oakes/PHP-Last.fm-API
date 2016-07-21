<?php

namespace Tests\Api;

use LastFmApi\Exception\ApiFailedException;
use LastFmApi\Api\ArtistApi;

/**
 * Tests artist api calls
 *
 * @author Marcos Peña
 */
class ArtistTest extends BaseNotAuthenticatedApiTest
{

    private $artistApi;

    const ARTIST_NAME = 'Descendents';

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        if (!$this->isApiInitiated()) {
            $this->initiateApi();
        }
        parent::__construct($name, $data, $dataName);
        $this->artistApi = new ArtistApi($this->authentication);
    }

    public function testGetExistingInfo()
    {
        $albumInfo = $this->artistApi->getInfo(array(
            'artist' => self::ARTIST_NAME));
        // Assert
        $this->assertArrayHasKey('name', $albumInfo);
    }

    public function testGetNonExistingInfo()
    {
        try {
            $this->artistApi->getInfo(array(
                'artist' => 'daqfadfaldfa'));
            $this->fail("Expected Artist not found exception not thrown");
        } catch (ApiFailedException $error) {
            $this->assertEquals(6, $error->getCode());
            $this->assertEquals("The artist you supplied could not be found", $error->getMessage());
        }
    }

    public function testSearch()
    {
        $searchResults = $this->artistApi->search(array(
            'artist' => self::ARTIST_NAME)
        );
        $this->assertArrayHasKey('results', $searchResults);
    }

    public function testGetImages()
    {
        $result = $this->artistApi->getImages(array(
            'artist' => self::ARTIST_NAME)
        );
        $this->assertArrayHasKey('image', $result);
    }

    public function testSimilar()
    {
        $result = $this->artistApi->getSimilar(array(
            'artist' => self::ARTIST_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }

    public function testTopAlbums()
    {
        $result = $this->artistApi->getTopAlbums(array(
            'artist' => self::ARTIST_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }

    public function testTopTags()
    {
        $result = $this->artistApi->getTopTags(array(
            'artist' => self::ARTIST_NAME)
        );

        $this->assertNotEmpty($result);
    }

    public function testTopTracks()
    {
        $result = $this->artistApi->getTopTracks(array(
            'artist' => self::ARTIST_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }

}
