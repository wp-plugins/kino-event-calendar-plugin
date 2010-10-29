<?php
extract($_POST);error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", "on");

// NEED THE WORDPRESS ROOT HERE

$root = dirname(dirname(dirname(getcwd())));
require_once $root."/wp-load.php";

extract($_GET);
extract($_POST);
switch($action)
{
	case "get_events":
	
		// EVENTS WILL NOW BE PULLED FROM CUSTOM POSTS .. THANK FUCK!
		$events = get_posts("post_type=events&numberposts=-1");
		
		$cal_events = array();
		
		foreach($events as $x)
		{
			// GET EVENT CATEGORY - TO GET COLOR
			$event_categories = wp_get_post_terms( $x->ID , "event_category"); 
			if(count($event_categories) > 0)
			{
				$event_category = $event_categories[0]->slug;
			}
			
			// GET CUSTOM FOR POST
			$custom = get_post_custom($x->ID);
			
			$selected_event = false;
			// NEED TO GIVE THE SELECTED EVENT A DIFFERENT CLASS NAME
			if(isset($ID) && !empty($ID) && $ID == $x->ID)
			{
				$selected_event = true;
			}
			
			if(get_option(PLUGIN_SHORT_NAME."_date_format"))
			{
				$date_format = get_option(PLUGIN_SHORT_NAME."_date_format");
			}
			else
			{
				$date_format = get_option("date_format");
			}
			
			
			// RECURRING?
			if($custom['_event_recurring'][0])
			{
				// GET FREQUENCY AND LOOP UNTIL END DATE
				$freq = $custom['_event_frequency'][0];
				
				// NEED TO GET NUM OF DAYS BETWEEN START AND END
				$days = GetDays($custom['_event_start_date'][0], $custom['_event_end_date'][0]);
				
				//print $days; exit;
				$counter = 0;
				for($i=0; $i<$days; $i++)
				{
					
					if($freq == "daily" && $counter == 1)
					{
						$counter = 0;	
					}
					
					if($freq == "weekly" && $counter == 7)
					{
						$counter = 0;	
					}
					
					if($freq == "fortnightly" && $counter == 14)
					{
						$counter = 0;	
					}
					
					if($freq == "monthly" && date("d", strtotime($custom['_event_start_date'][0])) == date("d", strtotime($custom['_event_start_date'][0]) + ($i * 60 * 60 * 24)) )
					{
						$counter = 0;
					}
										
					if($counter == 0)
					{
						// TITLE OF EVENT
						$event_title = stripslashes($x->post_title);
						$event_title = str_replace("'", "&apos;", $event_title);
						$event_title = "<strong>".$event_title."</strong>";
						
						// LOCATION
						$event_title .= (!empty($custom['_event_location'][0]))?("<br/>".$custom['_event_location'][0]):(""); 
						
						
						// START TO END DATE
						/*
						$event_title .= (!empty($custom['_event_start_date'][0]))?("<br/>".date($date_format, strtotime($custom['_event_start_date'][0]) + ($i * 60 * 60 * 24))):("");
						$event_title .= (!empty($custom['_event_end_date'][0]))?(" - ".date($date_format, strtotime($custom['_event_end_date'][0]) + ($i * 60 * 60 * 24))):("");
						*/	
						$event_title .= (!empty($custom['_event_start_date'][0]))?("<br/>".date($date_format, strtotime($custom['_event_start_date'][0]) )):("");
						if($custom['_event_end_date'][0] != $custom['_event_start_date'][0])
						{
							$event_title .= (!empty($custom['_event_end_date'][0]))?(" - ".date($date_format, strtotime($custom['_event_end_date'][0]) )):("");
						}
						//$event_title .= (!empty($custom['_event_end_date'][0]))?(" - ".date($date_format, strtotime($custom['_event_end_date'][0]) )):("");
						
						// IF NOT ALL DAY (TIMES)
						if(!$custom['_event_all_day'][0])
						{
							// IF 12 HR
							if(get_option(PLUGIN_SHORT_NAME."_time_format") == 12)
							{								
								// START TO END TIME
								$event_title .= (!empty($custom['_event_start_time'][0]))?("<br/>".time_to_12hr($custom['_event_start_time'][0])):("");
								$event_title .= (!empty($custom['_event_end_time'][0]))?(" - ".time_to_12hr($custom['_event_end_time'][0])):("");							
							}
							// IF 24 HR - REMOVE SECONDS
							else
							{
								// START TO END TIME
								$event_title .= (!empty($custom['_event_start_time'][0]))?("<br/>".$custom['_event_start_time'][0]):("");
								$event_title .= (!empty($custom['_event_end_time'][0]))?(" - ".$custom['_event_end_time'][0]):("");
							}							
						}
						// ALL DAY
						else
						{
							$event_title .= "<br/>All day";
						}
						
						$event_title = htmlentities($event_title);
						
						$cal_events[] = array("id"=>$x->ID,
												"title"=>$event_title,
												"start"=>date("Y-m-d", strtotime($custom['_event_start_date'][0]) + ($i * 60 * 60 * 24)),
												"end"=>date("Y-m-d", strtotime($custom['_event_start_date'][0]) + ($i * 60 * 60 * 24)),
												"url"=>get_permalink($x->ID),
												"allDay"=>"true",
												"className"=>(($selected_event)?("fc-event-selected ".$event_category):("fc-event ".$event_category)));
					}
					$counter++;
				}
										
				
			}
			// NOT RECURRING
			else
			{
				
				// TITLE OF EVENT
				$event_title = stripslashes($x->post_title);
				$event_title = str_replace("'", "&apos;", $event_title);
				$event_title = "<strong>".$event_title."</strong>";
				
				// LOCATION
				$event_title .= (!empty($custom['_event_location'][0]))?("<br/>".$custom['_event_location'][0]):(""); 
				
				
				// START TO END DATE
				$event_title .= (!empty($custom['_event_start_date'][0]))?("<br/>".date($date_format, strtotime($custom['_event_start_date'][0]))):("");
				if($custom['_event_end_date'][0] != $custom['_event_start_date'][0])
				{
					$event_title .= (!empty($custom['_event_end_date'][0]))?(" - ".date($date_format, strtotime($custom['_event_end_date'][0]) )):("");
				}
				//$event_title .= (!empty($custom['_event_end_date'][0]))?(" - ".date($date_format, strtotime($custom['_event_end_date'][0]))):("");
										
				// IF NOT ALL DAY (TIMES)
				if(!$custom['_event_all_day'][0])
				{
					// IF 12 HR
					if(get_option(PLUGIN_SHORT_NAME."_time_format") == 12)
					{								
						// START TO END TIME
						$event_title .= (!empty($custom['_event_start_time'][0]))?("<br/>".time_to_12hr($custom['_event_start_time'][0])):("");
						$event_title .= (!empty($custom['_event_end_time'][0]))?(" - ".time_to_12hr($custom['_event_end_time'][0])):("");							
					}
					// IF 24 HR - REMOVE SECONDS
					else
					{
						// START TO END TIME
						$event_title .= (!empty($custom['_event_start_time'][0]))?("<br/>".$custom['_event_start_time'][0]):("");
						$event_title .= (!empty($custom['_event_end_time'][0]))?(" - ".$custom['_event_end_time'][0]):("");
					}							
				}
				// ALL DAY
				else
				{
					$event_title .= "<br/>All day";
				}
						
				$event_title = htmlentities($event_title);
						
				if($selected_event)
				{
					
					$cal_events[] = array("id"=>$x->ID,
										"title"=>$event_title,
										"start"=>date("Y-m-d", strtotime($custom['_event_start_date'][0])),
										"end"=>date("Y-m-d", strtotime($custom['_event_end_date'][0])),
										"url"=>get_permalink($x->ID),
										"allDay"=>"true",
										"className"=>"fc-event-selected ".$event_category);
				}
				else
				{
					$cal_events[] = array("id"=>$x->ID,
										"title"=>$event_title,
										"start"=>date("Y-m-d", strtotime($custom['_event_start_date'][0])),
										"end"=>date("Y-m-d", strtotime($custom['_event_end_date'][0])),
										"url"=>get_permalink($x->ID),
										"allDay"=>"true",
										"className"=>"fc-event ".$event_category);
				}
			}
		}
		print json_encode($cal_events);
		break;


	default:
		break;
}
?>