<?php
/*
Plugin Name: MFGetweather
Plugin URI: http://www.markus-frenzel.de/wordpress-plugins/getweather
Version: 1.6.0
Author: Markus Frenzel
Author URI: http://www.markus-frenzel.de
Description: Shows weather conditions by querying the weather.com website
*/
$getweather_domain = 'mfgetweather';
$getweather_is_setup = 0;

load_plugin_textdomain($getweather_domain, 'wp-content/plugins/mfgetweather/lang');

/*
Ex Plugin Name: Simple Cache
Ex Version: 1.0
Ex Plugin URI:
Ex Description: Simple set of caching functions. Useful for plugin authors to build off of.
Ex Author: Jeff Minard
Ex Author URI: http://creatimation.net/
*/

$divoptions = (array) get_option('MFGetweatherAdminOptions');

if ($divoptions['cache']=="true") {

if (!function_exists(cache_recall) || !function_exists(cache_restore)) {

define(CACHE_DIR, ABSPATH . 'wp-content/simple-cache');

if ( !file_exists(CACHE_DIR) ) {
	//ack, no cache dir created - try to make one
	if ( is_writable( dirname(CACHE_DIR) ) ) {
		// parent folder is writeable, try to make a dir.
		$dir = @mkdir( CACHE_DIR, 0666);
		if($dir == false) {
			// tried to make cache folder and failed.
			// die(__("Your cache directory (<code>" . CACHE_DIR . "</code>) needs to be writable for this plugin to work. Double-check it. <a href='" . get_settings('siteurl') . "/wp-admin/plugins.php?action=deactivate&amp;plugin=mfgetweather.php'>Deactivate the MFGetweather plugin</a>.", $getweather_domain));
			?>
			<div class="updated"><p><strong><?php __("Your cache directory (<code>\" . CACHE_DIR . \"</code>) needs to be writable for this plugin to work. Double-check it. <a href='\" . get_settings('siteurl') . \"/wp-admin/plugins.php?action=deactivate&amp;plugin=mfgetweather.php'>Deactivate the MFGetweather plugin</a>.", $getweather_domain); ?></strong></p></div>
                        <?php
		}
	} else {
		// parent is unwrite able, and we have no chache folder.
		// die(__("The MFGetweather plugin cache directory (<code>" . CACHE_DIR . "</code>) needs to exist and be writable for this plugin to work. Double-check it. <a href='" . get_settings('siteurl') . "/wp-admin/plugins.php?action=deactivate&amp;plugin=mfgetweather.php'>Deactivate the MFGetweather plugin</a>.", $getweather_domain));
		?>
		<div class="updated"><p><strong><?php __("The MFGetweather plugin cache directory (<code>\" . CACHE_DIR . \"</code>) needs to exist and be writable for this plugin to work. Double-check it. <a href='\" . get_settings('siteurl') . \"/wp-admin/plugins.php?action=deactivate&amp;plugin=mfgetweather.php'>Deactivate the MFGetweather plugin</a>.", $getweather_domain); ?></strong></p></div>
		<?php
	}

        update_option("use_cache", "false");

}


function cache_recall($func_call, $stale_age=15) {

	$filename = CACHE_DIR . md5($func_call) . '.txt';

	if( !file_exists($filename) ) {
		// this function call has not been cached before
		return false;
	}

	if ( filemtime($filename) < strtotime("$stale_age minutes ago") ) {
		// file contents are too old!
		return false;
	}

	$cached_content = file_get_contents($filename);

	return $cached_content;

}

function cache_store($func_call, $content) {

	$filename = CACHE_DIR . md5($func_call) . '.txt';

	if (!$handle = @fopen($filename, 'w')) {
		// Can't open file?
		return false;
	}

	if (fwrite($handle, $content) === FALSE) {
		// Write fail?
		return false;
	}

	fclose($handle);

	return true;

}

}

}
// end of the simple_cache tool

if (!class_exists("MFGetweather")) {
  class MFGetweather {
    var $adminOptionsName = "MFGetweatherAdminOptions";

    function MFGetweather() { // constructor
    } // end function MFGetweather

    function getAdminOption() {
      $mfgetweatherAdminOptions = array('measurement_system' => 'm',
                                       'iconDirURL' => ABSPATH.'wp-content/plugins/mfgetweather/iconset',
                                       'use_cache' => 'true',
                                       'areacode' => '94115',
                                       'cityname' => 'SF, CA',
                                       'features' => 'icon,temp');

      $mfgetweatherOptions = get_option($this->adminOptionsName);

      if (!empty($mfgetweatherOptions)) {
        foreach ($mfgetweatherOptions as $key => $option)
        $mfgetweatherAdminOptions[$key] = $option;
      }

      update_option($this->adminOptionsName, $mfgetweatherAdminOptions);
      return $mfgetweatherAdminOptions;
    } // end function getAdminOption

    function init() {
      $this->getAdminOption();
    } // end function init

    function printAdminPage() {
      global $getweather_domain;
      load_plugin_textdomain($getweather_domain, 'wp-content/plugins/mfgetweather/lang');

      $mfgetweatherOptions = $this->getAdminOption();

      if (isset($_POST['update_mfgetweatherSettings'])) {
        if (isset($_POST['mf_measurement_system'])) {
          $mfgetweatherOptions['measurement_system'] = $_POST['mf_measurement_system'];
        }
        if (isset($_POST['mf_iconDirURL'])) {
          $mfgetweatherOptions['iconDirURL'] = $_POST['mf_iconDirURL'];
        }
        if (isset($_POST['mf_use_cache'])) {
          $mfgetweatherOptions['use_cache'] = $_POST['mf_use_cache'];
        }
        if (isset($_POST['mf_areacode'])) {
          $mfgetweatherOptions['areacode'] = $_POST['mf_areacode'];
        }
        if (isset($_POST['mf_cityname'])) {
          $mfgetweatherOptions['cityname'] = $_POST['mf_cityname'];
        }
        if (isset($_POST['mf_features'])) {
          $mfgetweatherOptions['features'] = $_POST['mf_features'];
        }

        update_option($this->adminOptionsName, $mfgetweatherOptions);

        ?>
<div class="updated"><p><strong><?php _e("Settings Updated.", "MFGetweather"); ?></strong></p></div>
        <?php
        } ?>

<div class=wrap>
<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
<h2>MFGetweather</h2>

<h3><?php echo __("Icon Dir URL?", $getweather_domain) ?></h3>
<p><?php echo __("NO trailing slash please", $getweather_domain) ?></p>
<p><label for "mf_iconDirURL_text"><input type="text" name="mf_iconDirURL" size="80" value="<?php echo $mfgetweatherOptions['iconDirURL']; ?>"></label></p>

<h3><?php echo __("Areacode?", $getweather_domain) ?></h3>
<p><?php echo __("Follow the instructions in the \"AREACODE.TXT\" file to retrieve your personal areacode", $getweather_domain) ?></p>
<p><label for "mf_areacode_text"><input type="text" name="mf_areacode" size="10" value="<?php echo $mfgetweatherOptions['areacode']; ?>"></label></p>

<h3><?php echo __("Text to display?", $getweather_domain) ?></h3>
<p><?php echo __("This text will only be displayed if you use the \"description\" feature.", $getweather_domain) ?></p>
<p><label for "mf_cityname_text"><input type="text" name="mf_cityname" size="40" value="<?php echo $mfgetweatherOptions['cityname']; ?>"></label></p>

<h3><?php echo __("Measurement System?", $getweather_domain) ?></h3>
<p><label for "mf_measurement_system_s"><input type="radio" id="mf_measurement_system_s" name="mf_measurement_system" value="s" <?php if ($mfgetweatherOptions['measurement_system'] == "s") { _e('checked="checked"', "MFGetweather"); }?> /> <?php echo __("US Standard", $getweather_domain) ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for "mf_measurement_system_m"><input type="radio" id="mf_measurement_system_m" name="mf_measurement_system" value="m" <?php if ($mfgetweatherOptions['measurement_system'] == "m") { _e('checked="checked"', "MFGetweather"); }?>/> <?php echo __("Metric", $getweather_domain) ?></label></p>

<h3><?php echo __("Use the cache function?", $getweather_domain) ?></h3>
<p><?php echo __("Selecting \"No\" will disable the cache function (not recommended).", $getweather_domain) ?></p>
<p><label for "mf_use_cache_yes"><input type="radio" id="mf_use_cache_yes" name="mf_use_cache" value="true" <?php if ($mfgetweatherOptions['use_cache'] == "true") { echo 'checked="checked"'; }?> /> <?php echo __("Yes", $getweather_domain) ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<label for "mf_use_cache_no"><input type="radio" id="mf_use_cache_no" name="mf_use_cache" value="false" <?php if ($mfgetweatherOptions['use_cache'] == "false") { echo 'checked="checked"'; } ?>/> <?php echo __("No", $getweather_domain) ?></label></p>

<h3><?php echo __("Which options should be displayed?", $getweather_domain) ?></h3>
<p><?php echo __("Look at the file \"options.txt\" in the \"doc\" folder!", $getweather_domain) ?></p>
<p><label for "mf_features_text"><input type="text" name="mf_features" size="80" value="<?php echo $mfgetweatherOptions['features']; ?>"></label></p>

<div class="submit">
<input type="submit" name="update_mfgetweatherSettings" value="<?php _e('Update Settings', $getweather_domain) ?>" /></div>
</form>
</div>

        <?php
      } // end function printAdminPage

/*
this is the part where the original script is implemented
*/

function get_weather_setup()
{
   global $getweather_domain, $getweather_is_setup;

   if($getweather_is_setup) {
      return;
   }

   load_plugin_textdomain($getweather_domain, 'wp-content/plugins/mfgetweather/lang');

}

function get_weather_chgFormatDate($time)
{
  $timestamp = strtotime($time);
  $time = date('H:i', $timestamp);
  return $time;
}

function get_weather($citycode='USCA0987', $desc='', $order="desc,icon,temp,forecast,curtime,sunrise,sunset,vis,wind,hum,dew,high,low,dayforecast") {
	// use this function just to echo out the LI's
	echo( $this->get_weather_raw($citycode, $desc, $order) );
}

function get_weather_raw($citycode='USCA0987', $desc='', $order="desc,icon,temp,forecast,curtime,sunrise,sunset,vis,wind,hum,dew,high,low,dayforecast") {
	// this function does the heavy lifting and simple "returns" the li string.
	// use this if you want to do something to the string before echoing.
	// Get the weather from xoap weather.com and parse it to an array

	global $iconDirURL, $measurement_system, $transparent_hack, $use_cache, $getweather_domain;

        load_plugin_textdomain($getweather_domain, 'wp-content/plugins/mfgetweather/lang');

        $mfgetweatherOptions = $this->getAdminOption();
        $citycode = $mfgetweatherOptions['areacode'];
        $measurement_system = $mfgetweatherOptions['measurement_system'];

        $this->get_weather_setup();

        if( !function_exists(cache_recall) || !function_exists(cache_store) ) {
		// caching function not available
		$use_cache = false;
	}

	// check the cache 1.2
	if($use_cache) {
		$function_string = "get_weather_raw($citycode,$desc,$order)";
		if($returns = cache_recall($function_string)) {
			return $returns;
		}
	}


	$file = "http://xoap.weather.com/weather/local/$citycode?cc=*&dayf=1&unit=$measurement_system";
	$xml_parser = xml_parser_create();

	if (!($fp = fopen($file, "r"))) {
	   return("<li>".__("Weather Not Available (Read Error)", $getweather_domain)."</li>");
	}

	$data = '';
	while (!feof($fp)) {
	  $data .= fread($fp, 8192);
	}

	fclose($fp);
	xml_parse_into_struct($xml_parser, $data, $vals, $index);
	xml_parser_free($xml_parser);

	$params = array();
	$level = array();
	foreach ($vals as $xml_elem) {
	  if ($xml_elem['type'] == 'open') {
	   if (array_key_exists('attributes',$xml_elem)) {
		 list($level[$xml_elem['level']],$extra) = array_values($xml_elem['attributes']);
	   } else {
		 $level[$xml_elem['level']] = $xml_elem['tag'] ;
	   }
	  }
	  if ($xml_elem['type'] == 'complete') {
	   $start_level = 1;
	   $php_stmt = '$params';
	   while($start_level < $xml_elem['level']) {
		 $php_stmt .= '[$level['.$start_level.']]';
		 $start_level++;
	   }
	   $php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';
	   eval($php_stmt);
	  }
	}

	$workoptions = $this->getAdminOption();
	$iconDirURL = $workoptions['iconDirURL'];

	// mmm, parsing goodness
	//
	// Now, return it in the order requested

	$order_array = explode(',', $order);

	foreach($order_array as $val) {

		switch($val) {
			case 'desc':
				if($desc != '') { $d = $desc; } else { $d = $params["2.0"][CC][OBST]; }
				$output .= $this->lir((__("Weather for ", $getweather_domain)) . "$d", $val);
				break;

			case 'icon':
				$icon = $iconDirURL .'/' . $params["2.0"][CC][ICON] . '.png';

				$s = "<div id=\"weather-image\" style=\"background: url('$icon') !important; background: transparent;	filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='$icon', sizingMethod='scale'); height: 128px; width: 128px;\"> </div>";

				$output .= $this->lir($s, $val);
				break;

			case 'temp':
				$output .= $this->lir(__("Temperature: ", $getweather_domain) . $params["2.0"][CC][TMP] . $params["2.0"][HEAD][UT], $val);
				break;

			case 'forecast':
				$output .= $this->lir(__("Forecast: ", $getweather_domain) . $params["2.0"][CC][T], $val);
				break;

			case 'curtime':
				$output .= $this->lir(__("Current Time: ", $getweather_domain) . get_weather_chgFormatDate($params["2.0"][$citycode]["TM"]), $val);
				break;

			case 'sunrise':
				$output .= $this->lir(__("Sunrise: ", $getweather_domain) . $this->get_weather_chgFormatDate($params["2.0"][$citycode][SUNR]), $val);
				break;

			case 'sunset':
				$output .= $this->lir(__("Sunset: ", $getweather_domain) . $this->get_weather_chgFormatDate($params["2.0"][$citycode][SUNS]), $val);
				break;

			case 'sunrise-sunset':
				$output .= $this->lir(__("Sunrise/Sunset: ", $getweather_domain) . $this->get_weather_chgFormatDate($params["2.0"][$citycode][SUNR]).'/'.$this->get_weather_chgFormatDate($params["2.0"][$citycode][SUNS]), $val);
				break;

			case 'vis':
				$output .= $this->lir(__("Visibility: ", $getweather_domain) . $params["2.0"][CC][VIS] . $params["2.0"][HEAD][UD], $val);
				break;

			case 'wind':
				$output .= $this->lir(__("Wind: ", $getweather_domain) . $params["2.0"][CC][WIND][S] . $params["2.0"][HEAD][US], $val);
				break;

			case 'hum':
				$output .= $this->lir(__("Humidity: ", $getweather_domain) . $params["2.0"][CC][HMID]."%", $val);
				break;

			case 'dew':
				$output .= $this->lir(__("Dewpoint: ", $getweather_domain) . $params["2.0"][CC][DEWP], $val);
				break;

			case 'high':
				$output .= $this->lir(__("High: ", $getweather_domain) . $params["2.0"][DAYF][0][HI], $val);
				break;

			case 'low':
				$output .= $this->lir(__("Low: ", $getweather_domain) . $params["2.0"][DAYF][0][LOW], $val);
				break;

			case 'high-low':
				$output .= $this->lir(__("High/Low: ", $getweather_domain) . $params["2.0"][DAYF][0][HI].'/'.$params["2.0"][DAYF][0][LOW], $val);
				break;

			case 'dayforecast':
			        $icon2 = $iconDirURL .'/small/' . $params["2.0"][DAYF][0][d][ICON] . '_small.png';
			        $icon3 = $iconDirURL .'/small/' . $params["2.0"][DAYF][0][n][ICON] . '_small.png';

                                $s2 = "<img src=\"$icon2\" height=64px width=64px alt=\"".__("Forecast Day", $getweather_domain)."\">";
                                $s3 = "<img src=\"$icon3\" height=64px width=64px alt=\"".__("Forecast Night", $getweather_domain)."\">";

				$s4 = "<div id=\"weather-image2\" style=\"background: url('$icon2') !important; background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='$icon2', sizingMethod='scale'); height: 64px; width: 64px\"> </div> Testbereich";
                                $s5 = "<div id=\"weather-image2\" style=\"background: url('$icon3') !important; background: transparent; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='$icon3', sizingMethod='scale'); height: 64px; width: 64px;\"> </div>";

				$output .= $this->lir($s2 . " " . $s3, $val);
				break;

		}
	}


	// store the results so that they can be recalled via cache 1.2
	if($use_cache) {
		cache_store($function_string, $output);
	}


	return $output;

}

//general function for LI'ing things.
function lir($s, $c=''){
	if($c!='') { $c=" class=\"$c\""; }
	return "<li$c>$s</li>\n";
}
/*
here it comes to an end ;-)
*/

  }  // end class MFGetweather

} // end if-clause to check class MFGetweather

if (class_exists("MFGetweather")) {
  $mfgetweather = new MFGetweather();
}

// initialize the Admin Panel
if (!function_exists("MFGetweather_ap")) {
  function MFGetweather_ap() {
    global $mfgetweather;
    if (!isset($mfgetweather)) {
      return;
    }
    if (function_exists('add_options_page')) {
      add_options_page('MFGetweather', 'MFGetweather', 9, basename(__FILE__), array(&$mfgetweather, 'printAdminPage'));
    }
  } // end function MFGetweather_ap
}

/*
Widget Name: Getweather widget
Description: Adds a sidebar widget to display the Getweather-Plugin
Author:      Markus Frenzel
Version:     1.0
Author URI:  http://www.markus-frenzel.de
*/
if (!function_exists("widget_mfgetweatherwidget_init")) {
function widget_mfgetweatherwidget_init () {
        // Check for the required API functions
        if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
                return;

        function widget_mfgetweatherwidget_control() {
                $options = $newoptions = get_option('widget_mfgetweatherwidget');
                if ( $_POST['mfgetweather_submit'] ) {
                        $newoptions['title'] = strip_tags(stripslashes($_POST['mfgetweather_title']));
                }
                if ( $options != $newoptions ) {
                        $options = $newoptions;
                        update_option('widget_mfgetweatherwidget', $options);
                }
        ?>
                <div style="text-align:right">
                <label for="mfgetweather_title" style="line-height:35px;display:block;"><?php _e('Title:', $getweather_domain); ?> <input type="text" id="mfgetweather_title" name="mfgetweather_title" value="<?php echo wp_specialchars($options['title'], true); ?>" /></label>
                <input type="hidden" name="mfgetweather_submit" id="mfgetweather_submit" value="1" />
                </div>
        <?php
        }

        function widget_mfgetweatherwidget ($args) {
                global $mfgetweather;
                extract($args);
                $defaults = array('title' => 'MFGetWeather');
                $options = (array) get_option('widget_mfgetweatherwidget');
                $divoptions = (array) get_option('MFGetweatherAdminOptions');

                foreach ( $defaults as $key => $value )
                        if ( !isset($options[$key]) )
                                $options[$key] = $defaults[$key];

        ?>
                <?php echo $before_widget; ?>
                        <?php echo $before_title . "{$options['title']}" . $after_title; ?>
                        <?php $mfgetweather->get_weather($divoptions['areacode'], $divoptions['cityname'], $divoptions['features']); ?>
                <?php echo $after_widget; ?>
        <?php
        }

        register_sidebar_widget(array('MFGetweather', 'widgets'), 'widget_mfgetweatherwidget');
        register_widget_control(array('MFGetweather', 'widgets'), 'widget_mfgetweatherwidget_control', 300, 400);

}

} // end of if-clause for checking the widget init function

/*
end of widget code
*/

// Actions and Filters
if (isset($mfgetweather)) {
  // Actions
  add_action('activate_mfgetweather/mf_getweather.php', array(&$mfgetweather, 'init'));
  add_action('admin_menu', 'MFGetweather_ap');
  add_action('plugins_loaded', 'widget_mfgetweatherwidget_init');

  // Filters
    // no filters used!

}

?>