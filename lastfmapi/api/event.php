<?php

class lastfmApiEvent extends lastfmApiBase {
	public $info;
	
	private $apiKey;
	private $eventId;
	
	function __construct($apiKey, $eventId) {
		$this->apiKey = $apiKey;
		$this->eventId = $eventId;
	}
	
	public function getInfo() {
		$vars = array(
			'method' => 'event.getinfo',
			'api_key' => $this->apiKey,
			'event' => $this->eventId
		);
		
		$call = $this->apiGetCall($vars);
		
		if ( $call['status'] == 'ok' ) {
			$this->info['id'] = (string) $call->event->id;
			$this->info['title'] = (string) $call->event->title;
			$ii = 0;
			foreach ( $call->event->artists->artist as $artist ) {
				$this->info['artists'][$ii] = (string) $artist;
				$ii++;
			}
			$this->info['headliner'] = (string) $call->event->artists->headliner;
			$this->info['venue']['name'] = (string) $call->event->venue->name;
			$this->info['venue']['location']['city'] = (string) $call->event->venue->location->city;
			$this->info['venue']['location']['country'] = (string) $call->event->venue->location->country;
			$this->info['venue']['location']['street'] = (string) $call->event->venue->location->street;
			$this->info['venue']['location']['postcode'] = (string) $call->event->venue->location->postalcode;
			$geopoint =  $call->event->venue->location->children('http://www.w3.org/2003/01/geo/wgs84_pos#');
			$this->info['venue']['location']['geopoint']['lat'] = (string) $geopoint->point->lat;
			$this->info['venue']['location']['geopoint']['long'] = (string) $geopoint->point->long;
			$this->info['venue']['location']['timezone'] = (string) $call->event->venue->location->timezone;
			$this->info['venue']['url'] = (string) $call->venue->url;
			$this->info['startdate'] = strtotime(trim((string) $call->event->startDate));
			$this->info['description'] = (string) $call->event->description;
			$this->info['image']['small'] = (string) $call->event->image[0];
			$this->info['image']['mendium'] = (string) $call->event->image[1];
			$this->info['image']['large'] = (string) $call->event->image[2];
			$this->info['attendance'] = (string) $call->event->attendance;
			$this->info['reviews'] = (string) $call->event->reviews;
			$this->info['tag'] = (string) $call->event->tag;
			$this->info['url'] = (string) $call->event->url;
			
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