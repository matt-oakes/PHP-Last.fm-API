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
	$tasteometerClass = $apiClass->getPackage($auth, 'tasteometer');
	
	// Creat an array with all the lastfm staff members usernames
	$staffMembers = array(
		'RNR',
		'RJ',
		'mainstream',
		'spencerhyman',
		'rregan',
		'menardnyc',
		'lynn_fischer',
		'Orlenay',
		'mischa',
		'Russ',
		'muesli',
		'flaneur',
		'skr',
		'mokele',
		'pete_bug',
		'sharevari',
		'hannahdonovan',
		'nova77LF',
		'vincro',
		'sideb0ard',
		'mustaqila',
		'joanofarctan',
		'fionapinkstars',
		'julians',
		'lumberjack',
		'Jonty',
		'martind',
		'theneonfever',
		'foreverautumn',
		'Greg_J',
		'grazziee',
		'pellitero',
		'Korean_Cowboy',
		'mxcl',
		'Whiskeyface',
		'juliasven',
		'galeote',
		'Edouard',
		'jwheare',
		'gaoxing',
		'gaoxing',
		'justanotherider',
		'erikfrey',
		'E1i45',
		'robinlisle',
		'fakesensations',
		'HawkeVIPER',
		'nancyvw',
		'underpangs',
		'laimee',
		'lozzd',
		'jonocole',
		'por',
		'jensnikolaus',
		'afonsoduarte',
		'dunk',
		'massdosage',
		'acreature',
		'LeJeff',
		'lizrice',
		'Omnicia',
		'rynos',
		'miadellocca',
		'dmhuk',
		'Daniel1986',
		'Schrollum',
		'michalboo',
		'musicmobs',
		'xe54',
		'liquid986',
		'irvinebrown',
		'bambidambo',
		'nananaina',
		'wakehurst2',
		'dundez',
		'Tars21',
		'NisaMishap',
		'dukedave',
		'klbostee',
		'crshamburg'
	);
	// WOW! That's a lot of people
	// Last updated: 03/09/08
	
	// Setup the results array ready for the results
	$results = array();
	
	// Loop through each staff member and put the results into the results array
	foreach ( $staffMembers as $staffMember ) {
		// Setup the variables
		$methodVars = array(
			1 => array(
				'type' => 'user',
				'value' => $_GET['username']
			),
			2 => array(
				'type' => 'user',
				'value' => $staffMember
			)
		);
		
		if ( $result = $tasteometerClass->compare($methodVars) ) {
			$key = $result['score'] * 1000000;
			$results[$key] = $result;
		}
	}
	
	// Sort the array with higest match first
	krsort($results);
	
	// Loop through the results
	$first = 1;
	foreach ( $results as $match ) {
		if ( $first == 1 ) {
			?>
				<h2>Top Match</h2>
				<div class="match top">
					<?php if ( !empty($match['inputTwo']['image']['large']) ) :?>
						<img src="<?php echo $match['inputTwo']['image']['large']; ?>" alt="'s avatar" class="avatar" />
					<?php endif; ?>
					<h3><?php echo ( $match['score'] * 100 ); ?>% match with <a href="<?php echo $match['inputTwo']['url']; ?>"><?php echo $match['inputTwo']['name']; ?></a></h3>
					<p>You have <?php echo $match['matches']; ?> artist(s) in common, including:</p>
					<ul>
						<?php foreach ( $match['artists'] as $artist ) : ?>
							<li>
								<?php if ( !empty($artist['image']['small']) ) : ?>
									<img src="<?php echo $artist['image']['small']; ?>" alt="Photo of <?php echo $artist['name']; ?>" class="icon" />
								<?php endif; ?>
								<a href="<?php echo $artist['url']; ?>"><?php echo $artist['name']; ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
					<div style="clear: both">&nbsp;</div>
				</div>
				
				<h2>Other Matches</h2>
				<ul id="otherMatches">
			<?php
			$first = 0;
		}	
		else {
			?>
			<li>
				<div class="match">
					<?php if ( !empty($match['inputTwo']['image']['large']) ) :?>
						<img src="<?php echo $match['inputTwo']['image']['large']; ?>" alt="'s avatar" class="avatar" />
					<?php endif; ?>
					<h3><?php echo ( $match['score'] * 100 ); ?>% match with <a href="<?php echo $match['inputTwo']['url']; ?>"><?php echo $match['inputTwo']['name']; ?></a></h3>
					<p>You have <?php echo $match['matches']; ?> artist(s) in common, including:</p>
					<ul>
						<?php foreach ( $match['artists'] as $artist ) : ?>
							<li>
								<?php if ( !empty($artist['image']['small']) ) : ?>
									<img src="<?php echo $artist['image']['small']; ?>" alt="Photo of <?php echo $artist['name']; ?>" class="icon" />
								<?php endif; ?>
								<a href="<?php echo $artist['url']; ?>"><?php echo $artist['name']; ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
					<div style="clear: both">&nbsp;</div>
				</div>
			</li>
			<?php
		}
	}
	// End it all!
	echo '</ul>';
}
else {
	// If not: ERROR!!!!!!!!!
	echo '<div class="error">You must submit a username</div>';
}

//Include the footer
include 'template/footer.php';

?>