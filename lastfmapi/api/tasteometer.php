<?php

class lastfmApiTasteometer extends lastfmApiBase {
	public $results;
	
	private $auth;
	private $fullAuth;
	
	function __construct($auth, $fullAuth) {
		$this->auth = $auth;
		$this->fullAuth = $fullAuth;
	}
	
	public function compare($methodVars) {
		// Check for required variables
		if ( !empty($methodVars[1]['type']) && !empty($methodVars[1]['value']) &&  !empty($methodVars[2]['type']) && !empty($methodVars[2]['value']) ) {	
			if ( $methodVars[1]['type'] == 'artists' && is_array($methodVars[1]['value']) ) {
				$value1 = '';
				foreach ( $methodVars[1]['value'] as $artist ) {
					$value1 .= $artist.',';
				}
				$value1 = substr($value1, 0, -1);
			}
			else {
				$value1 = $methodVars[1]['value'];
			}
			
			if ( $methodVars[2]['type'] == 'artists' && is_array($methodVars[2]['value']) ) {
				$value2 = '';
				foreach ( $methodVars[2]['value'] as $artist ) {
					$value2 .= $artist.',';
				}
				$value2 = substr($value2, 0, -1);
			}
			else {
				$value2 = $methodVars[2]['value'];
			}
			
			$vars = array(
				'method' => 'tasteometer.compare',
				'api_key' => $this->auth->apiKey,
				'type1' => $methodVars[1]['type'],
				'value1' => $value1,
				'type2' => $methodVars[2]['type'],
				'value2' => $value2
			);
			
			if ( $call = $this->apiGetCall($vars) ) {
				$this->result = '';
				
				$this->result['score'] = (string) $call->comparison->result->score;
				$this->result['matches'] = (string) $call->comparison->result->artists['matches'];
				$i = 0;
				foreach ( $call->comparison->result->artists->artist as $artist ) {
					$this->result['artists'][$i]['name'] = (string) $artist->name;
					$this->result['artists'][$i]['url'] = (string) $artist->url;
					$this->result['artists'][$i]['image']['small'] = (string) $artist->image[2];
					$this->result['artists'][$i]['image']['medium'] = (string) $artist->image[1];
					$this->result['artists'][$i]['image']['large'] = (string) $artist->image[0];
					$i++;
				}
				
				$countUser = 0;
				$countMyspace = 0;
				switch ( $methodVars[1]['type'] ) {
					case 'user':
						$this->result['inputOne']['type'] = 'user';
						$this->result['inputOne']['name'] = (string) $call->comparison->input->user[$countUser]->name;
						$this->result['inputOne']['url'] = (string) $call->comparison->input->user[$countUser]->url;
						$this->result['inputOne']['image']['small'] = (string) $call->comparison->input->user[$countUser]->image[2];
						$this->result['inputOne']['image']['medium'] = (string) $call->comparison->input->user[$countUser]->image[1];
						$this->result['inputOne']['image']['large'] = (string) $call->comparison->input->user[$countUser]->image[0];
						$countUser++;
					break;
					case 'artists':
						$this->result['inputOne']['type'] = 'artists';
					break;
					case 'myspace':
						$this->result['inputOne']['type'] = 'myspace';
						$this->result['inputOne']['url'] = (string) $call->comparison->input->myspace[$countMyspace]->url;
						$this->result['inputOne']['image'] = (string) $call->comparison->input->myspace[$countMyspace]->image;
						$countMyspace++;
					break;
				}
				
				switch ( $methodVars[2]['type'] ) {
					case 'user':
						$this->result['inputTwo']['type'] = 'user';
						$this->result['inputTwo']['name'] = (string) $call->comparison->input->user[$countUser]->name;
						$this->result['inputTwo']['url'] = (string) $call->comparison->input->user[$countUser]->url;
						$this->result['inputTwo']['image']['small'] = (string) $call->comparison->input->user[$countUser]->image[2];
						$this->result['inputTwo']['image']['medium'] = (string) $call->comparison->input->user[$countUser]->image[1];
						$this->result['inputTwo']['image']['large'] = (string) $call->comparison->input->user[$countUser]->image[0];
						$countUser++;
					break;
					case 'artists':
						$this->result['inputTwo']['type'] = 'artists';
					break;
					case 'myspace':
						$this->result['inputTwo']['type'] = 'myspace';
						$this->result['inputTwo']['url'] = (string) $call->comparison->input->myspace[$countMyspace]->url;
						$this->result['inputTwo']['image'] = (string) $call->comparison->input->myspace[$countMyspace]->image;
						$countMyspace++;
					break;
				}
				
				return $this->result;
			}
			else {
				return FALSE;
			}
		}
		else {
			// Give a 91 error if incorrect variables are used
			$this->handleError(91, 'You must include 2 sets of type and value varialbes in the call for this method');
			return FALSE;
		}
	}
}

?>