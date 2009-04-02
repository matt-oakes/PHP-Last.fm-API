<?php

class lastfmApiUser extends lastfmApiBase {
	public $events;
	public $friends;
	public $info;
	public $lovedtracks;
	public $neighbours;
	public $pastevents;
	public $topalbums;
	public $topartists;
	public $toptags;
	public $recommendedartists;
	public $weeklyalbums;
	public $weeklyartists;
	public $weeklychartlist;
	public $weeklytracks;
	public $config;
	
	private $auth;
	private $fullAuth;
	
	function __construct($auth, $fullAuth, $config) {
		$this->auth = $auth;
		$this->fullAuth = $fullAuth;
		$this->config = $config;
	}
	
	public function getEvents($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['user']) ) {
			$vars = array(
				'method' => 'user.getevents',
				'api_key' => $this->auth->apiKey,
				'user' => $methodVars['user']
			);
			
			if ( $call = $this->apiGetCall($vars) ) {
				if ( $call->events['total'] > 0 ) {
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
						$this->events[$i]['venue']['location']['postalcode'] = (string) $event->venue->location->postalcode;
						$geoPoints = $event->venue->location->children('http://www.w3.org/2003/01/geo/wgs84_pos#');
						$this->events[$i]['venue']['location']['geopoint']['lat'] = (string) $geoPoints->point->lat;
						$this->events[$i]['venue']['location']['geopoint']['long'] = (string) $geoPoints->point->long;
						$this->events[$i]['venue']['timezone'] = (string) $event->venue->location->timezone;
						$this->events[$i]['startDate'] = strtotime(trim((string) $event->startDate));
						$this->events[$i]['description'] = (string) $event->description;
						$this->events[$i]['images']['small'] = (string) $event->image[0];
						$this->events[$i]['images']['medium'] = (string) $event->image[1];
						$this->events[$i]['images']['large'] = (string) $event->image[2];
						$this->events[$i]['attendance'] = (string) $event->attendance;
						$this->events[$i]['reviews'] = (string) $event->reviews;
						$this->events[$i]['tag'] = (string) $event->tag;
						$this->events[$i]['url'] = (string) $event->url;
						$i++;
					}
					return $this->events;
				}
				else {
					$this->handleError(90, 'This user has no events');
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
	
	public function getFriends($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['user']) ) {
			$vars = array(
				'method' => 'user.getfriends',
				'api_key' => $this->auth->apiKey,
				'user' => $methodVars['user']
			);
			if ( !empty($methodVars['recentTracks']) ) {
				$vars['recenttracks'] = 1;
			}
			if ( !empty($methodVars['limit']) ) {
				$vars['limit'] = $methodVars['limit'];
			}
			
			if ( $call = $this->apiGetCall($vars) ) {
				if ( count($call->friends->user) > 0 ) {
					$i = 0;
					foreach ( $call->friends->user as $user ) {
						$this->friends[$i]['name'] = (string) $user->name;
						$this->friends[$i]['images']['small'] = (string) $user->image[0];
						$this->friends[$i]['images']['medium'] = (string) $user->image[1];
						$this->friends[$i]['images']['large'] = (string) $user->image[2];
						$this->friends[$i]['url'] = (string) $user->url;
						if ( !empty($recentTracks) ) {
							$this->friends[$i]['recenttrack']['artist']['name'] = (string) $user->recenttrack->artist->name;
							$this->friends[$i]['recenttrack']['artist']['mbid'] = (string) $user->recenttrack->artist->mbid;
							$this->friends[$i]['recenttrack']['artist']['url'] = (string) $user->recenttrack->artist->url;
							$this->friends[$i]['recenttrack']['name'] = (string) $user->recenttrack->name;
							$this->friends[$i]['recenttrack']['mbid'] = (string) $user->recenttrack->mbid;
							$this->friends[$i]['recenttrack']['url'] = (string) $user->recenttrack->url;
						}
						$i++;
					}
					
					return $this->friends;
				}
				else {
					$this->handleError(90, 'This user has no friends');
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
	
	public function getInfo() {
		// Only allow full authed calls
		if ( $this->fullAuth == TRUE ) {
			$vars = array(
				'method' => 'user.getinfo',
				'api_key' => $this->auth->apiKey,
				'sk' => $this->auth->sessionKey
			);
			$apiSig = $this->apiSig($this->auth->secret, $vars);
			$vars['api_sig'] = $apiSig;
			
			if ( $call = $this->apiGetCall($vars) ) {
				$this->info['name'] = (string) $call->user->name;
				$this->info['url'] = (string) $call->user->url;
				$this->info['image'] = (string) $call->user->image;
				$this->info['lang'] = (string) $call->user->lang;
				$this->info['country'] = (string) $call->user->country;
				$this->info['age'] = (string) $call->user->age;
				$this->info['gender'] = (string) $call->user->gender;
				$this->info['subscriber'] = (string) $call->user->subscriber;
				$this->info['playcount'] = (string) $call->user->playcount;
				$this->info['playlists'] = (string) $call->user->playlists;
				
				return $this->info;
			}
			else {
				return FALSE;
			}
		}
		else {
			// Give a 92 error if not fully authed
			$this->handleError(92, 'Method requires full auth. Call auth.getSession using lastfmApiAuth class');
			return FALSE;
		}
	}
	
	public function getLovedTracks($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['user']) ) {
			$vars = array(
				'method' => 'user.getlovedtracks',
				'api_key' => $this->auth->apiKey,
				'user' => $methodVars['user']
			);
			
			if ( $call = $this->apiGetCall($vars) ) {
				if ( count($call->lovedtracks->track) > 0 ) {
					$i = 0;
					foreach ( $call->lovedtracks->track as $track ) {
						$this->lovedtracks[$i]['name'] = (string) $track->name;
						$this->lovedtracks[$i]['mbid'] = (string) $track->mbid;
						$this->lovedtracks[$i]['url'] = (string) $track->url;
						$this->lovedtracks[$i]['date'] = (string) $track->date['uts'];
						$this->lovedtracks[$i]['artist']['name'] = (string) $track->artist->name;
						$this->lovedtracks[$i]['artist']['mbid'] = (string) $track->artist->mbid;
						$this->lovedtracks[$i]['artist']['url'] = (string) $track->artist->url;
						$this->lovedtracks[$i]['images']['small'] = (string) $track->image[0];
						$this->lovedtracks[$i]['images']['medium'] = (string) $track->image[1];
						$this->lovedtracks[$i]['images']['large'] = (string) $track->image[2];
						$i++;
					}
					
					return $this->lovedtracks;
				}
				else {
					$this->handleError(90, 'This user has no loved tracks');
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
	
	public function getNeighbours($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['user']) ) {
			$vars = array(
				'method' => 'user.getneighbours',
				'api_key' => $this->auth->apiKey,
				'user' => $methodVars['user']
			);
			if ( !empty($methodVars['limit']) ) {
				$vars['limit'] = $methodVars['limit'];
			}
			
			if ( $call = $this->apiGetCall($vars) ) {
				if ( count($call->neighbours->user) > 0 ) {
					$i = 0;
					foreach ( $call->neighbours->user as $user ) {
						$this->neighbours[$i]['name'] = (string) $user->name;
						$this->neighbours[$i]['url'] = (string) $user->url;
						$this->neighbours[$i]['image'] = (string) $user->image;
						$this->neighbours[$i]['match'] = (string) $user->match;
						$i++;
					}
					
					return $this->neighbours;
				}
				else {
					$this->handleError(90, 'This user has no neighbours');
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
	
	public function getPastEvents($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['user']) ) {
			$vars = array(
				'method' => 'user.getpastevents',
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
				if ( $call->events['total'] > 0 ) {
					$i = 0;
					foreach ( $call->events->event as $event ) {
						$this->pastevents[$i]['id'] = (string) $event->id;
						$this->pastevents[$i]['title'] = (string) $event->title;
						$ii = 0;
						foreach ( $event->artists->artist as $artist ) {
							$this->pastevents[$i]['artists'][$ii] = (string) $artist;
							$ii++;
						}
						$this->pastevents[$i]['headliner'] = (string) $event->artists->headliner;
						$this->pastevents[$i]['venue']['name'] = (string) $event->venue->name;
						$this->pastevents[$i]['venue']['location']['city'] = (string) $event->venue->location->city;
						$this->pastevents[$i]['venue']['location']['country'] = (string) $event->venue->location->country;
						$this->pastevents[$i]['venue']['location']['street'] = (string) $event->venue->location->street;
						$this->pastevents[$i]['venue']['location']['postalcode'] = (string) $event->venue->location->postalcode;
						$geoPoints = $event->venue->location->children('http://www.w3.org/2003/01/geo/wgs84_pos#');
						$this->pastevents[$i]['venue']['location']['geopoint']['lat'] = (string) $geoPoints->point->lat;
						$this->pastevents[$i]['venue']['location']['geopoint']['long'] = (string) $geoPoints->point->long;
						$this->pastevents[$i]['startDate'] = strtotime(trim((string) $event->startDate));
						$this->pastevents[$i]['description'] = (string) $event->description;
						$this->pastevents[$i]['images']['small'] = (string) $event->image[0];
						$this->pastevents[$i]['images']['medium'] = (string) $event->image[1];
						$this->pastevents[$i]['images']['large'] = (string) $event->image[2];
						$this->pastevents[$i]['attendance'] = (string) $event->attendance;
						$this->pastevents[$i]['reviews'] = (string) $event->reviews;
						$this->pastevents[$i]['tag'] = (string) $event->tag;
						$this->pastevents[$i]['url'] = (string) $event->url;
						$i++;
					}
					
					return $this->pastevents;
				}
				else {
					$this->handleError(90, 'This user has no past events');
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
	
	public function getPlaylists($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['user']) ) {
			$vars = array(
				'method' => 'user.getplaylists',
				'api_key' => $this->auth->apiKey,
				'user' => $methodVars['user']
			);
			
			if ( $call = $this->apiGetCall($vars) ) {
				if ( count($call->playlists->playlist) > 0 ) {
					$i = 0;
					foreach ( $call->playlists->playlist as $playlist ) {
						$this->playlists[$i]['id'] = (string) $playlist->id;
						$this->playlists[$i]['title'] = (string) $playlist->title;
						$this->playlists[$i]['date'] = strtotime(trim((string) $playlist->date));
						$this->playlists[$i]['size'] = (string) $playlist->size;
						$this->playlists[$i]['streamalbe'] = (string) $playlist->streamable;
						$this->playlists[$i]['creator'] = (string) $playlist->creator;
						$i++;
					}
					
					return $this->playlists;
				}
				else {
					$this->handleError(90, 'This user has no past events');
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
	
	public function getRecentTracks($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['user']) ) {
			$vars = array(
				'method' => 'user.getrecenttracks',
				'api_key' => $this->auth->apiKey,
				'user' => $methodVars['user']
			);
			
			if ( $call = $this->apiGetCall($vars) ) {
				if ( count($call->recenttracks->track) > 0 ) {
					$i = 0;
					foreach ( $call->recenttracks->track as $track ) {
						$this->recenttracks[$i]['name'] = (string) $track->name;
						if ( isset($track['nowplaying']) ) {
							$this->recenttracks[$i]['nowplaying'] = TRUE;
						}
						$this->recenttracks[$i]['mbid'] = (string) $track->mbid;
						$this->recenttracks[$i]['url'] = (string) $track->url;
						$this->recenttracks[$i]['date'] = (string) $track->date['uts'];
						$this->recenttracks[$i]['streamable'] = (string) $track->streamable;
						$this->recenttracks[$i]['artist']['name'] = (string) $track->artist;
						$this->recenttracks[$i]['artist']['mbid'] = (string) $track->artist['mbid'];
						$this->recenttracks[$i]['album']['name'] = (string) $track->album;
						$this->recenttracks[$i]['album']['mbid'] = (string) $track->album['mbid'];
						$this->recenttracks[$i]['images']['small'] = (string) $track->image[0];
						$this->recenttracks[$i]['images']['medium'] = (string) $track->image[1];
						$this->recenttracks[$i]['images']['large'] = (string) $track->image[2];
						$i++;
					}
					
					return $this->recenttracks;
				}
				else {
					$this->handleError(90, 'This user has no recent tracks');
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
	
	public function getRecommendedArtists($methodVars = '') {
		// Only allow full authed calls
		if ( $this->fullAuth == TRUE ) {
			$vars = array(
				'method' => 'user.getrecommendedartists',
				'api_key' => $this->auth->apiKey,
				'sk' => $this->auth->sessionKey
			);
			if ( !empty($methodVars['page']) ) {
				$vars['page'] = $methodVars['page'];
			}
			if ( !empty($methodVars['limit']) ) {
				$vars['limit'] = $methodVars['limit'];
			}
			$apiSig = $this->apiSig($this->auth->secret, $vars);
			$vars['api_sig'] = $apiSig;
			
			if ( $call = $this->apiGetCall($vars) ) {
				if ( count($call->recommendations->artist) > 0 ) {
					$this->recommendedartists['user'] = (string) $call->recommendations['user'];
					$this->recommendedartists['page'] = (string) $call->recommendations['page'];
					$this->recommendedartists['perPage'] = (string) $call->recommendations['perPage'];
					$this->recommendedartists['totalPages'] = (string) $call->recommendations['totalPages'];
					$this->recommendedartists['total'] = (string) $call->recommendations['total'];
					
					$i = 0;
					foreach ( $call->recommendations->artist as $artist ) {
						$this->recommendedartists['artists'][$i]['name'] = (string) $artist->name;
						$this->recommendedartists['artists'][$i]['mbid'] = (string) $artist->mbid;
						$this->recommendedartists['artists'][$i]['url'] = (string) $artist->url;
						$this->recommendedartists['artists'][$i]['streamable'] = (string) $artist->streamable;
						$this->recommendedartists['artists'][$i]['image']['small'] = (string) $artist->image[0];
						$this->recommendedartists['artists'][$i]['image']['medium'] = (string) $artist->image[1];
						$this->recommendedartists['artists'][$i]['image']['large'] = (string) $artist->image[2];
						$i++;
					}
				
					return $this->recommendedartists;
				}
				else {
					$this->handleError(90, 'This user has no recommendations');
					return FALSE;
				}
			}
			else {
				return FALSE;
			}
		}
		else {
			// Give a 92 error if not fully authed
			$this->handleError(92, 'Method requires full auth. Call auth.getSession using lastfmApiAuth class');
			return FALSE;
		}
	}
	
	public function getRecommendedEvents($methodVars = '') {
		// Only allow full authed calls
		if ( $this->fullAuth == TRUE ) {
			$vars = array(
				'method' => 'user.getrecommendedevents',
				'api_key' => $this->auth->apiKey,
				'sk' => $this->auth->sessionKey
			);
			if ( !empty($methodVars['page']) ) {
				$vars['page'] = $methodVars['page'];
			}
			if ( !empty($methodVars['limit']) ) {
				$vars['limit'] = $methodVars['limit'];
			}
			$apiSig = $this->apiSig($this->auth->secret, $vars);
			$vars['api_sig'] = $apiSig;
			
			if ( $call = $this->apiGetCall($vars) ) {
				if ( count($call->events->event) > 0 ) {
					$this->recommendedevents['user'] = (string) $call->events['user'];
					$this->recommendedevents['page'] = (string) $call->events['page'];
					$this->recommendedevents['perPage'] = (string) $call->events['perPage'];
					$this->recommendedevents['totalPages'] = (string) $call->events['totalPages'];
					$this->recommendedevents['total'] = (string) $call->events['total'];
					
					$i = 0;
					foreach ( $call->events->event as $event ) {
						$this->recommendedevents['events'][$i]['id'] = (string) $event->id;
						$this->recommendedevents['events'][$i]['title'] = (string) $event->title;
						$ii = 0;
						foreach ( $event->artists->artist as $artist ) {
							$this->recommendedevents['events'][$i]['artists'][$ii] = (string) $artist;
							$ii++;
						}
						$this->recommendedevents['events'][$i]['headliner'] = (string) $event->artists->headliner;
						$this->recommendedevents['events'][$i]['venue']['name'] = (string) $event->venue->name;
						$this->recommendedevents['events'][$i]['venue']['location']['city'] = (string) $event->venue->location->city;
						$this->recommendedevents['events'][$i]['venue']['location']['country'] = (string) $event->venue->location->country;
						$this->recommendedevents['events'][$i]['venue']['location']['street'] = (string) $event->venue->location->street;
						$this->recommendedevents['events'][$i]['venue']['location']['postcode'] = (string) $event->venue->location->postalcode;
						$geopoint =  $event->venue->location->children('http://www.w3.org/2003/01/geo/wgs84_pos#');
						$this->recommendedevents['events'][$i]['venue']['location']['geopoint']['lat'] = (string) $geopoint->point->lat;
						$this->recommendedevents['events'][$i]['venue']['location']['geopoint']['long'] = (string) $geopoint->point->long;
						$this->recommendedevents['events'][$i]['venue']['location']['timezone'] = (string) $event->venue->location->timezone;
						$this->recommendedevents['events'][$i]['venue']['url'] = (string) $call->venue->url;
						$this->recommendedevents['events'][$i]['startdate'] = strtotime(trim((string) $event->startDate));
						$this->recommendedevents['events'][$i]['description'] = (string) $event->description;
						$this->recommendedevents['events'][$i]['image']['small'] = (string) $event->image[0];
						$this->recommendedevents['events'][$i]['image']['medium'] = (string) $event->image[1];
						$this->recommendedevents['events'][$i]['image']['large'] = (string) $event->image[2];
						$this->recommendedevents['events'][$i]['attendance'] = (string) $event->attendance;
						$this->recommendedevents['events'][$i]['reviews'] = (string) $event->reviews;
						$this->recommendedevents['events'][$i]['tag'] = (string) $event->tag;
						$this->recommendedevents['events'][$i]['url'] = (string) $event->url;
						$i++;
					}
				
					return $this->recommendedevents;
				}
				else {
					$this->handleError(90, 'This user has no recommendations');
					return FALSE;
				}
			}
			else {
				return FALSE;
			}
		}
		else {
			// Give a 92 error if not fully authed
			$this->handleError(92, 'Method requires full auth. Call auth.getSession using lastfmApiAuth class');
			return FALSE;
		}
	}
	
	public function getShouts($methodVars) {
		if ( !empty($methodVars['user']) ) {
			$vars = array(
				'method' => 'user.getshouts',
				'api_key' => $this->auth->apiKey,
				'user' => $methodVars['user']
			);
			
			if ( $call = $this->apiGetCall($vars) ) {
				$this->shouts['user'] = (string)$call->shouts['user'];
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
	
	public function getTopAlbums($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['user']) ) {
			$vars = array(
				'method' => 'user.gettopalbums',
				'api_key' => $this->auth->apiKey,
				'user' => $methodVars['user']
			);
			if ( !empty($methodVars['period']) && ( $methodVars['period'] == 3 || $methodVars['period'] == 6 || $methodVars['period'] == 12 ) ) {
				$vars['period'] = $methodVars['period'].'month';
			}
			else {
				$vars['period'] = 'overall';
			}
			
			if ( $call = $this->apiGetCall($vars) ) {
				if ( count($call->topalbums->album) > 0 ) {
					$i = 0;
					foreach ( $call->topalbums->album as $album ) {
						$this->topalbums[$i]['name'] = (string) $album->name;
						$this->topalbums[$i]['playcount'] = (string) $album->playcount;
						$this->topalbums[$i]['mbid'] = (string) $album->mbid;
						$this->topalbums[$i]['url'] = (string) $album->url;
						$this->topalbums[$i]['artist']['name'] = (string) $album->artist->name;
						$this->topalbums[$i]['artist']['mbid'] = (string) $album->artist->mbid;
						$this->topalbums[$i]['artist']['url'] = (string) $album->artist->url;
						$this->topalbums[$i]['images']['small'] = (string) $album->image[0];
						$this->topalbums[$i]['images']['medium'] = (string) $album->image[1];
						$this->topalbums[$i]['images']['large'] = (string) $album->image[2];
						$i++;
					}
					
					return $this->topalbums;
				}
				else {
					$this->handleError(90, 'This user has no top albums');
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
	
	public function getTopArtists($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['user']) ) {
			$vars = array(
				'method' => 'user.gettopartists',
				'api_key' => $this->auth->apiKey,
				'user' => $methodVars['user']
			);
			if ( !empty($methodVars['period']) && ( $methodVars['period'] == 3 || $methodVars['period'] == 6 || $methodVars['period'] == 12 ) ) {
				$vars['period'] = $methodVars['period'].'month';
			}
			else {
				$vars['period'] = 'overall';
			}
			
			if ( $call = $this->apiGetCall($vars) ) {
				if ( count($call->topartists->artist) > 0 ) {
					$i = 0;
					foreach ( $call->topartists->artist as $artist ) {
						$this->topartists[$i]['name'] = (string) $artist->name;
						$this->topartists[$i]['rank'] = (string) $artist['rank'];
						$this->topartists[$i]['playcount'] = (string) $artist->playcount;
						$this->topartists[$i]['mbid'] = (string) $artist->mbid;
						$this->topartists[$i]['url'] = (string) $artist->url;
						$this->topartists[$i]['streamable'] = (string) $artist->streamable;
						$this->topartists[$i]['images']['small'] = (string) $artist->image[0];
						$this->topartists[$i]['images']['medium'] = (string) $artist->image[1];
						$this->topartists[$i]['images']['large'] = (string) $artist->image[2];
						$i++;
					}
					
					return $this->topartists;
				}
				else {
					$this->handleError(90, 'This user has no top artists');
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
	
	public function getTopTags($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['user']) ) {
			$vars = array(
				'method' => 'user.gettoptags',
				'api_key' => $this->auth->apiKey,
				'user' => $methodVars['user']
			);
			if ( !empty($methodVars['limit']) ) {
				$vars['limit'] = $methodVars['limit'];
			}
			
			if ( $call = $this->apiGetCall($vars) ) {
				if ( count($call->toptags->tag) > 0 ) {
					$i = 0;
					foreach ( $call->toptags->tag as $tag ) {
						$this->toptags[$i]['name'] = (string) $tag->name;
						$this->toptags[$i]['count'] = (string) $tag->count;
						$this->toptags[$i]['url'] = (string) $tag->url;
						$i++;
					}
					
					return $this->toptags;
				}
				else {
					$this->handleError(90, 'This user has no top tags');
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
	
	public function getTopTracks($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['user']) ) {
			$vars = array(
				'method' => 'user.gettoptracks',
				'api_key' => $this->auth->apiKey,
				'user' => $methodVars['user']
			);
			if ( !empty($methodVars['period']) && ( $methodVars['period'] == 3 || $methodVars['period'] == 6 || $methodVars['period'] == 12 ) ) {
				$vars['period'] = $methodVars['period'].'month';
			}
			else {
				$vars['period'] = 'overall';
			}
			
			if ( $call = $this->apiGetCall($vars) ) {
				if ( count($call->toptracks->track) > 0 ) {
					$i = 0;
					foreach ( $call->toptracks->track as $track ) {
						$this->toptracks[$i]['name'] = (string) $track->name;
						$this->toptracks[$i]['rank'] = (string) $track['rank'];
						$this->toptracks[$i]['playcount'] = (string) $track->playcount;
						$this->toptracks[$i]['mbid'] = (string) $track->mbid;
						$this->toptracks[$i]['url'] = (string) $track->url;
						$this->toptracks[$i]['streamable'] = (string) $track->streamable;
						$this->toptracks[$i]['fulltrack'] = (string) $track->streamable['fulltrack'];
						$this->toptracks[$i]['artist']['name'] = (string) $track->artist->name;
						$this->toptracks[$i]['artist']['mbid'] = (string) $track->artist->mbid;
						$this->toptracks[$i]['artist']['url'] = (string) $track->artist->url;
						$this->toptracks[$i]['images']['small'] = (string) $track->image[0];
						$this->toptracks[$i]['images']['medium'] = (string) $track->image[1];
						$this->toptracks[$i]['images']['large'] = (string) $track->image[2];
						$i++;
					}
					
					return $this->toptracks;
				}
				else {
					$this->handleError(90, 'This user has no top tracks');
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
	
	public function getWeeklyAlbumChart($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['user']) ) {
			$vars = array(
				'method' => 'user.getweeklyalbumchart',
				'api_key' => $this->auth->apiKey,
				'user' => $methodVars['user']
			);
			if ( !empty($methodVars['from']) ) {
				$vars['from'] = $methodVars['from'];
			}
			if ( !empty($methodVars['to']) ) {
				$vars['to'] = $methodVars['to'];
			}
			
			if ( $call = $this->apiGetCall($vars) ) {
				$i = 0;
				foreach ( $call->weeklyalbumchart->album as $album ) {
					$this->weeklyalbums[$i]['name'] = (string) $album->name;
					$this->weeklyalbums[$i]['rank'] = (string) $album['rank'];
					$this->weeklyalbums[$i]['artist']['name'] = (string) $album->artist;
					$this->weeklyalbums[$i]['artist']['mbid'] = (string) $album->artist['mbid'];
					$this->weeklyalbums[$i]['mbid'] = (string) $album->mbid;
					$this->weeklyalbums[$i]['playcount'] = (string) $album->playcount;
					$this->weeklyalbums[$i]['url'] = (string) $album->url;
					$i++;
				}
				
				return $this->weeklyalbums;
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
	
	public function getWeeklyArtistChart($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['user']) ) {
			$vars = array(
				'method' => 'user.getweeklyartistchart',
				'api_key' => $this->auth->apiKey,
				'user' => $methodVars['user']
			);
			if ( !empty($methodVars['from']) ) {
				$vars['from'] = $methodVars['from'];
			}
			if ( !empty($methodVars['to']) ) {
				$vars['to'] = $methodVars['to'];
			}
			
			if ( $call = $this->apiGetCall($vars) ) {
				$i = 0;
				foreach ( $call->weeklyartistchart->artist as $artist ) {
					$this->weeklyartists[$i]['name'] = (string) $artist->name;
					$this->weeklyartists[$i]['rank'] = (string) $artist['rank'];
					$this->weeklyartists[$i]['mbid'] = (string) $artist->mbid;
					$this->weeklyartists[$i]['playcount'] = (string) $artist->playcount;
					$this->weeklyartists[$i]['url'] = (string) $artist->url;
					$i++;
				}
				
				return $this->weeklyartists;
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
	
	public function getWeeklyChartList($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['user']) ) {
			$vars = array(
				'method' => 'user.getweeklychartlist',
				'api_key' => $this->auth->apiKey,
				'user' => $methodVars['user']
			);
			
			if ( $call = $this->apiGetCall($vars) ) {
				$i = 0;
				foreach ( $call->weeklychartlist->chart as $chart ) {
					$this->weeklychartlist[$i]['from'] = (string) $chart['from'];
					$this->weeklychartlist[$i]['to'] = (string) $chart['to'];
					$i++;
				}
				
				return $this->weeklychartlist;
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
	
	public function getWeeklyTrackChart($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['user']) ) {
			$vars = array(
				'method' => 'user.getweeklytrackchart',
				'api_key' => $this->auth->apiKey,
				'user' => $methodVars['user']
			);
			if ( !empty($methodVars['from']) ) {
				$vars['from'] = $methodVars['from'];
			}
			if ( !empty($methodVars['to']) ) {
				$vars['to'] = $methodVars['to'];
			}
			
			if ( $call = $this->apiGetCall($vars) ) {
				$i = 0;
				foreach ( $call->weeklytrackchart->track as $track ) {
					$this->weeklytracks[$i]['name'] = (string) $track->name;
					$this->weeklytracks[$i]['rank'] = (string) $track['rank'];
					$this->weeklytracks[$i]['artist']['name'] = (string) $track->artist;
					$this->weeklytracks[$i]['artist']['mbid'] = (string) $track->artist['mbid'];
					$this->weeklytracks[$i]['mbid'] = (string) $track->mbid;
					$this->weeklytracks[$i]['playcount'] = (string) $track->playcount;
					$this->weeklytracks[$i]['url'] = (string) $track->url;
					$i++;
				}
				
				return $this->weeklytracks;
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
}

?>