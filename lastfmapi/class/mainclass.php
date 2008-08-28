<?php

class lastfmApi extends lastfmApiBase {
	
	function getPackage($auth, $package) {
		if ( is_object($auth) ) {
			if ( !empty($auth->apiKey) && !empty($auth->secret) && !empty($auth->username) && !empty($auth->sessionKey) && !empty($auth->subscriber) ) {
				$fullAuth = 1;
			}
			elseif ( !empty($auth->apiKey) ) {
				$fullAuth = 0;
			}
			else {
				$this->handleError(91, 'Invalid auth class was passed to lastfmApi. You need to have at least an apiKey set');
				return FALSE;
			}
		}
		else {
			$this->handleError(91, 'You need to pass a lastfmApiAuth class as the first variable to this class');
			return FALSE;
		}
		
		if ( $package == 'album' || $package == 'artist' || $package == 'event' || $package == 'geo' || $package == 'group' || $package == 'library' || $package == 'playlist' || $package == 'tag' || $package == 'tasteometer' || $package == 'track' || $package == 'user' ) {
			$className = 'lastfmApi'.ucfirst($package);
			return new $className($auth, $fullAuth);
		}
		else {
			$this->handleError(91, 'The package name you past was invalid');
			return FALSE;
		}
	}
}

?>