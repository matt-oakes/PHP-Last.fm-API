<?php

class lastfmApiEvent extends lastfmApiBase {
	public $info;
	public $config;
	
	private $auth;
	private $fullAuth;
	
	function __construct($auth, $fullAuth, $config) {
		$this->auth = $auth;
		$this->fullAuth = $fullAuth;
		$this->config = $config;
	}
	
	public function attend($methodVars) {
		// Only allow full authed calls
		if ( $this->fullAuth == TRUE ) {
			// Check for required variables
			if ( !empty($methodVars['eventId']) && !empty($methodVars['status']) ) {
				$vars = array(
					'method' => 'event.attend',
					'api_key' => $this->auth->apiKey,
					'event' => $methodVars['eventId'],
					'status' => $methodVars['status'],
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
				$this->handleError(91, 'You must include eventId and status varialbes in the call for this method');
				return FALSE;
			}
		}
		else {
			// Give a 92 error if not fully authed
			$this->handleError(92, 'Method requires full auth. Call auth.getSession using lastfmApiAuth class');
			return FALSE;
		}
	}
	
	public function getAttendees($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['eventId']) ) {
			$vars = array(
				'method' => 'event.getattendees',
				'api_key' => $this->auth->apiKey,
				'event' => $methodVars['eventId']
			);
			
			if ( $call = $this->apiGetCall($vars) ) {
				$this->attendees['id'] = (string) $call->attendees['event'];
				$this->attendees['total'] = (string) $call->attendees['total'];
				$i = 0;
				foreach ( $call->attendees->user as $user ) {
					$this->attendees['attendees'][$i]['name'] = (string) $user->name;
					$this->attendees['attendees'][$i]['realname'] = (string) $user->realname;
					$this->attendees['attendees'][$i]['images']['small'] = (string) $user->image[0];
					$this->attendees['attendees'][$i]['images']['medium'] = (string) $user->image[1];
					$this->attendees['attendees'][$i]['images']['large'] = (string) $user->image[2];
					$this->attendees['attendees'][$i]['url'] = (string) $user->url;
					$i++;
				}
				
				return $this->attendees;
			}
			else {
				return FALSE;
			}
		}
		else {
			// Give a 91 error if incorrect variables are used
			$this->handleError(91, 'You must include eventId and status varialbes in the call for this method');
			return FALSE;
		}
	}
	
	public function getInfo($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['eventId']) ) {
			$vars = array(
				'method' => 'event.getinfo',
				'api_key' => $this->auth->apiKey,
				'event' => $methodVars['eventId']
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
				$this->info['image']['medium'] = (string) $call->event->image[1];
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
		else {
			// Give a 91 error if incorrect variables are used
			$this->handleError(91, 'You must include eventId and status varialbes in the call for this method');
			return FALSE;
		}
	}
	
	public function getShouts($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['eventId']) ) {
			$vars = array(
				'method' => 'event.getshouts',
				'api_key' => $this->auth->apiKey,
				'event' => $methodVars['eventId']
			);
			
			if ( $call = $this->apiGetCall($vars) ) {
				$this->shouts['id'] = (string) $call->shouts['event'];
				$this->shouts['total'] = (string) $call->shouts['total'];
				$i = 0;
				foreach ( $call->shouts->shout as $shout ) {
					$this->shouts['shouts'][$i]['body'] = (string) $shout->body;
					$this->shouts['shouts'][$i]['author'] = (string) $shout->author;
					$this->shouts['shouts'][$i]['date'] = strtotime((string) $shout->date);
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
			$this->handleError(91, 'You must include eventId and status varialbes in the call for this method');
			return FALSE;
		}
	}
	
	public function share($methodVars) {
		// Only allow full authed calls
		if ( $this->fullAuth == TRUE ) {
			// Check for required variables
			if ( !empty($methodVars['eventId']) && !empty($methodVars['recipient']) ) {
				$vars = array(
					'method' => 'event.share',
					'api_key' => $this->auth->apiKey,
					'event' => $methodVars['eventId'],
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
				$this->handleError(91, 'You must include eventId and recipient variables in the call for this method');
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
			if ( !empty($methodVars['eventId']) && !empty($methodVars['message']) ) {
				$vars = array(
					'method' => 'event.shout',
					'api_key' => $this->auth->apiKey,
					'event' => $methodVars['eventId'],
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
				$this->handleError(91, 'You must include eventId and message variables in the call for this method');
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