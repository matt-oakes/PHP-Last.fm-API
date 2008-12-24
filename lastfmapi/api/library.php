<?php

class lastfmApiLibrary extends lastfmApiBase {
	public $albums;
	public $artists;
	public $tracks;
	
	private $auth;
	private $fullAuth;
	
	function __construct($auth, $fullAuth) {
		$this->auth = $auth;
		$this->fullAuth = $fullAuth;
	}
	
	public function addAlbum($methodVars) {
		// Only allow full authed calls
		if ( $this->fullAuth == TRUE ) {
			// Check for required variables
			if ( !empty($methodVars['artist']) && !empty($methodVars['album']) ) {
				$vars = array(
					'method' => 'library.addalbum',
					'api_key' => $this->auth->apiKey,
					'artist' => $methodVars['artist'],
					'album' => $methodVars['album'],
					'sk' => $this->auth->sessionKey
				);
				$sig = $this->apiSig($this->auth->secret, $vars);
				$vars['api_sig'] = $sig;
				
				if ( $call = $this->apiPostCall($vars) ) {
					return TRUE;
				}
				else {
					return FALSE;
				}
			}
			else {
				// Give a 91 error if incorrect variables are used
				$this->handleError(91, 'You must include artist and album varialbes in the call for this method');
				return FALSE;
			}
		}
		else {
			// Give a 92 error if not fully authed
			$this->handleError(92, 'Method requires full auth. Call auth.getSession using lastfmApiAuth class');
			return FALSE;
		}
	}
	
	public function addArtist($methodVars) {
		// Only allow full authed calls
		if ( $this->fullAuth == TRUE ) {
			// Check for required variables
			if ( !empty($methodVars['artist']) ) {
				$vars = array(
					'method' => 'library.addartist',
					'api_key' => $this->auth->apiKey,
					'artist' => $methodVars['artist'],
					'sk' => $this->auth->sessionKey
				);
				$sig = $this->apiSig($this->auth->secret, $vars);
				$vars['api_sig'] = $sig;
				
				if ( $call = $this->apiPostCall($vars) ) {
					return TRUE;
				}
				else {
					return FALSE;
				}
			}
			else {
				// Give a 91 error if incorrect variables are used
				$this->handleError(91, 'You must include artist varialbe in the call for this method');
				return FALSE;
			}
		}
		else {
			// Give a 92 error if not fully authed
			$this->handleError(92, 'Method requires full auth. Call auth.getSession using lastfmApiAuth class');
			return FALSE;
		}
	}
	
	public function addTrack($methodVars) {
		// Only allow full authed calls
		if ( $this->fullAuth == TRUE ) {
			// Check for required variables
			if ( !empty($methodVars['artist']) && !empty($methodVars['track']) ) {
				$vars = array(
					'method' => 'library.addtrack',
					'api_key' => $this->auth->apiKey,
					'artist' => $methodVars['artist'],
					'track' => $methodVars['track'],
					'sk' => $this->auth->sessionKey
				);
				$sig = $this->apiSig($this->auth->secret, $vars);
				$vars['api_sig'] = $sig;
				
				if ( $call = $this->apiPostCall($vars) ) {
					return TRUE;
				}
				else {
					return FALSE;
				}
			}
			else {
				// Give a 91 error if incorrect variables are used
				$this->handleError(91, 'You must include artist and track varialbes in the call for this method');
				return FALSE;
			}
		}
		else {
			// Give a 92 error if not fully authed
			$this->handleError(92, 'Method requires full auth. Call auth.getSession using lastfmApiAuth class');
			return FALSE;
		}
	}
	
	public function getAlbums($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['user']) ) {
			$vars = array(
				'method' => 'library.getalbums',
				'api_key' => $this->auth->apiKey,
				'user' => $methodVars['user']
			);
			if ( !empty($methodVars['page']) ) {
				$vars['page'] = $methodVars['page'];
			}
			if ( !empty($methodVars['limit']) ) {
				$vars['limit'] = $methodVars['limit'];
			}
			
			if ( $call = $this->apiGetCall($vars) ) {
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
			else {
				return FALSE;
			}
		}
		else {
			// Give a 91 error if incorrect variables are used
			$this->handleError(91, 'You must include a user variable in the call for this method');
			return FALSE;
		}
	}
	
	public function getArtists($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['user']) ) {
			$vars = array(
				'method' => 'library.getartists',
				'api_key' => $this->auth->apiKey,
				'user' => $methodVars['user']
			);
			if ( !empty($methodVars['page']) ) {
				$vars['page'] = $methodVars['page'];
			}
			if ( !empty($methodVars['limit']) ) {
				$vars['limit'] = $methodVars['limit'];
			}
			
			if ( $call = $this->apiGetCall($vars) ) {
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
			else {
				return FALSE;
			}
		}
		else {
			// Give a 91 error if incorrect variables are used
			$this->handleError(91, 'You must include a user variable in the call for this method');
			return FALSE;
		}
	}
	
	public function getTracks($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['user']) ) {
			$vars = array(
				'method' => 'library.gettracks',
				'api_key' => $this->auth->apiKey,
				'user' => $methodVars['user']
			);
			if ( !empty($methodVars['page']) ) {
				$vars['page'] = $methodVars['page'];
			}
			if ( !empty($methodVars['limit']) ) {
				$vars['limit'] = $methodVars['limit'];
			}
			
			if ( $call = $this->apiGetCall($vars) ) {
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
			else {
				return FALSE;
			}
		}
		else {
			// Give a 91 error if incorrect variables are used
			$this->handleError(91, 'You must include a user variable in the call for this method');
			return FALSE;
		}
	}
}

?>