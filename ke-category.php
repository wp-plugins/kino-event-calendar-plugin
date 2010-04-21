<?php
global $table_prefix;


$today = date("Y-m-d");

// GET FORTHCOMING EVENTS
$ke_events = get_posts('order=asc&orderby=date&category='.get_option("ke_setting_wp_category"));

$event_counter = 0;
if(count($ke_events))
{
	
	foreach($ke_events as $x)
	{
		
		list($event_location) = get_post_meta($x->ID, "ke_location");
		list($event_start_date) = get_post_meta($x->ID, "ke_start_date");
		list($event_start_time) = get_post_meta($x->ID, "ke_start_time");
		list($event_end_date) = get_post_meta($x->ID, "ke_end_date");
		list($event_end_time) = get_post_meta($x->ID, "ke_end_time");
		
		if(strtotime($event_start_date) >= mktime())
		{
			$event_counter++;
			if($event_counter == 1)
			{
				?>
				<h2 class="title">Forthcoming Events</h2>
				<?php
			}
			?>
			<div class="search-result vevent">
				<h3><a href="<?php print get_permalink($x->ID); ?>"><span class="summary"><?php print stripslashes($x->post_title); ?></span></a></h3>
				<p>
				<?php
				// IF START DATE
				if(!empty($event_start_date))
				{
					print "<span style=\"display:none;\" class=\"dtstart\">".$event_start_date."</span>";
					?><strong><?php print date("jS F Y", strtotime($event_start_date)); 
					// IF START TIME
					if($event_start_time && $event_start_time != "00:00:00" && $event_start_time != "NULL")
					{
						print ", ".date("G:i", strtotime($event_start_date." ".$event_start_time));
						// IF END TIME
						if($event_end_time && $event_end_time != "00:00:00" && $event_end_time != "NULL")
						{
							print " - ".date("G:i", strtotime($event_start_date." ".$event_end_time));
						}
					}
					?>
					</strong>
					<?php
				}
				?>
				<?php
				if(!empty($event_location))
				{
					?>
					<br/><strong>Location:</strong> <span class="location"><?php print $event_location; ?></span>
					<?php
				}
				?>
				</p>
				
				<p><?php print substr(strip_tags(stripslashes($x->post_content)), 0, 320); ?></p>
				
				<?php
				
				?>
				<p><a class="more-link" href="<?php print get_permalink($x->ID); ?>">read more</a></p>
			</div>
			<?php	
		}
	}
	
	if($event_counter)
	{
		print "<p style='border-bottom: 1px solid #e8e8e8;'/>";
	}
}
else
{
	$no_forthcoming_events = true;	
}// END IF


// GET PAST EVENTS
//$query = "SELECT * FROM ".$table_prefix."ke_events WHERE event_start_date < '".$today."' AND event_status = 1 ORDER BY event_start_date DESC";
//$ke_events = db_get_rows($query);

$ke_events = get_posts('order=desc&orderby=date&category='.get_option("ke_setting_wp_category"));

$event_counter = 0;
if(count($ke_events))
{
	foreach($ke_events as $x)
	{
		
		list($event_location) = get_post_meta($x->ID, "ke_location");
		list($event_start_date) = get_post_meta($x->ID, "ke_start_date");
		list($event_start_time) = get_post_meta($x->ID, "ke_start_time");
		list($event_end_date) = get_post_meta($x->ID, "ke_end_date");
		list($event_end_time) = get_post_meta($x->ID, "ke_end_time");
		
		if(strtotime($event_start_date) < mktime())
		{
			$event_counter++;
			if($event_counter == 1)
			{
				?>
				<h2 class="title">Past Events</h2>
				<?php
			}
			?>
			<div class="search-result vevent">
				<h3><a href="<?php print get_permalink($x->ID); ?>"><span class="summary"><?php print stripslashes($x->post_title); ?></span></a></h3>
				<p>
				<?php
				// IF START DATE
				if(!empty($event_start_date))
				{
					print "<span style=\"display:none;\" class=\"dtstart\">".$event_start_date."</span>";
					?><strong><?php print date("jS F Y", strtotime($event_start_date)); 
					// IF START TIME
					if($event_start_time && $event_start_time != "00:00:00" && $event_start_time != "NULL")
					{
						print ", ".date("G:i", strtotime($event_start_date." ".$event_start_time));
						// IF END TIME
						if($event_end_time && $event_end_time != "00:00:00" && $event_end_time != "NULL")
						{
							print " - ".date("G:i", strtotime($event_start_date." ".$event_end_time));
						}
					}
					?>
					</strong>
					<?php
				}
				?>
				<?php
				if(!empty($event_location))
				{
					?>
					<br/><strong>Location:</strong> <span class="location"><?php print $event_location; ?></span>
					<?php
				}
				?>
				</p>
				
				<p><?php print substr(strip_tags(stripslashes($x->post_content)), 0, 320); ?></p>
				
				<?php
				
				?>
				<p><a class="more-link" href="<?php print get_permalink($x->ID); ?>">read more</a></p>
			</div>
			<?php	
		}
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
	
	

?>