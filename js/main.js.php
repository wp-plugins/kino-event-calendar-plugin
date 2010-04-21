<?php
include $_SERVER['DOCUMENT_ROOT']."/wp-load.php";
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
			j.getJSON("/wp-content/plugins/kino-events/ke-rpc.php",
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
