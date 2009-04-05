<?php

class lastfmApiArtist extends lastfmApi {
	public $events;
	public $info;
	public $similar;
	public $tags;
	public $topAlbums;
	public $topFans;
	public $topTags;
	public $topTracks;
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
			if ( !empty($methodVars['artist']) && !empty($methodVars['tags']) ) {
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
				
				// Set the call variables
				$vars = array(
					'method' => 'artist.addtags',
					'api_key' => $this->auth->apiKey,
					'artist' => $methodVars['artist'],
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
				$this->handleError(91, 'You must include artist and tags varialbes in the call for this method');
				return FALSE;
			}
		}
		else {
			// Give a 92 error if not fully authed
			$this->handleError(92, 'Method requires full auth. Call auth.getSession using lastfmApiAuth class');
			return FALSE;
		}
	}
	
	public function getEvents($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['artist']) ) {
			$vars = array(
				'method' => 'artist.getevents',
				'api_key' => $this->auth->apiKey,
				'artist' => $methodVars['artist']
			);
			
			if ( $call = $this->apiGetCall($vars) ) {
				if ( $call->events['total'] != 0 ) {
					$i = 0;
					foreach ( $call->events->event as $event ) {
						$this->events[$i]['id'] = (string) $event->id;
						$this->events[$i]['title'] = (string) $event->title;
						$ii = 0;
						foreach ( $event->artists->artist as $artist ) {
							$this->events[$i]['artists'][$ii] = (string) $artist;
							$ii++;
						}
						$this->events[$i]['headliner'] = (string) $event->artists->headliner;
						$this->events[$i]['venue']['name'] = (string) $event->venue->name;
						$this->events[$i]['venue']['location']['city'] = (string) $event->venue->location->city;
						$this->events[$i]['venue']['location']['country'] = (string) $event->venue->location->country;
						$this->events[$i]['venue']['location']['street'] = (string) $event->venue->location->street;
						$this->events[$i]['venue']['location']['postcode'] = (string) $event->venue->location->postalcode;
						$geopoint =  $event->venue->location->children('http://www.w3.org/2003/01/geo/wgs84_pos#');
						$this->events[$i]['venue']['location']['geopoint']['lat'] = (string) $geopoint->point->lat;
						$this->events[$i]['venue']['location']['geopoint']['long'] = (string) $geopoint->point->long;
						$this->events[$i]['venue']['location']['timezone'] = (string) $event->venue->location->timezone;
						$this->events[$i]['venue']['url'] = (string) $call->venue->url;
						$this->events[$i]['startdate'] = strtotime(trim((string) $event->startDate));
						$this->events[$i]['description'] = (string) $event->description;
						$this->events[$i]['image']['small'] = (string) $event->image[0];
						$this->events[$i]['image']['mendium'] = (string) $event->image[1];
						$this->events[$i]['image']['large'] = (string) $event->image[2];
						$this->events[$i]['attendance'] = (string) $event->attendance;
						$this->events[$i]['reviews'] = (string) $event->reviews;
						$this->events[$i]['tag'] = (string) $event->tag;
						$this->events[$i]['url'] = (string) $event->url;
						
						$i++;
					}
					
					return $this->events;
				}
				else {
					// No events are found
					$this->handleError(90, 'Artist has no events');
					return FALSE;
				}
			}
			else {
				return FALSE;
			}
		}
		else {
			// Give a 91 error if incorrect variables are used
			$this->handleError(91, 'You must include artist variable in the call for this method');
			return FALSE;
		}
	}
	
	public function getImages($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['artist']) ) {
			$vars = array(
				'method' => 'artist.getimages',
				'api_key' => $this->auth->apiKey,
				'artist' => $methodVars['artist']
			);
			if ( !empty($methodVars['page']) ) {
				$vars['page'] = $methodVars['page'];
			}
			if ( !empty($methodVars['limit']) ) {
				$vars['limit'] = $methodVars['limit'];
			}
			if ( !empty($methodVars['order']) && ( $methodVars['order'] == 'popularity' || $methodVars['order'] == 'dateadded' ) ) {
				$vars['order'] = $methodVars['order'];
			}
			
			if ( $call = $this->apiGetCall($vars) ) {
				$this->images = array();
				$i = 0;
				$this->images['artist'] = (string)$call->images['artist'];
				$this->images['page'] = (string)$call->images['page'];
				$this->images['totalpages'] = (string)$call->images['totalpages'];
				$this->images['total'] = (string)$call->images['total'];
				
				foreach ( $call->images->image as $image ) {
					$this->images['images'][$i]['title'] = (string) $image->title;
					$this->images['images'][$i]['url'] = (string) $image->url;
					$this->images['images'][$i]['dateadded'] = (string) $image->dateadded;
					$this->images['images'][$i]['format'] = (string) $image->format;
					$this->images['images'][$i]['sizes'] = array();
					$official = isset($image['official']) ? (string) $image['official'] : false;
					$this->images['images'][$i]['official'] = $official == 'yes';
					foreach( $image->sizes->size as $size ) {
						$this->images['images'][$i]['sizes'][(string)$size['name']] = array(
							'width' => (string) $size['width'],
							'height' => (string) $size['height'],
							'url' => (string) $size,
						);
					}
					$this->images['images'][$i]['votes'] = array(
						'thumbsup' => (string) $image->votes->thumbsup,
						'thumbsdown' => (string) $image->votes->thumbsdown,
					);
					$i++;
				}
				return $this->images;
			}
			else {
				return FALSE;
			}
		}
		else {
			// Give a 91 error if incorrect variables are used
			$this->handleError(91, 'You must include artist variable in the call for this method');
			return FALSE;
		}
	}
	
	public function getInfo($methodVars) {
		$vars = array(
			'method' => 'artist.getinfo',
			'api_key' => $this->auth->apiKey
		);
		if ( !empty($methodVars['mbid']) ) {
			$vars['mbid'] = $methodVars['mbid'];
		}
		if ( !empty($methodVars['artist']) ) {
			$vars['artist'] = $methodVars['artist'];
		}
		
		if ( $call = $this->apiGetCall($vars) ) {
			$this->info['name'] = (string) $call->artist->name;
			$this->info['mbid'] = (string) $call->artist->mbid;
			$this->info['url'] = (string) $call->artist->url;
			$this->info['image']['small'] = (string) $call->artist->image;
			$this->info['image']['medium'] = (string) $call->artist->image[1];
			$this->info['image']['large'] = (string) $call->artist->image[2];
			$this->info['streamable'] = (string) $call->artist->streamable;
			$this->info['stats']['listeners'] = (string) $call->artist->stats->listeners;
			$this->info['stats']['playcount'] = (string) $call->artist->stats->plays;
			$i = 0;
			foreach ( $call->artist->similar->artist as $artist ) {
				$this->info['similar'][$i]['name'] = (string) $artist->name;
				$this->info['similar'][$i]['url'] = (string) $artist->url;
				$this->info['similar'][$i]['image']['small'] = (string) $artist->image;
				$this->info['similar'][$i]['image']['medium'] = (string) $artist->image[1];
				$this->info['similar'][$i]['image']['large'] = (string) $artist->image[2];
				$i++;
			}
			if ( count($call->artist->tags->tag) > 0 ) {
				$i = 0;
				foreach ( $call->artist->tags->tag as $tag ) {
					$this->info['tags'][$i]['name'] = (string) $tag->name;
					$this->info['tags'][$i]['url'] = (string) $tag->url;
					$i++;
				}
			}
			else {
				$this->info['tags'] = FALSE;
			}
			$this->info['bio']['published'] = (string) $call->artist->bio->published;
			$this->info['bio']['summary'] = (string) $call->artist->bio->summary;
			$this->info['bio']['content'] = (string) $call->artist->bio->content;
			
			return $this->info;
		}
		else {
			return FALSE;
		}
	}
	
	public function getShouts($methodVars) {
		if ( !empty($methodVars['artist']) ) {
			$vars = array(
				'method' => 'artist.getshouts',
				'api_key' => $this->auth->apiKey,
				'artist' => $methodVars['artist']
			);
			
			if ( $call = $this->apiGetCall($vars) ) {
				$this->shouts['artist'] = (string)$call->shouts['artist'];
				$this->shouts['total'] = (string)$call->shouts['total'];
				$i = 0;
				foreach ( $call->shouts->shout as $shout ) {
					$this->shouts['shouts'][$i]['body'] = (string)$shout->body;
					$this->shouts['shouts'][$i]['author'] = (string)$shout->author;
					$this->shouts['shouts'][$i]['date'] = strtotime((string)$shout->date);
					$i++;
				}
				
				return $this->shouts;
			}
			else {
				return FALSE;
			}
		}
		else {
			// Give a 91 error if incorrect variables are used
			$this->handleError(91, 'You must include artist variable in the call for this method');
			return FALSE;
		}
	}
	
	public function getSimilar($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['artist']) ) {
			$vars = array(
				'method' => 'artist.getsimilar',
				'api_key' => $this->auth->apiKey,
				'artist' => $methodVars['artist']
			);
			if ( !empty($limit) ) {
				$vars['limit'] = $limit;
			}
			
			if ( $call = $this->apiGetCall($vars) ) {
				$this->similar = '';
				$i = 0;
				foreach ( $call->similarartists->artist as $artist ) {
					$this->similar[$i]['name'] = (string) $artist->name;
					$this->similar[$i]['mbid'] = (string) $artist->mbid;
					$this->similar[$i]['match'] = (string) $artist->match;
					$this->similar[$i]['url'] = (string) $artist->url;
					$this->similar[$i]['image'] = (string) $artist->image;
					$i++;
				}
				
				return $this->similar;
			}
			else {
				return FALSE;
			}
		}
		else {
			// Give a 91 error if incorrect variables are used
			$this->handleError(91, 'You must include artist variable in the call for this method');
			return FALSE;
		}
	}
	
	public function getTags($methodVars) {
		// Only allow full authed calls
		if ( $this->fullAuth == TRUE ) {
			// Check for required variables
			if ( !empty($methodVars['artist']) ) {
				$vars = array(
					'method' => 'artist.gettags',
					'api_key' => $this->auth->apiKey,
					'sk' => $this->auth->sessionKey,
					'artist' => $methodVars['artist']
				);
				$sig = $this->apiSig($this->auth->secret, $vars);
				$vars['api_sig'] = $sig;
				
				if ( $call = $this->apiGetCall($vars) ) {
					if ( count($call->tags->tag) > 0 ) {
						$i = 0;
						foreach ( $call->tags[0]->tag as $tag ) {
							$this->tags[$i]['name'] = (string) $tag->name;
							$this->tags[$i]['url'] = (string) $tag->url;
							$i++;
						}
						
						return $this->tags;
					}
					else {
						// No tagsare found
						$this->handleError(90, 'Artist has no tags from this user');
						return FALSE;
					}
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
	
	public function getTopAlbums($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['artist']) ) {
			$vars = array(
				'method' => 'artist.gettopalbums',
				'api_key' => $this->auth->apiKey,
				'artist' => $methodVars['artist']
			);
			
			if ( $call = $this->apiGetCall($vars) ) {
				if ( count($call->topalbums->album) > 0 ) {
					$i = 0;
					foreach ( $call->topalbums->album as $album ) {
						$this->topAlbums[$i]['rank'] = (string) $album['rank'];
						$this->topAlbums[$i]['name'] = (string) $album->name;
						$this->topAlbums[$i]['mbid'] = (string) $album->mbid;
						$this->topAlbums[$i]['playcount'] = (string) $album->playcount;
						$this->topAlbums[$i]['url'] = (string) $album->url;
						$this->topAlbums[$i]['image']['small'] = (string) $album->image[0];
						$this->topAlbums[$i]['image']['medium'] = (string) $album->image[1];
						$this->topAlbums[$i]['image']['large'] = (string) $album->image[2];
						$i++;
					}
					
					return $this->topAlbums;
				}
				else {
					// No tagsare found
					$this->handleError(90, 'Artist has no top albums');
					return FALSE;
				}
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
	
	public function getTopFans($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['artist']) ) {
			$vars = array(
				'method' => 'artist.gettopfans',
				'api_key' => $this->auth->apiKey,
				'artist' => $methodVars['artist']
			);
			
			if ( $call = $this->apiGetCall($vars) ) {
				if ( count($call->topfans->user) > 0 ) {
					$i = 0;
					foreach ( $call->topfans->user as $user ) {
						$this->topFans[$i]['name'] = (string) $user->name;
						$this->topFans[$i]['url'] = (string) $user->url;
						$this->topFans[$i]['image']['small'] = (string) $user->image[0];
						$this->topFans[$i]['image']['medium'] = (string) $user->image[1];
						$this->topFans[$i]['image']['large'] = (string) $user->image[2];
						$this->topFans[$i]['weight'] = (string) $user->weight;
						$i++;
					}
					
					return $this->topFans;
				}
				else {
					// No tagsare found
					$this->handleError(90, 'Artist has no top users');
					return FALSE;
				}
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
	
	public function getTopTags($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['artist']) ) {
			$vars = array(
				'method' => 'artist.gettoptags',
				'api_key' => $this->auth->apiKey,
				'artist' => $methodVars['artist']
			);
			
			if ( $call = $this->apiGetCall($vars) ) {
				if ( count($call->toptags->tag) > 0 ) {
					$i = 0;
					foreach ( $call->toptags->tag as $tag ) {
						$this->topTags[$i]['name'] = (string) $tag->name;
						$this->topTags[$i]['url'] = (string) $tag->url;
						$i++;
					}
					
					return $this->topTags;
				}
				else {
					// No tagsare found
					$this->handleError(90, 'Artist has no top tags');
					return FALSE;
				}
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
	
	public function getTopTracks($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['artist']) ) {
			$vars = array(
				'method' => 'artist.gettoptracks',
				'api_key' => $this->auth->apiKey,
				'artist' => $methodVars['artist']
			);
			
			if ( $call = $this->apiGetCall($vars) ) {
				if ( count($call->toptracks->track) > 0 ) {
					$i = 0;
					foreach ( $call->toptracks->track as $tracks ) {
						$this->topTracks[$i]['rank'] = (string) $tracks['rank'];
						$this->topTracks[$i]['name'] = (string) $tracks->name;
						$this->topTracks[$i]['playcount'] = (string) $tracks->playcount;
						$this->topTracks[$i]['streamable'] = (string) $tracks->streamable;
						$this->topTracks[$i]['url'] = (string) $tracks->url;
						$this->topTracks[$i]['image']['small'] = (string) $tracks->image[0];
						$this->topTracks[$i]['image']['medium'] = (string) $tracks->image[1];
						$this->topTracks[$i]['image']['large'] = (string) $tracks->image[2];
						$i++;
					}
					
					return $this->topTracks;
				}
				else {
					// No tagsare found
					$this->handleError(90, 'Artist has no top tracks');
					return FALSE;
				}
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
	
	public function removeTag($methodVars) {
		// Only allow full authed calls
		if ( $this->fullAuth == TRUE ) {
			// Check for required variables
			if ( !empty($methodVars['artist']) && !empty($methodVars['tag']) ) {
				$vars = array(
					'method' => 'artist.removetag',
					'api_key' => $this->auth->apiKey,
					'artist' => $methodVars['artist'],
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
				$this->handleError(91, 'You must include artist and tag varialbes in the call for this method');
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
		if ( !empty($methodVars['artist']) ) {
			$vars = array(
				'method' => 'artist.search',
				'api_key' => $this->auth->apiKey,
				'artist' => $methodVars['artist']
			);
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
					foreach ( $call->results->artistmatches->artist as $artist ) {
						$this->searchResults['results'][$i]['name'] = (string) $artist->name;
						$this->searchResults['results'][$i]['mbid'] = (string) $artist->mbid;
						$this->searchResults['results'][$i]['url'] = (string) $artist->url;
						$this->searchResults['results'][$i]['streamable'] = (string) $artist->streamable;
						$this->searchResults['results'][$i]['image']['small'] = (string) $artist->image_small;
						$this->searchResults['results'][$i]['image']['large'] = (string) $artist->image;
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
			$this->handleError(91, 'You must include artist varialbe in the call for this method');
			return FALSE;
		}
	}
	
	public function share($methodVars) {
		// Only allow full authed calls
		if ( $this->fullAuth == TRUE ) {
			// Check for required variables
			if ( !empty($methodVars['artist']) && !empty($methodVars['recipient']) ) {
				$vars = array(
					'method' => 'artist.share',
					'api_key' => $this->auth->apiKey,
					'artist' => $methodVars['artist'],
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
				$this->handleError(91, 'You must include artist and recipient varialbes in the call for this method');
				return FALSE;
			}
		}
		else {
			// Give a 92 error if not fully authed
			$this->handleError(92, 'Method requires full auth. Call auth.getSession using lastfmApiAuth class');
			return FALSE;
		}
	}
	
	public function shout($methodVars) {
		// Only allow full authed calls
		if ( $this->fullAuth == TRUE ) {
			// Check for required variables
			if ( !empty($methodVars['artist']) && !empty($methodVars['message']) ) {
				$vars = array(
					'method' => 'artist.shout',
					'api_key' => $this->auth->apiKey,
					'artist' => $methodVars['artist'],
					'message' => $methodVars['message'],
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
				$this->handleError(91, 'You must include artist and message variables in the call for this method');
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