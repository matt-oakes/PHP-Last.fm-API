var lastfmRadio = {};

// Global app settings
lastfmRadio.debug = true;

// User settings
lastfmRadio.sessionKey = jQuery.cookie('sessionkey');
lastfmRadio.username = jQuery.cookie('username');

lastfmRadio.init = function() {
	// Any global config that needs to be done
	
	// Start selecting a station
	this.station.select();
};
lastfmRadio.station = {
	select: function() {
		// self = lastfmRadio.stationSelect
		var self = this;
		
		// Display the station select block
		jQuery('#station-select').css('display', 'block');
		
		// When the station selector changes display or hide the extra details textbox
		jQuery('#station').bind('change', function(){
			if ( jQuery(this).find('option:selected').hasClass('details') ) {
				jQuery('#radio-details').show();
			}
			else {
				jQuery('#radio-details').hide();
			}
		});
		
		// When the user presses the tune in button run the tune function
		jQuery('#listen-button').bind('click', function() {
			self.tuneIn();
		});
	},
	tuneIn: function() {
		var stationUrl = this.generateStationUrl();
		if ( stationUrl !== false ) {
			// Station url generated succesfully
			jQuery.getJSON('radio/tune.php', {station: stationUrl}, function(data) {
				lastfmRadio.station.type = data.type;
				lastfmRadio.station.name = data.name;
				lastfmRadio.station.url = data.url;
				lastfmRadio.station.supportsdiscovery = data.supportsdiscovery;
				
				lastfmRadio.radio.init();
			});
		}
		else {
			// An error occured, most likely they forgot to fill in the details box
			alert('Please fill in the textbox with the required information');
		}
	},
	generateStationUrl: function() {
		var stationType = jQuery('#station option:selected').val();
		var stationTypeParts = stationType.split('-');
		var details = jQuery('#radio-details').val();
		
		var stationUrl = 'lastfm://';
		
		if ( stationTypeParts[0] === 'your' ) {
			stationUrl += 'user/' + lastfmRadio.username;
		}
		else {
			if ( details !== '' ) {
				stationUrl += stationTypeParts[0] + '/' + details;
			}
			else {
				return false;
			}
		}
		
		if ( stationTypeParts[1] !== undefined ) {
			stationUrl += '/' + stationTypeParts[1];
		}
		
		return stationUrl;
	}
};
lastfmRadio.radio = {
	init: function() {
		jQuery('#station-select').hide();
		jQuery('#now-playing').show();
		jQuery('#station-name').text(lastfmRadio.station.name);
		
		lastfmRadio.radio.getPlaylist();
	},
	currentTrack: {},
	playlist: [],
	getPlaylist: function() {
		jQuery.getJSON('radio/getPlaylist.php', function(data) {
			for ( var x = 0; x < data.tracklist.length; x++ ) {
				lastfmRadio.radio.playlist.push(new lastfmRadio.track(data.tracklist[x]));
			}
			
			lastfmRadio.radio.nextTrack();
		});
	},
	nextTrack: function() {
		if ( this.playlist.length > 0 ) {
			soundManager.destroySound('sound');
			this.currentTrack = this.playlist.shift();
			this.playTrack();
		}
		else {
			this.getPlaylist();
		}
	},
	playTrack: function() {
		jQuery('#artist-name').text(this.currentTrack.artist.name);
		jQuery('#album-name').text(this.currentTrack.album.name);
		jQuery('#album-image').attr('src', this.currentTrack.album.image);
		jQuery('#track-name').text(this.currentTrack.name);
		
		soundManager.createSound({
			id: 'sound',
			url: this.currentTrack.location,
			volume: 100,
			autoLoad: true,
			autoPlay: true,
			onfinish: function() {
				lastfmRadio.radio.nextTrack();
			}
		});
	}
};

lastfmRadio.track = function(json) {
	// Track info
	this.location = json.location;
	this.id = json.identifier;
	this.name = json.title;
	this.url = json.trackpage;
	this.auth = json.trackauth;
	this.duration = json.duration;
	this.buyUrl = json.buyTrackURL;
	this.freeUrl = json.freeTrackURL;
	
	// Artist info
	this.artist = new lastfmRadio.artist(json);
	
	// Album info
	this.album = new lastfmRadio.album(json);
};
lastfmRadio.track.prototype = {
	location: '',
	id: '',
	name: '',
	url: '',
	auth: '',
	duration: '',
	buyUrl: '',
	freeUrl: ''
};

lastfmRadio.artist = function(json) {
	this.id = json.artistid;
	this.name = json.creator;
	this.url = json.artistpage;
};
lastfmRadio.artist.prototype = {
	id: '',
	name: '',
	url: ''
};

lastfmRadio.album = function(json) {
	this.id = json.albumid;
	this.name = json.album;
	this.url = json.albumpage;
	this.image = json.image;
	this.buyUrl = json.buyAlbumURL;
};
lastfmRadio.album.prototype = {
	id: '',
	name: '',
	url: '',
	image: '',
	buyUrl: ''
};