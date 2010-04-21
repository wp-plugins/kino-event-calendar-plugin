<?php
global $table_prefix;

// IF NO EVENT SELECTED SHOW MAIN 
if(!isset($_GET['evt']) || empty($_GET['evt']))
{
	$today = date("Y-m-d");
	
	// GET FORTHCOMING EVENTS
	$query = "SELECT * FROM ".$table_prefix."ke_events WHERE event_start_date >= '".$today."' AND event_status = 1 ORDER BY event_start_date ";
	$ke_events = db_get_rows($query);
	
	// NEED TO GET THE URL FOR THE EVENTS DESTINATION PAGE
	$query = "SELECT * FROM ".$table_prefix."ke_settings 
	WHERE ".$table_prefix."ke_settings.setting_id = 1";
	$ke_settings = db_get_rows($query);
	
	if(count($ke_events))
	{
		?>

		<?php
		foreach($ke_events as $x)
		{
			if(get_option("permalink_structure") != "")
			//if(strstr($_SERVER['REQUEST_URI'], "?"))
			{
				$event_url = get_permalink($ke_settings[0]['setting_wp_page'])."?evt=".$x['event_slug'];
			}
			else
			{
				$event_url = get_permalink($ke_settings[0]['setting_wp_page'])."&evt=".$x['event_slug'];
			}
			?>
            <div class="search-result vevent">
			<h3><a href="<?php print $event_url; ?>"><span class="summary"><?php print stripslashes($x['event_title']); ?></span></a></h3>
			<p>
			<?php
			// IF START DATE
			if(!empty($x['event_start_date']))
			{
				print "<span style=\"display:none;\" class=\"dtstart\">".$x['event_start_date']."</span>";
				?><strong><?php print date("jS F Y", strtotime($x['event_start_date'])); 
				// IF START TIME
				if($x['event_start_time'] && $x['event_start_time'] != "00:00:00" && $x['event_start_time'] != "NULL")
				{
					print ", ".date("G:i", strtotime($x['event_start_date']." ".$x['event_start_time']));
					// IF END TIME
					if($x['event_end_time'] && $x['event_end_time'] != "00:00:00" && $x['event_end_time'] != "NULL")
					{
						print " - ".date("G:i", strtotime($x['event_start_date']." ".$x['event_end_time']));
					}
				}
				?>
				</strong>
				<?php
			}
			?>
			<?php
			if(!empty($x['event_location']))
			{
				?>
				<br/><strong>Location:</strong> <span class="location"><?php print $x['event_location']; ?></span>
				<?php
			}
			?>
			</p>
			
			<p><?php print substr(strip_tags(stripslashes($x['event_detail'])), 0, 320); ?></p>
			
			<?php
			
			?>
			<p><a class="more-link" href="<?php print $event_url; ?>">read more</a></p>
            </div>
			<?php	
		}
		print "<p style='border-bottom: 1px solid #e8e8e8;'/>";
	}
	else
	{
		$no_forthcoming_events = true;	
	}// END IF
	
	
	// GET PAST EVENTS
	$query = "SELECT * FROM ".$table_prefix."ke_events WHERE event_start_date < '".$today."' AND event_status = 1 ORDER BY event_start_date DESC";
	$ke_events = db_get_rows($query);
	
	if(count($ke_events))
	{
		?>
		<h2 class="title">Past Events</h2>
		<?php
		foreach($ke_events as $x)
		{
			if(get_option("permalink_structure") != "")
			//if(strstr($_SERVER['REQUEST_URI'], "?"))
			{
				$event_url = get_permalink($ke_settings[0]['setting_wp_page'])."?evt=".$x['event_slug'];
			}
			else
			{
				$event_url = get_permalink($ke_settings[0]['setting_wp_page'])."&evt=".$x['event_slug'];
			}
			?>
            <div class="search-result vevent">
			<h3><a href="<?php print $event_url; ?>"><span class="summary"><?php print stripslashes($x['event_title']); ?></span></a></h3>
			<p>
			<?php
			// IF START DATE
			if(!empty($x['event_start_date']))
			{
				print "<span style=\"display:none;\" class=\"dtstart\">".$x['event_start_date']."</span>";
				?><strong><?php print date("jS F Y", strtotime($x['event_start_date'])); 
				// IF START TIME
				if($x['event_start_time'] && $x['event_start_time'] != "00:00:00" && $x['event_start_time'] != "NULL")
				{
					print ", ".date("G:i", strtotime($x['event_start_date']." ".$x['event_start_time']));
					// IF END TIME
					if($x['event_end_time'] && $x['event_end_time'] != "00:00:00" && $x['event_end_time'] != "NULL")
					{
						print " - ".date("G:i", strtotime($x['event_start_date']." ".$x['event_end_time']));
					}
				}
				?>
				</strong>
				<?php
			}
			?>
			<?php
			if(!empty($x['event_location']))
			{
				?>
				<br/><strong>Location:</strong> <span class="location"><?php print $x['event_location']; ?></span>
				<?php
			}
			?>
			</p>
			
			<p><?php print substr(strip_tags(stripslashes($x['event_detail'])), 0, 320); ?></p>
			
			<?php
			// NEED TO GET THE URL FOR THE EVENTS DESTINATION PAGE
			$query = "SELECT * FROM ".$table_prefix."ke_settings 
			WHERE ".$table_prefix."ke_settings.setting_id = 1";
			$ke_settings = db_get_rows($query);
			?>
			<p><a class="more-link" href="<?php print $event_url; ?>">read more</a></p>
            </div>
			<?php	
		}
	}
	else
	{
		$no_past_events = true;	
		
	}// END IF
	
	if($no_forthcoming_events && $no_past_events)
	{
		?>
		<p>There are no events to list.</p>
		<?php	
	}
	
	
}
// IF EVENT SELECTED SHOW EVENT
else
{
	$_GET['evt'] = urlencode(stripslashes($_GET['evt']));
	
	$query = "SELECT * FROM ".$table_prefix."ke_events WHERE event_slug = '".mysql_real_escape_string($_GET['evt'])."'";
	//print $query;
	$ke_event = db_get_rows($query);
	$x = $ke_event[0];
	?>
	<div class="vevent">
	<h3><span class="summary"><?php print stripslashes($x['event_title']); ?></span></h3>
	<p>
	<?php
	// IF START DATE
	if(!empty($x['event_start_date']))
	{
		print "<span style=\"display:none;\" class=\"dtstart\">".$x['event_start_date']."</span>";
		?><strong><?php print date("jS F Y", strtotime($x['event_start_date'])); 
		// IF START TIME
		if($x['event_start_time'] && $x['event_start_time'] != "00:00:00" && $x['event_start_time'] != "NULL")
		{
			print ", ".date("G:i", strtotime($x['event_start_date']." ".$x['event_start_time']));
			// IF END TIME
			if($x['event_end_time'] && $x['event_end_time'] != "00:00:00" && $x['event_end_time'] != "NULL")
			{
				print " - ".date("G:i", strtotime($x['event_start_date']." ".$x['event_end_time']));
			}
		}
		?>
		</strong>
		<?php
	}
	?>
	<?php
	if(!empty($x['event_location']))
	{
		?>
		<br/><strong>Location:</strong> <span class="location"><?php print $x['event_location']; ?></span>
		<?php
	}
	?>
	</p>
	
	<p><?php print nl2br(stripslashes($x['event_detail'])); ?></p>
	
	<?php
	// NEED TO GET THE URL FOR THE EVENTS DESTINATION PAGE
	$query = "SELECT * FROM ".$table_prefix."ke_settings 
	WHERE ".$table_prefix."ke_settings.setting_id = 1";
	
	$ke_settings = db_get_rows($query);
	
	?>
	<p><a class="more-link" href="<?php print get_permalink($ke_settings[0]['setting_wp_page']);?>">All events</a></p>
	</div>
	<?php
	
}
?>