<?php

class lastfmApiUser extends lastfmApiBase {
	public $events;
	
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
				$this->error['desc'] = 'This track has no similar tracks';
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