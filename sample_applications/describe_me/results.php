<?php

// Include the header
include 'template/header.php';

// Check username was sent
if ( !empty($_GET['username']) ) {
	// If so: carry no
	
	// Include the API
	require '../../lastfmapi/lastfmapi.php';
	
	// Set the API key
	$authVars['apiKey'] = 'fa3af76b9396d0091c9c41ebe3c63716';
	// Pass the apiKey to the auth class to get a none fullAuth auth class
	$auth = new lastfmApiAuth('setsession', $authVars);
	
	// Call for the tasteometer package class with auth class
	$apiClass = new lastfmApi();
	$userClass = $apiClass->getPackage($auth, 'user');
	
	// Create a list of tag's that we don't one
	// Mainly ones that don't describe peoples musical taste
	$badTags = array(
		'good',
		'seen live',
		'favourite',
		'favorites',
		'favorite artists',
		'favourite bands',
		'favourites',
		'want to see live',
		'uk',
		'whales',
		'my music',
		'amazing',
		'awesome',
		'english',
		'fun',
		'multiple artists under same name',
		'a few of the bands ive seen',
		'albums i own',
		'music',
		'rock gods'
	);
	
	// Setup the variables get get the users top artists
	$methodVars = array(
		'user' => $_GET['username']
	);
	// Get the users top artist (with error check)
	if ( $artists = $userClass->getTopArtists($methodVars) ) {
		// Setup the results array
		$results = array();
		
		// Lopp through each of the users top artists
		foreach ( $artists as $artist ) {
			// Create an artists class to use
			$artistClass = $apiClass->getPackage($auth, 'artist');
			
			// Setup the variables for the artist call
			$methodVars = array(
				'artist' => $artist['name']
			);
			// Get the top tags that are givent to that artist
			$tags = $artistClass->getTopTags($methodVars);
			
			// Check that there is some tags and it is an array
			if ( count($tags) > 0 && is_array($tags) ) {
				// Loop through the tags
				foreach ( $tags as $tag ) {
					// Check it's not a bad tag
					// If it is then it won't be used and the second most popular will be
					// If it isn't just the most used tag is used
					if ( !in_array(strtolower($tag['name']), $badTags) && !empty($tag['name']) ) {
						// Get the previous score for the tag if it exists
						if ( !empty($results[$tag['name']]) ) {
							$prev = $results[$tag['name']]['value'];
						}
						else {
							$prev = 0;
						}
						// Calculate the new score
						$new = $prev + $artist['playcount'];
						
						// Write this score back to the results array
						$results[$tag['name']] = array(
							'name' => $tag['name'],
							'url' => $tag['url'],
							'value' => $new
						);
						
						// Break out of the loop to only get the top tag for this artists
						break;
					}
				}
			}
		}
		
		// Create a compare function which puts the results in descending order based on their score
		function compare($x, $y) {
			if ( $x['value'] == $y['value'] ) {
				return 0;
			}
			else if ( $x['value'] < $y['value'] ) {
				return 1;
			}
			else {
				return -1;
			}
		}
		// Do the sort
		usort($results, 'compare');
		
		// Loops through the results to get a total score to use in working out a percentage
		$total = 0;
		foreach ( $results as $result ) {
			$total = $total + $result['value'];
		}
		
		// Output the results
		?>
		
		<p><em><?php echo $_GET['username']; ?></em>'s musical taste is best described by the word:</p>
		<p><strong><a href="<?php echo $results[0]['url']; ?>"><?php echo $results[0]['name']; ?></a></strong> <span class="score">(<?php echo round(( $results[0]['value'] / $total ) * 100, 2); ?>%)</span></p>
		<p>However it can also be described with the words:</p>
		<ol>
			<?php $i = 0; ?>
			<?php foreach ( $results as $result ) : ?>
				<?php if ( $i > 0 ) : ?>
					<li><a href="<?php echo $result['url']; ?>"><?php echo $result['name']; ?></a> <span class="score">(<?php echo round(( $result['value'] / $total ) * 100, 2); ?>%)</span></li>
				<?php endif; ?>
				<?php $i++; ?>
			<?php endforeach; ?>
		</ol>
		
		<?php
	}
	else {
		// If error: show which error and go no further.
		echo '<b>Error '.$userClass->error['code'].' - </b><i>'.$userClass->error['desc'].'</i>';
	}
}
else {
	// If not: ERROR!!!!!!!!!
	echo '<div class="error">You must submit a username</div>';
}

//Include the footer
include 'template/footer.php';

?>