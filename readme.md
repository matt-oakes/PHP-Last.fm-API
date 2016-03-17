PHP Last.FM API
===============
Thank you for using PHP Last.FM API!

You will need your own API key by registering at: http://www.last.fm/api

# Usage
_:exclamation: Only read calls are tested (2016-03-17)_

### PSR-4
Add `/src/lastfmapi` to classmap

```php
use LastFmApi\Api\AuthApi;
use LastFmApi\Api\ArtistApi;

class LastFm
{
    private $apiKey;
    private $artistApi;

    public function __construct()
    {
        $this->apiKey = 'apikeyfromlastfm'; //required
        $auth = new AuthApi('setsession', array('apiKey' => $this->apiKey));
        $this->artistApi = new ArtistApi($auth);
    }
    public function getBio($artist)
    {
        $artistInfo = $this->artistApi->getInfo(array("artist" => $artist));

        return $artistInfo['bio'];
    }	
}
``` 

Enjoy!
Matt Oakes