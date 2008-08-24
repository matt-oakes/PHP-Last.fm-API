<?php

class lastfmApiGroup extends lastfmApiBase {
	public $artists;
	public $albums;
	public $tracks;
	public $chartList;
	
	private $apiKey;
	private $group;
	
	function __construct($apiKey, $group) {
		$this->apiKey = $apiKey;
		$this->group = $group;
	}
	
	public function getWeeklyAlbumChart($from = '', $to = '') {
		$vars = array(
			'method' => 'group.getweeklyalbumchart',
			'api_key' => $this->apiKey,
			'group' => $this->group
		);
		if ( !empty($from) ) {
			$vars['from'] = $from;
		}
		if ( !empty($to) ) {
			$vars['to'] = $to;
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
	
	public function getWeeklyArtistChart($from = '', $to = '') {
		$vars = array(
			'method' => 'group.getweeklyartistchart',
			'api_key' => $this->apiKey,
			'group' => $this->group
		);
		if ( !empty($from) ) {
			$vars['from'] = $from;
		}
		if ( !empty($to) ) {
			$vars['to'] = $to;
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
	
	public function getWeeklyChartList() {
		$vars = array(
			'method' => 'group.getweeklychartlist',
			'api_key' => $this->apiKey,
			'group' => $this->group
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
	
	public function getWeeklyTrackChart($from = '', $to = '') {
		$vars = array(
			'method' => 'group.getweeklytrackchart',
			'api_key' => $this->apiKey,
			'group' => $this->group
		);
		if ( !empty($from) ) {
			$vars['from'] = $from;
		}
		if ( !empty($to) ) {
			$vars['to'] = $to;
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
}

?>