<?php

namespace Tests\Api;

use LastFmApi\Api\TagApi;

/**
 * Tests tag api calls
 *
 * @author Marcos Peña
 */
class TagTest extends BaseNotAuthenticatedApiTest
{

    private $tagApi;

    const TAG_NAME = 'hardcore';

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        if (!$this->isApiInitiated()) {
            $this->initiateApi();
        }
        parent::__construct($name, $data, $dataName);
        $this->tagApi = new TagApi($this->authentication);
    }

    /**
     *  AS of march 15 is returning an empty array no matter what tag is passed
     */
    public function testSimilar()
    {
        $result = $this->tagApi->getSimilar(array(
            'tag' => self::TAG_NAME,
            'limit' => 1)
        );

        $this->assertTrue(is_array($result));
    }

    public function testTopAlbums()
    {
        $result = $this->tagApi->getTopAlbums(array(
            'tag' => self::TAG_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }

    public function testTopArtists()
    {
        $result = $this->tagApi->getTopArtists(array(
            'tag' => self::TAG_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }

    public function testTopTags()
    {
        $result = $this->tagApi->getTopTags();
        $this->assertNotEmpty($result);
    }

    public function testTopTracks()
    {
        $result = $this->tagApi->getTopTracks(array(
            'tag' => self::TAG_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }

    public function testWeeklyChartList()
    {
        $result = $this->tagApi->getWeeklyChartList(array(
            'tag' => self::TAG_NAME)
        );
        $this->assertNotEmpty($result);
    }

}
