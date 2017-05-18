PHP Last.FM API
===============
Thank you for using PHP Last.FM API!

You will need your own API key by registering at: http://www.last.fm/api

# Installation
`composer require matto1990/lastfm-api`

# Usage
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

# Testing

To run phpunit successfully, a _.env_ file must be included in tests/Api:
```properties
lastfm_api_key=
lastfm_api_secret=
lastfm_token=
lastfm_session_key=
lastfm_username=
```
