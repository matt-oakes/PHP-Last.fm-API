<?php

class lastfmApiGeo extends lastfmApi {
	public $events;
	public $artists;
	public $tracks;
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
		if ( !empty($methodVars['location']) ) {
			$vars = array(
				'method' => 'geo.getevents',
				'api_key' => $this->auth->apiKey,
				'location' => $methodVars['location']
			);
			if ( !empty($methodVars['distance']) ) {
				$vars['distance'] = $methodVars['distance'];
			}
			if ( !empty($methodVars['page']) ) {
				$vars['page'] = $methodVars['page'];
			}
			
			if ( $call = $this->apiGetCall($vars) ) {
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
			else {
				return FALSE;
			}
		}
		else {
			// Give a 91 error if incorrect variables are used
			$this->handleError(91, 'You must include a location varialbe in the call for this method');
			return FALSE;
		}
	}
	
	public function getTopArtists($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['country']) ) {
			$vars = array(
				'method' => 'geo.gettopartists',
				'api_key' => $this->auth->apiKey,
				'country' => $methodVars['country']
			);
			
			if ( $call = $this->apiGetCall($vars) ) {
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
				
				return $this->artists;
			}
			else {
				return FALSE;
			}
		}
		else {
			// Give a 91 error if incorrect variables are used
			$this->handleError(91, 'You must include a country varialbe in the call for this method');
			return FALSE;
		}
	}
	
	public function getTopTracks($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['country']) ) {
			$vars = array(
				'method' => 'geo.gettoptracks',
				'api_key' => $this->auth->apiKey,
				'country' => $methodVars['country']
			);
			
			if ( $call = $this->apiGetCall($vars) ) {
				$i = 0;
				foreach ( $call->toptracks->track as $track ) {
					$this->tracks[$i]['name'] = (string) $track->name;
					$this->tracks[$i]['rank'] = (string) $track['rank'];
					$this->tracks[$i]['playcount'] = (string) $track->playcount;
					$this->tracks[$i]['mbid'] = (string) $track->mbid;
					$this->tracks[$i]['url'] = (string) $track->url;
					$this->tracks[$i]['streamable'] = (string) $track->streamable;
					$this->tracks[$i]['fulltrack'] = (string) $track->streamable['fulltrack'];
					$this->tracks[$i]['artist']['name'] = (string) $track->artist->name;
					$this->tracks[$i]['artist']['mbid'] = (string) $track->artist->mbid;
					$this->tracks[$i]['artist']['url'] = (string) $track->artist->url;
					$this->tracks[$i]['image']['small'] = (string) $track->image[0];
					$this->tracks[$i]['image']['medium'] = (string) $track->image[1];
					$this->tracks[$i]['image']['large'] = (string) $track->image[2];
					$i++;
				}
				
				return $this->tracks;
			}
			else {
				return FALSE;
			}
		}
		else {
			// Give a 91 error if incorrect variables are used
			$this->handleError(91, 'You must include a country varialbe in the call for this method');
			return FALSE;
		}
	}
}

?>