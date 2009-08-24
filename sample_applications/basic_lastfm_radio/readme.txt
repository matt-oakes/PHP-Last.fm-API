==================================================================
--                                                              --
--                  Basic Lastfm Radio Player                   --
--                                                              --
==================================================================

Built by Matt Oakes (http://www.matto1990.com) on 23rd August 2009
in about 4 hours. It is built using my Lastfm API Library with PHP,
jQuery and loads of vanila JavaScript.

Released under the same licence as the PHP Lastfm API library (the
MIT licence)

==================================================================
--                                                              --
--                        Description                           --
--                                                              --
==================================================================

This is a very very simple lastfm radio player build using the
PHP Lastfm API library. Most of the heavy work is actually done
using Javascript, however it talks to three php files which give
it the data it needs from Lastfm to make the radio work.

It firstly checks your cookies to see if you've logged in before.
If you haven't it offers you a link to the Lastfm website which
allows you to choose to give the application permission to use your
account. You are then returned automatically to the application
and are ready to select a radio station.

You select a radio station using the drop down menu, and provide
any additional information using the textbox which appears. When
you press tune in the radio/tuneIn.php file is called which talks
to lastfm using the radio.tuneIn method. You are then ready to get
a new playlist and start listening to music.

The radio/getPlaylist.php files is run and calls the radio.getPlaylist
method which then returns a playlist of tracks (normally five tracks
long) which can then be played.

Each track is played in turn using Sound Manager 2. When each track
finishes it will automatically move onto the next track; if there
are no tracks left in the playlist it simply requests another one in
the same way it got the first and carries on as before. This
effectively creates a loop which allows it to just continue to play
music.

As this is a very simple example application I've not included any
error checking at all so it could fail at any point without explaining
why. If you were building a fully functional application you would
need to read the Lastfm API documentation to see what error checking
you need to include. This is important for a production application
however I left it out to show as simply as possible how to use the
API to create a radio station.

==================================================================
--                                                              --
--                        Installation                          --
--                                                              --
==================================================================

Installation is quite simple and only take a few steps:

1) Log into the Lastfm account you want to include the API account in
2) Go to: http://www.last.fm/api/account and fill in the form (non-
   commercial Use is fine)
3) Copy the API Key and Secret into the $config array into the
   radio/config.php file
4) On the http://www.last.fm/api/account page fill in the callback URL
   with http://<address to get where you've put the application>/login.php
5) Go to the application and give it a try =D

==================================================================
--                                                              --
--                        Compatability                         --
--                                                              --
==================================================================

- Tested fully on Opera 9.6
- Had a quick test on Firefox 3.5 and Google Chrome
- Tested a bit on IE8. It plays but sometimes just stops

If you can fix any browser specific bugs please send me patches :)