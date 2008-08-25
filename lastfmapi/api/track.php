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
	
	function __construct($apiKey, $track = '', $artist = '') {
		$this->apiKey = $apiKey;
		$this->track = $track;
		$this->artist = $artist;
	}
	
	public function addTags($tags, $sessionKey, $secret) {
		$vars = array(
			'method' => 'track.addtags',
			'api_key' => $this->apiKey,
			'artist' => $this->artist,
			'track' => $this->track,
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
	
	public function ban($sessionKey, $secret) {
		$vars = array(
			'method' => 'track.ban',
			'api_key' => $this->apiKey,
			'artist' => $this->artist,
			'track' => $this->track,
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
	
	public function getSimilar() {
		$vars = array(
			'method' => 'track.getsimilar',
			'api_key' => $this->apiKey,
			'track' => $this->track,
			'artist' => $this->artist
		);
		
		if ( $call = $this->apiGetCall($vars) ) {
			if ( count($call->similartracks->track) > 0 ) {
				$i = 0;
				foreach ( $call->similartracks->track as $track ) {
					$this->similar[$i]['name'] = (string) $track->name;
					$this->similar[$i]['match'] = (string) $track->match;
					$this->similar[$i]['mbid'] = (string) $track->mbid;
					$this->similar[$i]['url'] = (string) $track->url;
					$this->similar[$i]['streamable'] = (string) $track->streamable;
					$this->similar[$i]['fulltrack'] = (string) $track->streamable['fulltrack'];
					$this->similar[$i]['artist']['name'] = (string) $track->artist->name;
					$this->similar[$i]['artist']['mbid'] = (string) $track->artist->mbid;
					$this->similar[$i]['artist']['url'] = (string) $track->artist->url;
					$this->similar[$i]['images']['small'] = (string) $track->image[0];
					$this->similar[$i]['images']['medium'] = (string) $track->image[1];
					$this->similar[$i]['images']['large'] = (string) $track->image[2];
					$i++;
				}
				return $this->similar;
			}
			else {
				$this->handleError(90, 'This track has no similar tracks');
				return FALSE;
			}
		}
		else {
			return FALSE;
		}
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
		
		if ( $call = $this->apiGetCall($vars) ) {
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
				$this->handleError(90, 'The user has no tags on this track');
				return FALSE;
			}
		}
		else {
			return FALSE;
		}
	}
	
	public function getTopFans() {
		$vars = array(
			'method' => 'track.gettopfans',
			'api_key' => $this->apiKey,
			'track' => $this->track,
			'artist' => $this->artist
		);
		
		if ( $call = $this->apiGetCall($vars) ) {
			if ( count($call->topfans->user) > 0 ) {
				$this->topFans['artist'] = (string) $call->topfans['artist'];
				$this->topFans['track'] = (string) $call->topfans['track'];
				$i = 0;
				foreach ( $call->topfans->user as $user ) {
					$this->topFans['users'][$i]['name'] = (string) $user->name;
					$this->topFans['users'][$i]['url'] = (string) $user->url;
					$this->topFans['users'][$i]['image']['small'] = (string) $user->image[0];
					$this->topFans['users'][$i]['image']['medium'] = (string) $user->image[1];
					$this->topFans['users'][$i]['image']['large'] = (string) $user->image[2];
					$this->topFans['users'][$i]['weight'] = (string) $user->weight;
					$i++;
				}
				return $this->topFans;
			}
			else {
				$this->handleError(90, 'This track has no fans');
				return FALSE;
			}
		}
		else {
			return FALSE;
		}
	}
	
	public function getTopTags() {
		$vars = array(
			'method' => 'track.gettoptags',
			'api_key' => $this->apiKey,
			'track' => $this->track,
			'artist' => $this->artist
		);
		
		if ( $call = $this->apiGetCall($vars) ) {
			if ( count($call->toptags->tag) > 0 ) {
				$this->topTags['artist'] = (string) $call->toptags['artist'];
				$this->topTags['track'] = (string) $call->toptags['track'];
				$i = 0;
				foreach ( $call->toptags->tag as $tag ) {
					$this->topTags['tags'][$i]['name'] = (string) $tag->name;
					$this->topTags['tags'][$i]['count'] = (string) $tag->count;
					$this->topTags['tags'][$i]['url'] = (string) $tag->url;
					$i++;
				}
				return $this->topTags;
			}
			else {
				$this->handleError(90, 'This track has no tags');
				return FALSE;
			}
		}
		else {
			return FALSE;
		}
	}
	
	public function love($sessionKey, $secret) {
		$vars = array(
			'method' => 'track.love',
			'api_key' => $this->apiKey,
			'artist' => $this->artist,
			'track' => $this->track,
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
	
	public function search($page = '', $limit = '') {
		$vars = array(
			'method' => 'track.search',
			'api_key' => $this->apiKey,
			'artist' => $this->artist,
			'track' => $this->track
		);
		if ( !empty($limit) ) {
			$vars['limit'] = $limit;
		}
		if ( !empty($page) ) {
			$vars['page'] = $page;
		}
		
		if ( $call = $this->apiGetCall($vars) ) {
			$opensearch = $call->results->children('http://a9.com/-/spec/opensearch/1.1/');
			if ( $opensearch->totalResults > 0 ) {
				$this->searchResults['totalResults'] = (string) $opensearch->totalResults;
				$this->searchResults['startIndex'] = (string) $opensearch->startIndex;
				$this->searchResults['itemsPerPage'] = (string) $opensearch->itemsPerPage;
				$i = 0;
				foreach ( $call->results->trackmatches->track as $track ) {
					$this->searchResults['results'][$i]['name'] = (string) $track->name;
					$this->searchResults['results'][$i]['artist'] = (string) $track->artist;
					$this->searchResults['results'][$i]['url'] = (string) $track->url;
					$this->searchResults['results'][$i]['streamable'] = (string) $track->streamable;
					$this->searchResults['results'][$i]['fulltrack'] = (string) $track->streamable['fulltrack'];
					$this->searchResults['results'][$i]['listeners'] = (string) $track->listeners;
					$this->searchResults['results'][$i]['image']['small'] = (string) $track->image[0];
					$this->searchResults['results'][$i]['image']['medium'] = (string) $track->image[1];
					$this->searchResults['results'][$i]['image']['large'] = (string) $track->image[2];
					$i++;
				}
				return $this->searchResults;
			}
			else {
				// No tagsare found
				$this->handleError(90, 'No results');
				return FALSE;
			}
		}
		else {
			return FALSE;
		}
	}
}

?>