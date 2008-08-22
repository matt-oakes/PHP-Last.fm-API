<?php

class lastfmApiUser extends lastfmApiBase {
	public $events;
	public $friends;
	public $info;
	
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
					$this->events[$i]['venue']['timezone'] = (string) $event->venue->timezone;
					$this->events[$i]['startDate'] = strtotime(trim((string) $event->startDate));
					$this->events[$i]['description'] = (string) $event->description;
					$this->events[$i]['image']['small'] = (string) $event->image[0];
					$this->events[$i]['image']['medium'] = (string) $event->image[1];
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
			$vars['limit'] = 1;
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
}

?>