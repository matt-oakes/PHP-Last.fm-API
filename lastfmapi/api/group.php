<?php

class lastfmApiGroup extends lastfmApiBase {
	public $artists;
	public $members;
	public $albums;
	public $tracks;
	public $chartList;
	public $config;
	
	private $auth;
	private $fullAuth;
	
	function __construct($auth, $fullAuth, $config) {
		$this->auth = $auth;
		$this->fullAuth = $fullAuth;
		$this->config = $config;
	}
	
	public function getMembers($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['group']) ) {
			$vars = array(
				'method' => 'group.getmembers',
				'api_key' => $this->auth->apiKey,
				'group' => $methodVars['group']
			);
			if ( !empty($methodVars['page']) ) {
				$vars['page'] = $methodVars['page'];
			}
			if ( !empty($methodVars['limit']) ) {
				$vars['limit'] = $methodVars['limit'];
			}
			
			if ( $call = $this->apiGetCall($vars) ) {
				$i = 0;
				$this->members['for'] = (string) $call->members['for'];
				$this->members['page'] = (string) $call->members['page'];
				$this->members['perPage'] = (string) $call->members['perPage'];
				$this->members['totalPages'] = (string) $call->members['totalPages'];
				foreach ( $call->members->user as $user ) {
					$this->members['members'][$i]['name'] = (string) $user->name;
					$this->members['members'][$i]['realname'] = (string) $user->realname;
					$this->members['members'][$i]['url'] = (string) $user->url;
					$this->members['members'][$i]['image']['small'] = (string) $user->image[0];
					$this->members['members'][$i]['image']['medium'] = (string) $user->image[1];
					$this->members['members'][$i]['image']['large'] = (string) $user->image[2];
					$i++;
				}
				return $this->members;
			}
			else {
				return FALSE;
			}
		}
		else {
			// Give a 91 error if incorrect variables are used
			$this->handleError(91, 'You must include a group variable in the call for this method');
			return FALSE;
		}
	}
	
	public function getWeeklyAlbumChart($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['group']) ) {
			$vars = array(
				'method' => 'group.getweeklyalbumchart',
				'api_key' => $this->auth->apiKey,
				'group' => $methodVars['group']
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
					$this->albums[$i]['name'] = (string) $album->name;
					$this->albums[$i]['rank'] = (string) $album['rank'];
					$this->albums[$i]['artist']['name'] = (string) $album->artist;
					$this->albums[$i]['artist']['mbid'] = (string) $album->artist['mbid'];
					$this->albums[$i]['playcount'] = (string) $album->playcount;
					$this->albums[$i]['url'] = (string) $album->url;
					$i++;
				}
				return $this->albums;
			}
			else {
				return FALSE;
			}
		}
		else {
			// Give a 91 error if incorrect variables are used
			$this->handleError(91, 'You must include a group variable in the call for this method');
			return FALSE;
		}
	}
	
	public function getWeeklyArtistChart($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['group']) ) {
			$vars = array(
				'method' => 'group.getweeklyartistchart',
				'api_key' => $this->auth->apiKey,
				'group' => $methodVars['group']
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
					$this->artists[$i]['name'] = (string) $artist->name;
					$this->artists[$i]['rank'] = (string) $artist['rank'];
					$this->artists[$i]['mbid'] = (string) $artist->mbid;
					$this->artists[$i]['playcount'] = (string) $artist->playcount;
					$this->artists[$i]['url'] = (string) $artist->url;
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
			$this->handleError(91, 'You must include a group variable in the call for this method');
			return FALSE;
		}
	}
	
	public function getWeeklyChartList($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['group']) ) {
			$vars = array(
				'method' => 'group.getweeklychartlist',
				'api_key' => $this->auth->apiKey,
				'group' => $methodVars['group']
			);
			
			if ( $call = $this->apiGetCall($vars) ) {
				$i = 0;
				foreach ( $call->weeklychartlist->chart as $chart ) {
					$this->chartList[$i]['from'] = (string) $chart['from'];
					$this->chartList[$i]['to'] = (string) $chart['to'];
					$i++;
				}
				return $this->chartList;
			}
			else {
				return FALSE;
			}
		}
		else {
			// Give a 91 error if incorrect variables are used
			$this->handleError(91, 'You must include a group variable in the call for this method');
			return FALSE;
		}
	}
	
	public function getWeeklyTrackChart($methodVars) {
		// Check for required variables
		if ( !empty($methodVars['group']) ) {
			$vars = array(
				'method' => 'group.getweeklytrackchart',
				'api_key' => $this->auth->apiKey,
				'group' => $methodVars['group']
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
					$this->tracks[$i]['name'] = (string) $track->name;
					$this->tracks[$i]['rank'] = (string) $track['rank'];
					$this->tracks[$i]['artist']['name'] = (string) $track->artist;
					$this->tracks[$i]['artist']['mbid'] = (string) $track->artist['mbid'];
					$this->tracks[$i]['playcount'] = (string) $track->playcount;
					$this->tracks[$i]['url'] = (string) $track->url;
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
			$this->handleError(91, 'You must include a group variable in the call for this method');
			return FALSE;
		}
	}
}

?>