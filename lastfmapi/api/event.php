<?php

class lastfmApiEvent extends lastfmApiBase {
	public $info;
	
	private $apiKey;
	private $eventId;
	
	function __construct($apiKey, $eventId) {
		$this->apiKey = $apiKey;
		$this->eventId = $eventId;
	}
	
	public function attend($status, $sessionKey, $secret) {
		if ( is_numeric($status) && $status >= 0 && $status <= 2 ) {
			$vars = array(
				'method' => 'event.attend',
				'api_key' => $this->apiKey,
				'event' => $this->eventId,
				'status' => $status,
				'sk' => $sessionKey
			);
			$sig = $this->apiSig($secret, $vars);
			$vars['api_sig'] = $sig;
			
			if ( $call = $this->apiPostCall($vars) ) {
				return TRUE;
			}
			else {
				return FALSE;
			}
		}
		else {
			$this->handleError(91, 'Incorrect use of status variable (0=Attending, 1=Maybe attending, 2=Not attending)');
			return FALSE;
		}
	}
	
	public function getInfo() {
		$vars = array(
			'method' => 'event.getinfo',
			'api_key' => $this->apiKey,
			'event' => $this->eventId
		);
		
		if ( $call = $this->apiGetCall($vars) ) {
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
		else {
			return FALSE;
		}
	}
}

?>