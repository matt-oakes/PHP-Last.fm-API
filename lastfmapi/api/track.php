<?php

class lastfmApiTrack extends lastfmApiBase {
	public $similar;
	public $topFans;
	public $topTags;
	public $searchResults;
	
	private $apiKey;
	private $track;
	private $artist;
	private $mbid;
	
	function __construct($apiKey, $track, $artist) {
		$this->apiKey = $apiKey;
		$this->track = $track;
		$this->artist = $artist;
	}
	
	public function getTags($sessionKey, $secret) {
		$vars = array(
			'method' => 'track.gettags',
			'api_key' => $this->apiKey,
			'sk' => $sessionKey,
			'track' => $this->track,
			'artist' => $this->artist
		);
		$sig = $this->apiSig($secret, $vars);
		$vars['api_sig'] = $sig;
		
		$call = $this->apiGetCall($vars);
		
		if ( $call['status'] == 'ok' ) {
			if ( count($call->tags->tag) > 0 ) {
				$this->tags['artist'] = (string) $call->tags['artist'];
				$this->tags['track'] = (string) $call->tags['track'];
				$i = 0;
				foreach ( $call->tags->tag as $tag ) {
					$this->tags['tags'][$i]['name'] = (string) $tag->name;
					$this->tags['tags'][$i]['url'] = (string) $tag->url;
					$i++;
				}
				
				return $this->tags;
			}
			else {
				$this->error['code'] = 90;
				$this->error['desc'] = 'The user has no tags on this track';
				return FALSE;
			}
		}
		elseif ( $call['status'] == 'failed' ) {
			// Fail with error code
			$this->error['code'] = $call->error['code'];
			$this->error['desc'] = $call->error;
			return FALSE;
		}
		else {
			//Hard failure
			$this->error['code'] = 0;
			$this->error['desc'] = 'Unknown error';
			return FALSE;
		}
	}
}

?>