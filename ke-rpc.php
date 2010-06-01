<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", "on");

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
include_once dirname(__FILE__)."/ke-location.php";

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

require_once $wpBaseLocation."/wp-load.php";
extract($_GET);
extract($_POST);

switch($action)
{
	case "ke_get_events":
		$events = ke_get_events("status=1");
		$cal_events = array();
		
		// NEED TO GET THE URL FOR THE EVENTS DESTINATION PAGE
		$query = "SELECT * FROM ".$table_prefix."ke_settings 
		WHERE ".$table_prefix."ke_settings.setting_id = 1";
		$ke_settings = db_get_rows($query);
					
					
		foreach($events as $x)
		{
			$x['event_title'] = stripslashes($x['event_title']);
			$x['event_title'] = str_replace("'", "&apos;", $x['event_title']);
			$event_title = "<strong>".$x['event_title']."</strong>";
			$event_title .= (!empty($x['event_location']))?("<br/>".$x['event_location']):(""); 
			$event_title .= (!empty($x['event_start_date']))?("<br/>".date("jS F Y", strtotime($x['event_start_date']))):(""); 
			$event_title .= (!empty($x['event_start_time']))?("<br/>".date("g.ia", strtotime("2009-12-01 ".$x['event_start_time']))):(""); 
			$event_title = htmlentities($event_title);
			
			$selected_event = false;
			// NEED TO GIVE THE SELECTED EVENT A DIFFERENT CLASS NAME
			if(isset($event_id) && !empty($event_id) && $event_id == $x['event_id'])
			{
				$selected_event = true;
			}
			
			if(get_option("permalink_structure") != "")
			//if(strstr($_SERVER['REQUEST_URI'], "?"))
			{
				$event_url = get_permalink(get_option("ke_setting_wp_page"))."?evt=".$x['event_slug'];
			}
			else
			{
				$event_url = get_permalink(get_option("ke_setting_wp_page"))."&evt=".$x['event_slug'];
			}

			if($selected_event)
			{
				$cal_events[] = array("id"=>$x['event_id'],
									"title"=>$event_title,
									"start"=>date("Y-m-d", strtotime($x['event_start_date'])),
									"end"=>date("Y-m-d", strtotime($x['event_end_date'])),
									"url"=>$event_url,
									"allDay"=>"true",
									"className"=>"fc-event-selected");
			}
			else
			{
				$cal_events[] = array("id"=>$x['event_id'],
									"title"=>$event_title,
									"start"=>date("Y-m-d", strtotime($x['event_start_date'])),
									"end"=>date("Y-m-d", strtotime($x['event_end_date'])),
									"url"=>$event_url,
									"allDay"=>"true");
			}
		}
		print json_encode($cal_events);
		break;


	default:
		break;
}
?>