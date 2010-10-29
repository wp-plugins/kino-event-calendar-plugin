<?php
$today = date("Y-m-d");

$events = get_posts("post_type=events&numberposts=-1");

if(count($events))
{
	if(get_option(PLUGIN_SHORT_NAME."_date_format"))
	{
		$date_format = get_option(PLUGIN_SHORT_NAME."_date_format");
	}
	else
	{
		$date_format = get_option("date_format");
	}
			
	foreach($events as $x)
	{
			
		// GET CUSTOM
		$custom 				= get_post_custom($x->ID);
		
		// FILTER OUT PAST EVENTS
		
		if(mktime(0,0,0,date('m'), date('d'), date('Y')) <= strtotime($custom['_event_start_date'][0]))
		{
		
		
	
			?>
			<div class="search-result vevent">
			<h3><a href="<?php print get_permalink($x->ID); ?>"><span class="summary"><?php print $x->post_title; ?></span></a></h3>
			
			<?php	
			$event_title = "";
			// LOCATION
			$event_title .= (!empty($custom['_event_location'][0]))?("<br/><span class='location'>".$custom['_event_location'][0]."</span>"):(""); 
			
			
			$event_title .= (!empty($custom['_event_start_date'][0]))?("<br/><span class='dtstart'>".date($date_format, strtotime($custom['_event_start_date'][0]) )."</span>"):("");
			
			if($custom['_event_recurring'][0])
			{
				switch($custom['_event_frequency'][0])
				{
					case "daily":
						$recurrance = "day";
						break;	
					case "weekly":
						$recurrance = "week";
						break;	
					case "fortnightly":
						$recurrance = "2 weeks";
						break;	
					case "monthly":
						$recurrance = "month";
						break;	
				}
				
				$event_title .= "<br/>Every ".$recurrance." until ".date($date_format, strtotime($custom['_event_end_date'][0]))."";
			}
			elseif($custom['_event_end_date'][0] != $custom['_event_start_date'][0])
			{
				$event_title .= (!empty($custom['_event_end_date'][0]))?(" - ".date($date_format, strtotime($custom['_event_end_date'][0]) )):("");
			}
			
			// IF NOT ALL DAY (TIMES)
			if(!$custom['_event_all_day'][0])
			{
				// IF 12 HR
				if(get_option(PLUGIN_SHORT_NAME."_time_format") == 12)
				{								
					// START TO END TIME
					$event_title .= (!empty($custom['_event_start_time'][0]) && $custom['_event_start_time'][0] != '00:00:00')?("<br/>".time_to_12hr($custom['_event_start_time'][0])):("");
					$event_title .= (!empty($custom['_event_end_time'][0]) && $custom['_event_end_time'][0] != '00:00:00')?(" - ".time_to_12hr($custom['_event_end_time'][0])):("");							
				}
				// IF 24 HR - REMOVE SECONDS
				else
				{
					// START TO END TIME
					$event_title .= (!empty($custom['_event_start_time'][0]) && $custom['_event_start_time'][0] != '00:00:00')?("<br/>".$custom['_event_start_time'][0]):("");
					$event_title .= (!empty($custom['_event_end_time'][0]) && $custom['_event_end_time'][0] != '00:00:00')?(" - ".$custom['_event_end_time'][0]):("");
				}							
			}
			// ALL DAY
			else
			{
				$event_title .= "<br/>All day";
			}
			
			
				
	
			?>
			<p class="event-meta"><strong><?php print $event_title; ?></strong></p>
			
			<?php /*<p><?php print substr(strip_tags(stripslashes($x->post_content)), 0, 320); ?></p>*/ ?>
			<p><?php print $x->post_excerpt; ?></p>
			
			<?php
			
			?>
			<p><a class="more-link" href="<?php print get_permalink($x->ID); ?>">read more</a></p>
			</div>
			<?php	
			print "<p style='border-bottom: 1px solid #e8e8e8;'/>";
		}
	}
	
}
else
{
	$no_forthcoming_events = true;	
}// END IF
?>