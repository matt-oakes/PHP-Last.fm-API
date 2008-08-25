<?php

class lastfmApiAlbum extends lastfmApiBase {
	public $info;
	public $tags;
	
	private $apiKey;
	private $artist;
	private $album;
	private $mbid;
	private $auth;
	
	function __construct($apiKey, $artist = '', $album = '', $mbid = '') {
		$this->apiKey = $apiKey;
		$this->artist = $artist;
		$this->album = $album;
		$this->mbid = $mbid;
	}
	
	public function addTags($tags, $sessionKey, $secret) {
		$vars = array(
			'method' => 'album.addtags',
			'api_key' => $this->apiKey,
			'album' => $this->album,
			'artist' => $this->artist,
			'tags' => $tags,
			'sk' => $sessionKey
		);
		$sig = $this->apiSig($secret, $vars);
		$vars['api_sig'] = $sig;
		
		if ( $call = $this->apiPostCall($vars) ) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
	
	public function getInfo() {
		$vars = array(
			'method' => 'album.getinfo',
			'api_key' => $this->apiKey,
			'album' => $this->album,
			'artist' => $this->artist,
			'mbid' => $this->mbid
		);
		
		if ( $call = $this->apiGetCall($vars) ) {
			$this->info['name'] = (string) $call->album->name;
			$this->info['artist'] = (string) $call->album->artist;
			$this->info['lastfmid'] = (string) $call->album->id;
			$this->info['mbid'] = (string) $call->album->mbid;
			$this->info['url'] = (string) $call->album->url;
			$this->info['releasedate'] = strtotime(trim((string) $call->album->releasedate));
			$this->info['image']['small'] = (string) $call->album->image;
			$this->info['image']['medium'] = (string) $call->album->image[1];
			$this->info['image']['large'] = (string) $call->album->image[2];
			$this->info['listeners'] = (string) $call->album->listeners;
			$this->info['playcount'] = (string) $call->album->playcount;
			$i = 0;
			foreach ( $call->album->toptags->tag as $tags ) {
				$this->info['toptags'][$i]['name'] = (string) $tags->name;
				$this->info['toptags'][$i]['url'] = (string) $tags->url;
				$i++;
			}
			
			return $this->info;
		}
		else {
			return FALSE;
		}
	}
	
	public function getTags($sessionKey, $secret) {
		$vars = array(
			'method' => 'album.gettags',
			'api_key' => $this->apiKey,
			'sk' => $sessionKey,
			'album' => $this->album,
			'artist' => $this->artist
		);
		$sig = $this->apiSig($secret, $vars);
		$vars['api_sig'] = $sig;
		
		if ( $call = $this->apiGetCall($vars) ) {
			if ( count($call->tags->tag) > 0 ) {
				$i = 0;
				foreach ( $call->tags->tag as $tag ) {
					$this->tags[$i]['name'] = (string) $tag->name;
					$this->tags[$i]['url'] = (string) $tag->url;
					$i++;
				}
				
				return $this->tags;
			}
			else {
				$this->handleError(90, 'Artist has no tags from this user');
				return FALSE;
			}
		}
		else {
			return FALSE;
		}
	}
}

?>