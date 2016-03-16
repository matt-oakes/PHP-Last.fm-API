<?php
require_once __DIR__ . '/BaseNotAuthenticatedApiTest.php';

/**
 * Tests tag api calls
 *
 * @author Marcos Peña
 */
class TagTest extends BaseNotAuthenticatedApiTest
{

    private $tagPackage;

    const TAG_NAME = 'hardcore';

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        if (!$this->isApiInitiated()) {
            $this->initiateApi();
        }
        $this->tagPackage = $this->lastFmApi->getPackage($this->authentication, 'tag');

        parent::__construct($name, $data, $dataName);
    } 

    /**
     *  AS of march 15 is returning an empty array no matter what tag is passed
     */
    public function testSimilar()
    {
        $result = $this->tagPackage->getSimilar(array(
            'tag' => self::TAG_NAME,
            'limit' => 1)
        );

        $this->assertTrue(is_array($result));
    }    

    public function testTopAlbums()
    {
        $result = $this->tagPackage->getTopAlbums(array(
            'tag' => self::TAG_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }       

    public function testTopArtists()
    {
        $result = $this->tagPackage->getTopArtists(array(
            'tag' => self::TAG_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }       

    public function testTopTags()
    {
        $result = $this->tagPackage->getTopTags();
        $this->assertNotEmpty($result);
    }     
    
    public function testTopTracks()
    {
        $result = $this->tagPackage->getTopTracks(array(
            'tag' => self::TAG_NAME,
            'limit' => 1)
        );
        $this->assertNotEmpty($result);
    }       
    
    public function testWeeklyChartList()
    {
        $result = $this->tagPackage->getWeeklyChartList(array(
            'tag' => self::TAG_NAME)
        );
        $this->assertNotEmpty($result);
    }       
}
