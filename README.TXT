=== MFGetWeather ===
Contributors: markusfrenzel
Tags: weather,getweather,forecast,temperature
Requires at least: 2.0.2
Tested up to: 2.3
Stable tag: trunk

Shows current weather conditions by querying the weather.com website.

== Description ==

If you want to show the weather of a specific city around the world MFGetWeather is the right plugin for your Wordpressblog! Widget included!

== Installation ==

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

== Frequently Asked Questions ==

= Is this plugin only available in English? =

No! Included are the language files for German and English! French and Spanish will follow!