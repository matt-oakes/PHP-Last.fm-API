<?php

class lastfmApiTasteometer extends lastfmApiBase {
	public $results;
	
	private $apiKey;
	private $typeOne;
	private $valueOne;
	private $typeTwo;
	private $valueTwo;
	
	function __construct($apiKey, $typeOne, $valueOne, $typeTwo, $valueTwo) {
		$this->apiKey = $apiKey;
		$this->typeOne = $typeOne;
		$this->valueOne = $valueOne;
		$this->typeTwo = $typeTwo;
		$this->valueTwo = $valueTwo;
	}
	
	public function compare() {
		$vars = array(
			'method' => 'tasteometer.compare',
			'api_key' => $this->apiKey,
			'type1' => $this->typeOne,
			'value1' => $this->valueOne,
			'type2' => $this->typeTwo,
			'value2' => $this->valueTwo
		);
		
		if ( $call = $this->apiGetCall($vars) ) {
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
			switch ( $this->typeOne ) {
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
			
			switch ( $this->typeTwo ) {
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
}

?>