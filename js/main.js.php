<?php
header("Content-type: text/javascript"); 
/*************************************************************************************************/
/*
 * To any coders/maintainers:
 * 
 * Put this code at the top of each PHP file that needs to know paths to other resources.
 * Modify the path to ke-location.php to be relative to the plugin tree structure. For example:
 *		"/../ke-location.php"
 *		"/../../ke-location.php"
 *		etc.
 * 
 * See <plugin_directory>/ke-location.php comments for docs of the expected return values for 
 * the variables.
 *
 */
include_once dirname(__FILE__)."/../ke-location.php";

// It's a good idea that if ke-location.php is extended, make explicit global variables 
// for these. Don't rely on variables automatically being global as they won't be when you
// least expect it, leading to INCREDIBLY difficult-to-find errors.
global $pluginLocation, $pluginRelativeLocation, $pluginDirname, $wpBaseLocation, $theBaseURL, $myURL;

$pluginLocation = ke_getPluginLocation();
$pluginRelativeLocation = ke_getPluginRelativeLocation();
$wpBaseLocation = ke_getInstallBaseLocation();
$pluginDirname = ke_getPluginDirname();
$theBaseURL = ke_getWPURL();
$myURL = ke_getMyURLDir();
/*************************************************************************************************/
include $wpBaseLocation."/wp-load.php";
?>
// JavaScript Document
var j = jQuery.noConflict();

j(document).ready(function()
{
	var d = new Date();
	var y = d.getFullYear();
	var m = d.getMonth();
	
	var mycal = j('#calendar').fullCalendar({
		editable: false,
		header: {
			left: 'prev,next today',
			center: 'title',
			right: ''
		},
		events: function(start, end, callback) {
		
			// do some asynchronous ajax
			// bmb
			j.getJSON("<?php global $pluginRelativeLocation; echo $pluginRelativeLocation; ?>/ke-rpc.php",
			{
				action: "ke_get_events",
				start: start.getTime(),
				end: end.getTime()<?php
				$_GET['evt'] = urlencode(stripslashes($_GET['evt']));
				if(isset($_GET['evt']) && !empty($_GET['evt']))
				{
					$query = "SELECT * FROM ".$table_prefix."ke_events WHERE event_slug = '".$_GET['evt']."'";
					print "//".$query."\n";
					$ke_event = db_get_rows($query);
					$x = $ke_event[0];
					?>,
				event_id: <?php print $x['event_id']; ?>
					<?php 	
				}
				?>
			},
			function(result)
			{
				// format the result into an array of CalEvents
				calevents = result;
				
				// then, pass the CalEvent array to the callback
				callback(calevents);
			
			});
		
		}

	});
	
	j(".event-link").live("click", function()
	{
		var event_id = j(this).attr('class').split(' ').slice(-1);
		
		// ON THE FRONT-END THIS WOULD BE THE TIME TO REDIRECT THE USER TO THE PAGE WITH THE EVENT
		
	});
	
	
	j("body").append("<div id='event-tooltip'></div>");
	
	// EQUALISE THE CALENDAR CELL WIDTHS
	j(".fc td,.fc th").width("14%");
	
});
