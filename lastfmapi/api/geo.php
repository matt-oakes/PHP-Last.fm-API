<?php

class lastfmApiGeo extends lastfmApiBase {
	public $events;
	public $artists;
	public $tracks;
	
	private $apiKey;
	private $location;
	
	function __construct($apiKey, $location) {
		$this->apiKey = $apiKey;
		$this->location = $location;
	}
	
	public function getEvents($distance = '', $page = '') {
		$vars = array(
			'method' => 'geo.getevents',
			'api_key' => $this->apiKey,
			'location' => $this->location
		);
		if ( !empty($distance) ) {
			$vars['distance'] = $distance;
		}
		if ( !empty($page) ) {
			$vars['page'] = $page;
		}
		
		$call = $this->apiGetCall($vars);
		
		if ( $call['status'] == 'ok' ) {
			$this->events['location'] = (string) $call->events['location'];
			$this->events['currentPage'] = (string) $call->events['page'];
			$this->events['totalPages'] = (string) $call->events['totalpages'];
			$this->events['totalResults'] = (string) $call->events['total'];
			$i = 0;
			foreach ( $call->events->event as $event ) {
				$this->events['events'][$i]['id'] = (string) $event->id;
				$this->events['events'][$i]['title'] = (string) $event->title;
				$ii = 0;
				foreach ( $event->artists->artist as $artist ) {
					$this->events['events'][$i]['artists'][$ii] = (string) $artist;
					$ii++;
				}
				$this->events['events'][$i]['headliner'] = (string) $event->artists->headliner;
				$this->events['events'][$i]['venue']['name'] = (string) $event->venue->name;
				$this->events['events'][$i]['venue']['location']['city'] = (string) $event->venue->location->city;
				$this->events['events'][$i]['venue']['location']['country'] = (string) $event->venue->location->country;
				$this->events['events'][$i]['venue']['location']['street'] = (string) $event->venue->location->street;
				$this->events['events'][$i]['venue']['location']['postalcode'] = (string) $event->venue->location->postalcode;
				$geoTags = $event->venue->location->children('http://www.w3.org/2003/01/geo/wgs84_pos#');
				$this->events['events'][$i]['venue']['location']['point']['lat'] = (string) $geoTags->point->lat;
				$this->events['events'][$i]['venue']['location']['point']['long'] = (string) $geoTags->point->long;
				$this->events['events'][$i]['venue']['location']['timezone'] = (string) $event->venue->location->timezone;
				$this->events['events'][$i]['venue']['url'] = (string) $event->venue->url;
				$this->events['events'][$i]['startDate'] = strtotime(trim((string) $event->startDate));
				$this->events['events'][$i]['startTime'] = (string) $event->startTime;
				$this->events['events'][$i]['description'] = (string) $event->description;
				$this->events['events'][$i]['attendance'] = (string) $event->attendance;
				$this->events['events'][$i]['reviews'] = (string) $event->reviews;
				$this->events['events'][$i]['tag'] = (string) $event->tag;
				$this->events['events'][$i]['url'] = (string) $event->url;
				$i++;
			}
			
			return $this->events;
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
	
	// geo.getTopArtists is currently broken
	/* public function getTopArtists() {
		$vars = array(
			'method' => 'geo.gettopartists',
			'api_key' => $this->apiKey,
			'location' => $this->location
		);
		
		$call = $this->apiGetCall($vars);
		
		if ( $call['status'] == 'ok' ) {
			$i = 0;
			foreach ( $call->topartists->artist as $artist ) {
				$this->artists[$i]['name'] = (string) $artist->name;
				$this->artists[$i]['rank'] = (string) $artist['rank'];
				$this->artists[$i]['playcount'] = (string) $artist->playcount;
				$this->artists[$i]['mbid'] = (string) $artist->mbid;
				$this->artists[$i]['url'] = (string) $artist->url;
				$this->artists[$i]['streamable'] = (string) $artist->streamable;
				$this->artists[$i]['image']['small'] = (string) $artist->image[0];
				$this->artists[$i]['image']['medium'] = (string) $artist->image[1];
				$this->artists[$i]['image']['large'] = (string) $artist->image[2];
				$i++;
			}
			
			return $this->events;
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
	} */
}

?>