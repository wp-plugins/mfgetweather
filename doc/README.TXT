=======
README:
=======


-------------------
Plugin-Information:
-------------------
Plugin Name: MFGetweather
Plugin URI:  http://www.markus-frenzel.de/wordpress-plugins/getweather
Author:      Markus Frenzel
Author URI:  http://www.markus-frenzel.de/
Version:     1.6.0
Description: Shows current weather conditions by querying the weather.com website.
Date:        11 June 2007
Extra Info:  Icon set "Crystal" care of Daniel Brauer's (DockZone)
             Extra icons can be downloaded from
             http://www.samurize.com/modules/ipboard/index.php?s=f171e96010a2120b73cd108b18c02203&showtopic=3857



---------------------
Original-Information:
---------------------
Originalversion:    1.2.1
Originaldate:       22 January 2005
Originalauthor:     Jeff Minard
Originalauthor URI: http://thecodepro.com



-------------
Installation:
-------------
1. Upload this plugin to your wordpress plugins directory
2. Active the MFGetweather Plugin via the admin menu
3. Drag 'n' Drop the widget to you sidebar OR Place this code in your template:

    <ul><?php $mfgetweather->get_weather('94115') ?></ul>

    Or get much more fancy and include:

    <ul><?php $mfgetweather->get_weather('94115', 'SF, CA', 'icon,temp,high,low'); ?></ul>

You need only change the "citycode" (the first variable in the
function) to your own city. You may use a US zip code in place
of a standard city code. International users will have to use
a standard city code.



Extra Functionality:

	<?php $mfgetweather->get_weather($citycode, $title, $options) ?>

$citycode
	A required attribute to define what city you want weather from
$title
	If using the attribute "desc", this string will replace the
	default city name.
$options
	A list of what attributes you want the plugin to return, and
	in what order to return them in. Look at the file "OPTIONS.TXT" for details!



-------------------------------
Update from a previous version:
-------------------------------
1. Deactivate the old version
2. Delete the old files (except individual icons)
3. Follow the "Installation:" instructions



----------
Important:
----------
CAUTION!
Do not change the directory names or any file names!



==========================================================
MFGetweather Plugin for Wordpress / Author: Markus Frenzel
==========================================================