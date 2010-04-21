<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", "on");
require_once $_SERVER['DOCUMENT_ROOT']."/wp-load.php";
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