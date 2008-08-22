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
	
	private $apiKey;
	private $user;
	
	function __construct($apiKey, $user) {
		$this->apiKey = $apiKey;
		$this->user = $user;
	}
	
	public function getEvents() {
		$vars = array(
			'method' => 'user.getevents',
			'api_key' => $this->apiKey,
			'user' => $this->user
		);
		
		$call = $this->apiGetCall($vars);
		
		if ( $call['status'] == 'ok' ) {
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
				$this->error['code'] = 90;
				$this->error['desc'] = 'This user has no events';
				return FALSE;
			}
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
	
	public function getFriends($recentTracks = '', $limit = '') {
		$vars = array(
			'method' => 'user.getfriends',
			'api_key' => $this->apiKey,
			'user' => $this->user
		);
		if ( !empty($recentTracks) ) {
			$vars['recenttracks'] = 1;
		}
		if ( !empty($limit) ) {
			$vars['limit'] = $limit;
		}
		
		$call = $this->apiGetCall($vars);
		
		if ( $call['status'] == 'ok' ) {
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
				$this->error['code'] = 90;
				$this->error['desc'] = 'This user has no friends';
				return FALSE;
			}
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
	
	public function getInfo($sessionKey, $secret) {
		$vars = array(
			'method' => 'user.getinfo',
			'api_key' => $this->apiKey,
			'sk' => $sessionKey
		);
		$apiSig = $this->apiSig($secret, $vars);
		$vars['api_sig'] = $apiSig;
		
		$call = $this->apiGetCall($vars);
		
		if ( $call['status'] == 'ok' ) {
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
	
	public function getLovedTracks() {
		$vars = array(
			'method' => 'user.getlovedtracks',
			'api_key' => $this->apiKey,
			'user' => $this->user
		);
		
		$call = $this->apiGetCall($vars);
		
		if ( $call['status'] == 'ok' ) {
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
				$this->error['code'] = 90;
				$this->error['desc'] = 'This user has no loved tracks';
				return FALSE;
			}
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
	
	public function getNeighbours($limit = '') {
		$vars = array(
			'method' => 'user.getneighbours',
			'api_key' => $this->apiKey,
			'user' => $this->user
		);
		if ( !empty($limit) ) {
			$vars['limit'] = $limit;
		}
		
		$call = $this->apiGetCall($vars);
		
		if ( $call['status'] == 'ok' ) {
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
				$this->error['code'] = 90;
				$this->error['desc'] = 'This user has no neighbours';
				return FALSE;
			}
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
	
	public function getPastEvents($page = '', $limit = '') {
		$vars = array(
			'method' => 'user.getpastevents',
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
				$this->error['code'] = 90;
				$this->error['desc'] = 'This user has no past events';
				return FALSE;
			}
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
	
	public function getPlaylists() {
		$vars = array(
			'method' => 'user.getplaylists',
			'api_key' => $this->apiKey,
			'user' => $this->user
		);
		
		$call = $this->apiGetCall($vars);
		
		if ( $call['status'] == 'ok' ) {
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
				$this->error['code'] = 90;
				$this->error['desc'] = 'This user has no past events';
				return FALSE;
			}
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
	
	public function getRecentTracks() {
		$vars = array(
			'method' => 'user.getrecenttracks',
			'api_key' => $this->apiKey,
			'user' => $this->user
		);
		
		$call = $this->apiGetCall($vars);
		
		if ( $call['status'] == 'ok' ) {
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
				$this->error['code'] = 90;
				$this->error['desc'] = 'This user has no recent tracks';
				return FALSE;
			}
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
	
	public function getTopAlbums($period = '') {
		$vars = array(
			'method' => 'user.gettopalbums',
			'api_key' => $this->apiKey,
			'user' => $this->user
		);
		if ( $period == 3 || $period == 6 || $period == 12 ) {
			$vars['period'] = $period.'month';
		}
		else {
			$vars['period'] = 'overall';
		}
		
		
		$call = $this->apiGetCall($vars);
		
		if ( $call['status'] == 'ok' ) {
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
				$this->error['code'] = 90;
				$this->error['desc'] = 'This user has no top albums';
				return FALSE;
			}
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
	
	public function getTopArtists($period = '') {
		$vars = array(
			'method' => 'user.gettopartists',
			'api_key' => $this->apiKey,
			'user' => $this->user
		);
		if ( $period == 3 || $period == 6 || $period == 12 ) {
			$vars['period'] = $period.'month';
		}
		else {
			$vars['period'] = 'overall';
		}
		
		
		$call = $this->apiGetCall($vars);
		
		if ( $call['status'] == 'ok' ) {
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
				$this->error['code'] = 90;
				$this->error['desc'] = 'This user has no top artists';
				return FALSE;
			}
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
	
	public function getTopTags($limit = '') {
		$vars = array(
			'method' => 'user.gettoptags',
			'api_key' => $this->apiKey,
			'user' => $this->user
		);
		if ( !empty($limit) ) {
			$vars['limit'] = $limit;
		}
		
		
		$call = $this->apiGetCall($vars);
		
		if ( $call['status'] == 'ok' ) {
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
				$this->error['code'] = 90;
				$this->error['desc'] = 'This user has no top tags';
				return FALSE;
			}
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