<?php

class lastfmApiTag extends lastfmApiBase {
	public $similar;
	public $topAlbums;
	public $topArtists;
	public $topTags;
	public $topTracks;
	public $searchResults;
	
	private $apiKey;
	private $tag;
	
	function __construct($apiKey, $tag = '') {
		$this->apiKey = $apiKey;
		$this->tag = $tag;
	}
	
	public function getSimilar() {
		$vars = array(
			'method' => 'tag.getsimilar',
			'api_key' => $this->apiKey,
			'tag' => $this->tag
		);
		
		if ( $call = $this->apiGetCall($vars) ) {
			$i = 0;
			foreach ( $call->similartags->tag as $tag ) {
				$this->similar[$i]['name'] = (string) $tag->name;
				$this->similar[$i]['url'] = (string) $tag->url;
				$this->similar[$i]['streamable'] = (string) $tag->streamable;
				$i++;
			}
			return $this->similar;
		}
		else {
			return FALSE;
		}
	}
	
	public function getTopAlbums() {
		$vars = array(
			'method' => 'tag.gettopalbums',
			'api_key' => $this->apiKey,
			'tag' => $this->tag
		);
		
		if ( $call = $this->apiGetCall($vars) ) {
			$i = 0;
			foreach ( $call->topalbums->album as $album ) {
				$this->topAlbums[$i]['name'] = (string) $album->name;
				$this->topAlbums[$i]['tagcount'] = (string) $album->tagcount;
				$this->topAlbums[$i]['url'] = (string) $album->url;
				$this->topAlbums[$i]['artist']['name'] = (string) $album->artist->name;
				$this->topAlbums[$i]['artist']['mbid'] = (string) $album->artist->mbid;
				$this->topAlbums[$i]['artist']['url'] = (string) $album->artist->url;
				$this->topAlbums[$i]['image']['small'] = (string) $album->image[0];
				$this->topAlbums[$i]['image']['medium'] = (string) $album->image[1];
				$this->topAlbums[$i]['image']['large'] = (string) $album->image[2];
				$i++;
			}
			return $this->topAlbums;
		}
		else {
			return FALSE;
		}
	}
	
	public function getTopArtists() {
		$vars = array(
			'method' => 'tag.gettopartists',
			'api_key' => $this->apiKey,
			'tag' => $this->tag
		);
		
		if ( $call = $this->apiGetCall($vars) ) {
			$i = 0;
			foreach ( $call->topartists->artist as $artist ) {
				$this->topArtists[$i]['name'] = (string) $artist->name;
				$this->topArtists[$i]['tagcount'] = (string) $artist->tagcount;
				$this->topArtists[$i]['url'] = (string) $artist->url;
				$this->topArtists[$i]['mbid'] = (string) $artist->mbid;
				$this->topArtists[$i]['streamable'] = (string) $artist->streamable;
				$this->topArtists[$i]['image']['small'] = (string) $artist->image[0];
				$this->topArtists[$i]['image']['medium'] = (string) $artist->image[1];
				$this->topArtists[$i]['image']['large'] = (string) $artist->image[2];
				$i++;
			}
			return $this->topArtists;
		}
		else {
			return FALSE;
		}
	}
	
	public function getTopTags() {
		$vars = array(
			'method' => 'tag.gettoptags',
			'api_key' => $this->apiKey
		);
		
		if ( $call = $this->apiGetCall($vars) ) {
			$i = 0;
			foreach ( $call->toptags->tag as $tag ) {
				$this->topTags[$i]['name'] = (string) $tag->name;
				$this->topTags[$i]['count'] = (string) $tag->count;
				$this->topTags[$i]['url'] = (string) $tag->url;
				$i++;
			}
			return $this->topTags;
		}
		else {
			return FALSE;
		}
	}
	
	public function getTopTracks() {
		$vars = array(
			'method' => 'tag.gettoptracks',
			'api_key' => $this->apiKey,
			'tag' => $this->tag
		);
		
		if ( $call = $this->apiGetCall($vars) ) {
			$i = 0;
			foreach ( $call->toptracks->track as $track ) {
				$this->topTracks[$i]['name'] = (string) $track->name;
				$this->topTracks[$i]['tagcount'] = (string) $track->tagcount;
				$this->topTracks[$i]['url'] = (string) $track->url;
				$this->topTracks[$i]['streamable'] = (string) $track->streamable;
				$this->topTracks[$i]['fulltrack'] = (string) $track->streamable['fulltrack'];
				$this->topTracks[$i]['artist']['name'] = (string) $track->artist->name;
				$this->topTracks[$i]['artist']['mbid'] = (string) $track->artist->mbid;
				$this->topTracks[$i]['artist']['url'] = (string) $track->artist->url;
				$this->topTracks[$i]['image']['small'] = (string) $track->image[0];
				$this->topTracks[$i]['image']['medium'] = (string) $track->image[1];
				$this->topTracks[$i]['image']['large'] = (string) $track->image[2];
				$i++;
			}
			return $this->topTracks;
		}
		else {
			return FALSE;
		}
	}
	public function search($page = '', $limit = '') {
		$vars = array(
			'method' => 'tag.search',
			'api_key' => $this->apiKey,
			'tag' => $this->tag
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
				foreach ( $call->results->tagmatches->tag as $tag ) {
					$this->searchResults['results'][$i]['name'] = (string) $tag->name;
					$this->searchResults['results'][$i]['tagcount'] = (string) $tag->tagcount;
					$this->searchResults['results'][$i]['url'] = (string) $tag->url;
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