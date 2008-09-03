<?php

class lastfmApiPlaylist extends lastfmApiBase {
	public $playlist;
	
	private $auth;
	private $fullAuth;
	
	function __construct($auth, $fullAuth) {
		$this->auth = $auth;
		$this->fullAuth = $fullAuth;
	}
	
	public function addTrack($methodVars) {
		// Only allow full authed calls
		if ( $this->fullAuth == TRUE ) {
			// Check for required variables
			if ( !empty($methodVars['playlistId']) && !empty($methodVars['artist']) && !empty($methodVars['track']) ) {
				$vars = array(
					'method' => 'playlist.addtrack',
					'api_key' => $this->auth->apiKey,
					'artist' => $methodVars['artist'],
					'track' => $methodVars['track'],
					'playlistID' => $methodVars['playlistId'],
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
				$this->handleError(91, 'You must include playlistId, artist and track varialbes in the call for this method');
				return FALSE;
			}
		}
		else {
			// Give a 92 error if not fully authed
			$this->handleError(92, 'Method requires full auth. Call auth.getSession using lastfmApiAuth class');
			return FALSE;
		}
	}
	
	public function fetch($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['playlistUrl']) ) {
			$vars = array(
				'method' => 'playlist.fetch',
				'api_key' => $this->auth->apiKey,
				'playlistURL' => $methodVars['playlistUrl']
			);
			
			if ( $call = $this->apiGetCall($vars) ) {
				$this->playlist['title'] = (string) $call->playlist->title;
				$this->playlist['annotation'] = (string) $call->playlist->annotation;
				$this->playlist['creator'] = (string) $call->playlist->creator;
				$this->playlist['date'] = strtotime(trim((string) $call->playlist->date));
				$this->playlist['version'] = (string) $call->playlist['version'];
				$i = 0;
				foreach ( $call->playlist->trackList->track as $track ) {
					$this->playlist['tracklisting'][$i]['title'] = (string) $track->title;
					$this->playlist['tracklisting'][$i]['url'] = (string) $track->extension->trackpage;
					$this->playlist['tracklisting'][$i]['duration'] = (string) $track->duration;
					$this->playlist['tracklisting'][$i]['info'] = (string) $track->info;
					$this->playlist['tracklisting'][$i]['image'] = (string) $track->image;
					$this->playlist['tracklisting'][$i]['artist']['name'] = (string) $track->creator;
					$this->playlist['tracklisting'][$i]['artist']['url'] = (string) $track->extension->artistpage;
					$this->playlist['tracklisting'][$i]['album']['name'] = (string) $track->album;
					$this->playlist['tracklisting'][$i]['album']['url'] = (string) $track->extension->albumpage;
					$i++;
				}
				return $this->playlist;
			}
			else {
				return FALSE;
			}
		}
		else {
			// Give a 91 error if incorrect variables are used
			$this->handleError(91, 'You must include playlistUrl varialbe in the call for this method');
			return FALSE;
		}
	}
}

?>