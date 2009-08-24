<div class="panel" id="station-select">
	<h2>Select A Station</h2>
	<select name="station" id="station">
		<optgroup label="Your Radio Stations">
			<option value="your-library" class="personal">Your Personal Station</option>
			<option value="your-loved" class="personal">Your Loved Tracks</option>
			<option value="your-neighbours" class="personal">Your Neighbourhood Station</option>
			<option value="your-recommended" class="personal">Your Recommendations</option>
		</optgroup>
		<optgroup label="Other User's Radio Stations">
			<option value="user-library" class="details user">Personal Station Of...</option>
			<option value="user-loved" class="details user">Loved Tracks Of...</option>
			<option value="user-neighbours" class="details user">Neighbourhood Station Of...</option>
		</optgroup>
		<optgroup label="Artist Radio Stations">
			<option value="artist-similarartists" class="details artist">Similar To...</option>
			<option value="artist-fans" class="details artist">Fans Of...</option>
		</optgroup>
		<optgroup label="Tag Radio Stations">
			<option value="globaltags" class="details tag">Music Tagged..</option>
		</optgroup>
	</select>
	<input type="text" name="radio-details" id="radio-details" />
	<input type="submit" id="listen-button" value="Tune In!" />
</div>
<div class="panel" id="now-playing">
	<strong>Station:</strong> <span id="station-name"></span><br />
	<img src="" alt="" id="album-image" /><br />
	<strong>Artist:</strong> <span id="artist-name"></span><br />
	<strong>Album:</strong> <span id="album-name"></span><br />
	<strong>Track:</strong> <span id="track-name"></span><br />
</div>

<noscript>WOAH! Not so fast there. You need javascript enabled to use this application.</noscript>