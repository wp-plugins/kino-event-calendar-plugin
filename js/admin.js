// JavaScript Document
jQuery(document).ready(function($)
{	
	// IS ALL DAY CHECKED ON LOAD?
	if($("input[name=_event_all_day]:checked").val() == 1)
	{
		$(".not-all-day").hide();
	}
	else
	{
		$(".not-all-day").show();
	}
	
	// ALL DAY EVENT
	$("input[name=_event_all_day]").click(function()
	{
		if($(this).attr("checked"))
		{
			// CHECKED .. HIDE TIMES
			$(".not-all-day").hide();
		}
		else
		{
			// UNCHECKED .. SHOW TIMES
			$(".not-all-day").show();
		}
	});
	
	/*
	* YOU CAN SEE WHERE CODE CAN BE CONSOLIDATED BUT FOR EASE - MEH
	*/
	
	// IS DURATION SET ON LOAD?
	if($("input[name=_event_duration]").val() != 0 && $("input[name=_event_duration]").val() != "")
	{
		// CLEAR END DATE 
		$("input[name=_event_end_date]").val("");
		
		// HIDE END DATE
		$(".end-date").hide();
		
		// SHOW DURATION
		$(".duration").show();
	}
	else
	{
		// CLEAR DURATION
		$("input[name=_event_duration]").val("");
		
		// HIDE DURATION
		$(".duration").hide();
		
		// SHOW END DATE
		$(".end-date").show();
	}
	
	// SPECIFY DURATION
	$(".set-duration").click(function()
	{
		// CLEAR END DATE 
		$("input[name=_event_end_date]").val("");
		
		// HIDE END DATE
		$(".end-date").hide();
		
		// SHOW DURATION
		$(".duration").show();
		
		return false;
	});
	
	// SPECIFY END DATE
	$(".set-end-date").click(function()
	{
		// CLEAR DURATION
		$("input[name=_event_duration]").val("");
		
		// HIDE DURATION
		$(".duration").hide();
		
		// SHOW END DATE
		$(".end-date").show();
		
		return false;
	});
	
	
	
	
	
	// IS RECURRING SET ON LOAD?
	if($("input[name=_event_recurring]:checked").val() == 1)
	{
		// SHOW RECURRING
		$(".recurring").show();
	}
	else
	{
		// HIDE RECURRING
		$(".recurring").hide();
	}
	
	// ALL DAY EVENT
	$("input[name=_event_recurring]").click(function()
	{
		if($(this).attr("checked"))
		{
			// SHOW RECURRING
			$(".recurring").show();
		}
		else
		{
			// HIDE RECURRING
			$(".recurring").hide();
		}
	});
	
	$('#colorSelector').ColorPicker(
	{
		color: ec_color,
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$('#colorSelector div').css('backgroundColor', '#' + hex);
			$("input[name=_event_color]").val('#' + hex);
		}
	});
	
	$('#colorSelectorHover').ColorPicker(
	{
		color: ec_hover_color,
		onShow: function (colpkr) {
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			$('#colorSelectorHover div').css('backgroundColor', '#' + hex);
			$("input[name=_event_hover_color]").val('#' + hex);
		}
	});
	
	
	$('.colorSelector').ColorPicker(
	{
		color: ec_hover_color,
		onShow: function (colpkr)
		{
			//var el = $(this.id.substring(this.id.indexOf("-")+1));
			
			$(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			//var el = $(this.id.substring(this.id.indexOf("-")+1));
			$(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb, elm) 
		{
			var el = $("#"+elm.id.substring(elm.id.indexOf("-")+1));
			//var el = $(elm.id.substring(elm.id.indexOf("-")+1));
			//alert(el.attr("id"));
			$(elm).find("div").css('backgroundColor', '#' + hex);
			el.val('#' + hex);
		}
	});


	$(".date").datepicker({
		changeMonth: true,
		changeYear: true, dateFormat: 'dd/mm/yy', showOn: 'button', buttonImage: plugin_path + '/images/icon-datepicker.png', buttonImageOnly: true});

});