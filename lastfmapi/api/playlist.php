<?php

class lastfmApiPlaylist extends lastfmApiBase {
	public $playlist;
	
	private $apiKey;
	private $playlistUrl;
	
	function __construct($apiKey, $playlistUrl) {
		$this->apiKey = $apiKey;
		$this->playlistUrl = $playlistUrl;
	}
	
	public function fetch() {
		$vars = array(
			'method' => 'playlist.fetch',
			'api_key' => $this->apiKey,
			'playlistURL' => $this->playlistUrl
		);
		
		$call = $this->apiGetCall($vars);
		
		if ( $call['status'] == 'ok' ) {
			$this->playlist['title'] = (string) $call->playlist->title;
			$this->playlist['annotation'] = (string) $call->playlist->annotation;
			$this->playlist['creator'] = (string) $call->playlist->creator;
			$this->playlist['date'] = strtotime(trim((string) $call->playlist->date));
			$this->playlist['version'] = (string) $call->playlist['version'];
			$i = 0;
			foreach ( $call->playlist->trackList->track as $track ) {
				$this->playlist['tracklisting'][$i]['title'] = (string) $track->title;
				$this->playlist['tracklisting'][$i]['identifier'] = (string) $track->identifier;
				$this->playlist['tracklisting'][$i]['album'] = (string) $track->album;
				$this->playlist['tracklisting'][$i]['creator'] = (string) $track->creator;
				$this->playlist['tracklisting'][$i]['duration'] = (string) $track->duration;
				$this->playlist['tracklisting'][$i]['info'] = (string) $track->info;
				$this->playlist['tracklisting'][$i]['image'] = (string) $track->image;
				$this->playlist['tracklisting'][$i]['artistPage'] = (string) $track->extention->artistpage;
				$this->playlist['tracklisting'][$i]['albumPage'] = (string) $track->extention->albumpage;
				$this->playlist['tracklisting'][$i]['trackPage'] = (string) $track->extention->trackpage;
				$i++;
			}
			
			return $this->playlist;
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