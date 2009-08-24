<?php include 'radio/setup.php'; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Basic Online Last.fm Player</title>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
	<link rel="stylesheet" type="text/css" href="style.css" />
	
	<script type="text/javascript" src="scripts/jquery/jquery.js"></script>
	<script type="text/javascript" src="scripts/jquery/jquery.cookie.js"></script>
	
	<script type="text/javascript" src="scripts/soundman/soundmanager2.js"></script>
	<script type="text/javascript">
		soundManager.debugMode = false;
	</script>
	
	<script type="text/javascript" src="scripts/radio/radio.js"></script>
	<script type="text/javascript" src="scripts/radio/init.js"></script>
</head>
<body>
	
	<?php if ( !isset($lastfmapi_auth) ) : ?>
		<p>To login to this service you need to login at lastfm and allow this service to <a href="http://www.last.fm/api/auth/?api_key=<?php echo $config['api_key']; ?>">use your account</a>.</p>
	<?php else: ?>
		<?php if ( $lastfmapi_auth->subscriber == 1 ) : ?>
			<?php include 'interface.php'; ?>
		<?php else: ?>
			Woops! You don't seem to be a <a href="http://www.last.fm/subscribe/">subscriber</a>. Last.fm only allows us to stream readio to paying subscribers. It's only £3 a month. Why not <a href="http://www.last.fm/subscribe/">subscribe</a>?
		<?php endif; ?>
	<?php endif; ?>
	
</body>
</html>