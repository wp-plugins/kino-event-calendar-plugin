// JavaScript Document
jQuery(document).ready(function($)
{
	var d = new Date();
	var y = d.getFullYear();
	var m = d.getMonth();
	
	var mycal = $('#ke-calendar').fullCalendar({
		editable: false,
		header: {
			left: 'prev,next today',
			center: 'title',
			right: ''
		},
		events: function(start, end, callback) {
		
			// do some asynchronous ajax
			$.getJSON(plugin_path+"/rpc.php",
			{
				action: "get_events",
				start: start.getTime(),
				end: end.getTime(),
				event_id: event_id || null
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
	
	$(".event-link").live("click", function()
	{
		var event_id = $(this).attr('class').split(' ').slice(-1);
		
		// ON THE FRONT-END THIS WOULD BE THE TIME TO REDIRECT THE USER TO THE PAGE WITH THE EVENT
		
	});
	
	
	$("body").append("<div id='event-tooltip'></div>");
	
	// EQUALISE THE CALENDAR CELL WIDTHS
	$(".fc td,.fc th").width("14%");
	//$(".fc td,.fc th").height("14%");
	
});
