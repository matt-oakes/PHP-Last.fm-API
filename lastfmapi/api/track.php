<?php

class lastfmApiTrack extends lastfmApiBase {
	public $info;
	public $similar;
	public $topFans;
	public $topTags;
	public $searchResults;
	public $config;
	
	private $auth;
	private $fullAuth;
	
	function __construct($auth, $fullAuth, $config) {
		$this->auth = $auth;
		$this->fullAuth = $fullAuth;
		$this->config = $config;
	}
	
	public function addTags($methodVars) {
		// Only allow full authed calls
		if ( $this->fullAuth == TRUE ) {
			// Check for required variables
			if ( !empty($methodVars['artist']) && !empty($methodVars['track']) && !empty($methodVars['tags']) ) {
				// If the tags variables is an array build a CS list
				if ( is_array($methodVars['tags']) ) {
					$tags = '';
					foreach ( $methodVars['tags'] as $tag ) {
						$tags .= $tag.',';
					}
					$tags = substr($tags, 0, -1);
				}
				else {
					$tags = $methodVars['tags'];
				}
				
				$vars = array(
					'method' => 'track.addtags',
					'api_key' => $this->auth->apiKey,
					'artist' => $methodVars['artist'],
					'track' => $methodVars['track'],
					'tags' => $tags,
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
				$this->handleError(91, 'You must include artist, track and tags varialbes in the call for this method');
				return FALSE;
			}
		}
		else {
			// Give a 92 error if not fully authed
			$this->handleError(92, 'Method requires full auth. Call auth.getSession using lastfmApiAuth class');
			return FALSE;
		}
	}
	
	public function ban($methodVars) {
		// Only allow full authed calls
		if ( $this->fullAuth == TRUE ) {
			// Check for required variables
			if ( !empty($methodVars['artist']) && !empty($methodVars['track']) ) {
				$vars = array(
					'method' => 'track.ban',
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
	
	public function getInfo($methodVars) {
		$vars = array(
			'method' => 'track.getinfo',
			'api_key' => $this->auth->apiKey,
			'track' => $methodVars['track'],
			'artist' => $methodVars['artist']
		);
		if ( !empty($methodVars['track']) ) {
			$vars['track'] = $methodVars['track'];
		}
		if ( !empty($methodVars['artist']) ) {
			$vars['artist'] = $methodVars['artist'];
		}
		if ( !empty($methodVars['mbid']) ) {
			$vars['mbid'] = $methodVars['mbid'];
		}
		
		if ( $call = $this->apiGetCall($vars) ) {
			$this->info['id'] = (string) $call->track->id;
			$this->info['name'] = (string) $call->track->name;
			$this->info['mbid'] = (string) $call->track->mbid;
			$this->info['url'] = (string) $call->track->url;
			$this->info['duration'] = (string) $call->track->duration;
			$this->info['streamable'] = (string) $call->track->streamable;
			$this->info['fulltrack'] = (string) $call->track->streamable['fulltrack'];
			$this->info['listeners'] = (string) $call->track->listeners;
			$this->info['playcount'] = (string) $call->track->playcount;
			$this->info['artist']['name'] = (string) $call->track->artist->name;
			$this->info['artist']['mbid'] = (string) $call->track->artist->mbid;
			$this->info['artist']['url'] = (string) $call->track->artist->url;
			$this->info['album']['position'] = (string) $call->track->album['position'];
			$this->info['album']['artist'] = (string) $call->track->album->artist;
			$this->info['album']['title'] = (string) $call->track->album->title;
			$this->info['album']['mbid'] = (string) $call->track->album->mbid;
			$this->info['album']['url'] = (string) $call->track->album->url;
			$this->info['album']['image']['small'] = (string) $call->track->album->image[0];
			$this->info['album']['image']['medium'] = (string) $call->track->album->image[1];
			$this->info['album']['image']['large'] = (string) $call->track->album->image[2];
			$i = 0;
			foreach ( $call->track->toptags->tag as $tag ) {
				$this->info['toptags'][$i]['name'] = (string) $tag->name;
				$this->info['toptags'][$i]['url'] = (string) $tag->url;
				$i++;
			}
			$this->info['wiki']['published'] = (string) $call->track->wiki->published;
			$this->info['wiki']['summary'] = (string) $call->track->wiki->summary;
			$this->info['wiki']['content'] = (string) $call->track->wiki->content;
			
			return $this->info;
		}
		else {
			return FALSE;
		}
	}
	
	public function getSimilar($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['artist']) && !empty($methodVars['track']) ) {
			$vars = array(
				'method' => 'track.getsimilar',
				'api_key' => $this->auth->apiKey,
				'track' => $methodVars['track'],
				'artist' => $methodVars['artist']
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
		else {
			// Give a 91 error if incorrect variables are used
			$this->handleError(91, 'You must include artist and track varialbes in the call for this method');
			return FALSE;
		}
	}
	
	public function getTags($methodVars) {
		// Only allow full authed calls
		if ( $this->fullAuth == TRUE ) {
			// Check for required variables
			if ( !empty($methodVars['artist']) && !empty($methodVars['track']) ) {
				$vars = array(
					'method' => 'track.gettags',
					'api_key' => $this->auth->apiKey,
					'sk' => $this->auth->sessionKey,
					'track' => $methodVars['track'],
					'artist' => $methodVars['artist']
				);
				$sig = $this->apiSig($this->auth->secret, $vars);
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
	
	public function getTopFans($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['artist']) && !empty($methodVars['track']) ) {
			$vars = array(
				'method' => 'track.gettopfans',
				'api_key' => $this->auth->apiKey,
				'track' => $methodVars['track'],
				'artist' => $methodVars['artist']
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
		else {
			// Give a 91 error if incorrect variables are used
			$this->handleError(91, 'You must include artist and track varialbes in the call for this method');
			return FALSE;
		}

	}
	
	public function getTopTags($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['artist']) && !empty($methodVars['track']) ) {
			$vars = array(
				'method' => 'track.gettoptags',
				'api_key' => $this->auth->apiKey,
				'track' => $methodVars['track'],
				'artist' => $methodVars['artist']
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
		else {
			// Give a 91 error if incorrect variables are used
			$this->handleError(91, 'You must include artist and track varialbes in the call for this method');
			return FALSE;
		}
	}
	
	public function love($methodVars) {
		// Only allow full authed calls
		if ( $this->fullAuth == TRUE ) {
			// Check for required variables
			if ( !empty($methodVars['artist']) && !empty($methodVars['track']) ) {
				$vars = array(
					'method' => 'track.love',
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
	
	public function removeTag($methodVars) {
		// Only allow full authed calls
		if ( $this->fullAuth == TRUE ) {
			// Check for required variables
			if ( !empty($methodVars['artist']) && !empty($methodVars['track']) && !empty($methodVars['tag']) ) {
				$vars = array(
					'method' => 'track.removetag',
					'api_key' => $this->auth->apiKey,
					'artist' => $methodVars['artist'],
					'track' => $methodVars['track'],
					'tag' => $methodVars['tag'],
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
				$this->handleError(91, 'You must include tag, artist and track varialbes in the call for this method');
				return FALSE;
			}
		}
		else {
			// Give a 92 error if not fully authed
			$this->handleError(92, 'Method requires full auth. Call auth.getSession using lastfmApiAuth class');
			return FALSE;
		}
	}
	
	public function search($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['track']) ) {
			$vars = array(
				'method' => 'track.search',
				'api_key' => $this->auth->apiKey,
				'track' => $methodVars['track']
			);
			if ( !empty($methodVars['artist']) ) {
				$vars['artist'] = $methodVars['artist'];
			}
			if ( !empty($methodVars['limit']) ) {
				$vars['limit'] = $methodVars['limit'];
			}
			if ( !empty($methodVars['page']) ) {
				$vars['page'] = $methodVars['page'];
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
		else {
			// Give a 91 error if incorrect variables are used
			$this->handleError(91, 'You must include track variable in the call for this method');
			return FALSE;
		}
	}
	
	public function share($methodVars) {
		// Only allow full authed calls
		if ( $this->fullAuth == TRUE ) {
			// Check for required variables
			if ( !empty($methodVars['artist']) && !empty($methodVars['track']) && !empty($methodVars['recipient']) ) {
				$vars = array(
					'method' => 'track.share',
					'api_key' => $this->auth->apiKey,
					'artist' => $methodVars['artist'],
					'track' => $methodVars['track'],
					'recipient' => $methodVars['recipient'],
					'sk' => $this->auth->sessionKey
				);
				if ( !empty($methodVars['message']) ) {
					$vars['message'] = $methodVars['message'];
				}
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
				$this->handleError(91, 'You must include recipient, artist and track varialbes in the call for this method');
				return FALSE;
			}
		}
		else {
			// Give a 92 error if not fully authed
			$this->handleError(92, 'Method requires full auth. Call auth.getSession using lastfmApiAuth class');
			return FALSE;
		}
	}
}

?>