<?php

class lastfmApiLibrary extends lastfmApiBase {
	public $albums;
	public $artists;
	public $tracks;
	
	private $apiKey;
	private $user;
	
	function __construct($apiKey, $user) {
		$this->apiKey = $apiKey;
		$this->user = $user;
	}
	
	public function getAlbums($page = '', $limit = '') {
		$vars = array(
			'method' => 'library.getalbums',
			'api_key' => $this->apiKey,
			'user' => $this->user
		);
		if ( !empty($page) ) {
			$vars['page'] = $page;
		}
		if ( !empty($limit) ) {
			$vars['limit'] = $limit;
		}
		
		$call = $this->apiGetCall($vars);
		
		if ( $call['status'] == 'ok' ) {
			$this->albums['page'] = (string) $call->albums['page'];
			$this->albums['perPage'] = (string) $call->albums['perPage'];
			$this->albums['totalPages'] = (string) $call->albums['totalPages'];
			$i = 0;
			foreach ( $call->albums->album as $album ) {
				$this->albums['results'][$i]['name'] = (string) $album->name;
				// THIS DOESN'T WORK AS DOCUMENTED  --- $this->albums['results'][$i]['rank'] = (string) $album['rank'];
				$this->albums['results'][$i]['playcount'] = (string) $album->playcount;
				$this->albums['results'][$i]['tagcount'] = (string) $album->tagcount;
				$this->albums['results'][$i]['mbid'] = (string) $album->mbid;
				$this->albums['results'][$i]['url'] = (string) $album->url;
				$this->albums['results'][$i]['artist']['name'] = (string) $album->artist->name;
				$this->albums['results'][$i]['artist']['mbid'] = (string) $album->artist->mbid;
				$this->albums['results'][$i]['artist']['url'] = (string) $album->artist->url;
				$this->albums['results'][$i]['image']['small'] = (string) $album->image[0];
				$this->albums['results'][$i]['image']['medium'] = (string) $album->image[1];
				$this->albums['results'][$i]['image']['large'] = (string) $album->image[2];
				$i++;
			}
			
			return $this->albums;
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
	
	public function getArtists($page = '', $limit = '') {
		$vars = array(
			'method' => 'library.getartists',
			'api_key' => $this->apiKey,
			'user' => $this->user
		);
		if ( !empty($page) ) {
			$vars['page'] = $page;
		}
		if ( !empty($limit) ) {
			$vars['limit'] = $limit;
		}
		
		$call = $this->apiGetCall($vars);
		
		if ( $call['status'] == 'ok' ) {
			$this->artists['page'] = (string) $call->artists['page'];
			$this->artists['perPage'] = (string) $call->artists['perPage'];
			$this->artists['totalPages'] = (string) $call->artists['totalPages'];
			$i = 0;
			foreach ( $call->artists->artist as $artist ) {
				$this->artists['results'][$i]['name'] = (string) $artist->name;
				// THIS DOESN'T WORK AS DOCUMENTED  --- $this->artists['results'][$i]['rank'] = (string) $artist['rank'];
				$this->artists['results'][$i]['playcount'] = (string) $artist->playcount;
				$this->artists['results'][$i]['tagcount'] = (string) $artist->tagcount;
				$this->artists['results'][$i]['mbid'] = (string) $artist->mbid;
				$this->artists['results'][$i]['url'] = (string) $artist->url;
				$this->artists['results'][$i]['streamable'] = (string) $artist->streamable;
				$this->artists['results'][$i]['image']['small'] = (string) $artist->image[0];
				$this->artists['results'][$i]['image']['medium'] = (string) $artist->image[1];
				$this->artists['results'][$i]['image']['large'] = (string) $artist->image[2];
				$i++;
			}
			
			return $this->artists;
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
	
	public function getTracks($page = '', $limit = '') {
		$vars = array(
			'method' => 'library.gettracks',
			'api_key' => $this->apiKey,
			'user' => $this->user
		);
		if ( !empty($page) ) {
			$vars['page'] = $page;
		}
		if ( !empty($limit) ) {
			$vars['limit'] = $limit;
		}
		
		$call = $this->apiGetCall($vars);
		
		if ( $call['status'] == 'ok' ) {
			$this->tracks['page'] = (string) $call->tracks['page'];
			$this->tracks['perPage'] = (string) $call->tracks['perPage'];
			$this->tracks['totalPages'] = (string) $call->tracks['totalPages'];
			$i = 0;
			foreach ( $call->tracks->track as $track ) {
				$this->tracks['results'][$i]['name'] = (string) $track->name;
				// THIS DOESN'T WORK AS DOCUMENTED  --- $this->tracks['results'][$i]['rank'] = (string) $track['rank'];
				$this->tracks['results'][$i]['playcount'] = (string) $track->playcount;
				$this->tracks['results'][$i]['tagcount'] = (string) $track->tagcount;
				$this->tracks['results'][$i]['url'] = (string) $track->url;
				$this->tracks['results'][$i]['streamable'] = (string) $track->streamable;
				$this->tracks['results'][$i]['fulltrack'] = (string) $track->streamable['fulltrack'];
				$this->tracks['results'][$i]['artist']['name'] = (string) $track->artist->name;
				$this->tracks['results'][$i]['artist']['mbid'] = (string) $track->artist->mbid;
				$this->tracks['results'][$i]['artist']['url'] = (string) $track->artist->url;
				$this->tracks['results'][$i]['image']['small'] = (string) $track->image[0];
				$this->tracks['results'][$i]['image']['medium'] = (string) $track->image[1];
				$this->tracks['results'][$i]['image']['large'] = (string) $track->image[2];
				$i++;
			}
			
			return $this->tracks;
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