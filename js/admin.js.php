<?php
header("Content-type: text/javascript"); // turned this from admin.js akin to calendar.js.php
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
$theAdminURL = ke_getAdminURL();
/*************************************************************************************************/

global $theURL;
$theURL = $theAdminURL."/admin.php?page=".$pluginDirname."/admin/";
?>
// JavaScript Document
var j = jQuery.noConflict();
j(document).ready(function()
{	

	var plugin_url = "<?php global $theURL; echo $theURL; ?>";
	
	/** FEEDS **/
	j('.feeds-table tbody tr td:not(.option)').click(function()
	{
		var url = plugin_url+"feeds.php&action=edit&itemID="+j(this).parent().attr('id');
		location.href = url;
	});
	
	j('.feeds-add').click(function()
	{
		var url = plugin_url+"feeds.php&action=add";
		location.href = url;
	});
		
	j('.feeds-delete').click(function()
	{
		if(j('.delete:checked').length > 0)
		{
			if(confirm("Delete selected item/s?"))
			{
				var url = plugin_url+"feeds.php&section=&action=delete";
				var items = j('.delete:checked');
				for(i=0; i< items.length; i++)
				{
					url += "&itemID[]="+items[i].value;
				}
				location.href = url;
			}
		}
		else
		{
			alert("No items have been selected.");
		}
		return false;
	});
	
	j("input[name=feed_url]").change(function()
	{
		j("#feed_url_view_link").fadeIn();
	});
	
	j("#feed_url_view_link").click(function()
	{
		// TEST URL FIRST
		
		// IF URL GOOD THEN
		j.getFeed({
		   url: j("input[name=feed_url]").val(),
		   success: function(feed) 
		   {
			   alert("TEST");
			  //alert(feed.title);
		   }
		});
	});

	/** END OF FEEDS **/
	
	/** EVENTS **/
	j('.events-table tbody tr td:not(.option)').click(function()
	{
		var url = plugin_url+"events.php&action=edit&itemID="+j(this).parent().attr('id');
		location.href = url;
	});
	
	j('.events-add').click(function()
	{
		var url = plugin_url+"events.php&action=add";
		location.href = url;
	});
		
	j('.events-delete').click(function()
	{
		if(j('.delete:checked').length > 0)
		{
			if(confirm("Delete selected item/s?"))
			{
				var url = plugin_url+"events.php&section=&action=delete";
				var items = j('.delete:checked');
				for(i=0; i< items.length; i++)
				{
					url += "&itemID[]="+items[i].value;
				}
				location.href = url;
			}
		}
		else
		{
			alert("No items have been selected.");
		}
		return false;
	});
	/** END OF EVENTS **/
	
	
	/*****************************************/
	
	
	/******** TABLE DISPLAY ACTIONS **********/
	j('.tabular-data tbody tr').mouseover(function()
	{
		j(this).css({cursor:"pointer"});
		j(this).children().addClass("table-row-hover");
	});
	
	j('.tabular-data tbody tr').mouseout(function()
	{
		j(this).css({cursor:"auto"});
		j(this).children().removeClass("table-row-hover");
	});
	
	/*****************************************/
	
	/** MISC ACTIONS **/
	j('.button').mouseover(function()
	{
		j(this).css({cursor:"pointer"});
	});
	
	j('.button').mouseout(function()
	{
		j(this).css({cursor:"auto"});
	});
	
	j('.back').click(function()
	{
		history.back();
	});
	
	/*****************************************/
	
	j(function()
	{
		//j('.date').datepicker({ dateFormat: 'dd/mm/yy' });
		
		// bmb
		j(".date").datepicker({dateFormat: 'dd/mm/yy', showOn: 'button', buttonImage: '<?php global $pluginRelativeLocation; echo $pluginRelativeLocation; ?>/images/icon-datepicker.png', buttonImageOnly: true});

	});
	
});

function url_text(str)
{
	str = trim(str);
	str = str.toLowerCase();
	str = str.replace(/\./gi, "");
	str = str.replace(/&/gi, "-");
	str = str.replace(/\s/gi, "-");
	str = str.replace(/--/gi, "-");
	str = str.replace(/--/gi, "-");	
	return Url.encode(str);	
}

/**
*
*  Javascript trim, ltrim, rtrim
*  http://www.webtoolkit.info/
*
**/
 
function trim(str, chars) {
	return ltrim(rtrim(str, chars), chars);
}
 
function ltrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}
 
function rtrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}

/**
*
*  URL encode / decode
*  http://www.webtoolkit.info/
*
**/
 
var Url = {
 
	// public method for url encoding
	encode : function (string) {
		return escape(this._utf8_encode(string));
	},
 
	// public method for url decoding
	decode : function (string) {
		return this._utf8_decode(unescape(string));
	},
 
	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";
 
		for (var n = 0; n < string.length; n++) {
 
			var c = string.charCodeAt(n);
 
			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}
 
		}
 
		return utftext;
	},
 
	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;
 
		while ( i < utftext.length ) {
 
			c = utftext.charCodeAt(i);
 
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
 
		}
 
		return string;
	}
 
}

function get_url_param(param) 
{
	var query = window.location.search.substring(1);
	var vars = query.split("&");
	for (var i=0;i < vars.length;i++) 
	{
		var pair = vars[i].split("=");
		if (pair[0] == param) 
		{
			return pair[1];
		}
	} 
}

j(document).ready(function()
{	
	//j(".colorpicker").ColorPicker({flat: true});
	
	if(j("input[name=ke_setting_event_colour]").length > 0 && j("input[name=ke_setting_event_colour]").val() != "")
	{
		var colorpicker_start_colour = j("input[name=ke_setting_event_colour]").val();
		colorpicker_start_colour = colorpicker_start_colour.substring(1);
		
		j('#colorSelector div').css('backgroundColor', '#' + colorpicker_start_colour);
	}
	else
	{
		var colorpicker_start_colour = "0000ff";
		j('#colorSelector div').css('backgroundColor', '#' + colorpicker_start_colour);
		
		colorpicker_start_colour = "#"+colorpicker_start_colour;
	}
	
	if(j("input[name=setting_event_colour_hover]").length > 0 && j("input[name=setting_event_colour_hover]").val() != "")
	{
		var colorpicker_start_colour_hover = j("input[name=ke_setting_event_colour_hover]").val();
		colorpicker_start_colour_hover = colorpicker_start_colour_hover.substring(1);
		
		j('#colorSelectorHover div').css('backgroundColor', '#' + colorpicker_start_colour_hover);
	}
	else
	{
		var colorpicker_start_colour_hover = "0000ff";
		j('#colorSelectorHover div').css('backgroundColor', '#' + colorpicker_start_colour_hover);
		
		colorpicker_start_colour_hover = "#"+colorpicker_start_colour_hover;
	}
	
	j('#colorSelector').ColorPicker(
	{
		color: colorpicker_start_colour,
		onShow: function (colpkr) {
			//alert(colorpicker_start_colour);
			j(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			j(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			j('#colorSelector div').css('backgroundColor', '#' + hex);
			j("input[name=ke_setting_event_colour]").val('#' + hex);
		}
	});
	
	j('#colorSelectorHover').ColorPicker(
	{
		color: colorpicker_start_colour_hover,
		onShow: function (colpkr) {
			//alert(colorpicker_start_colour);
			j(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			j(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			j('#colorSelectorHover div').css('backgroundColor', '#' + hex);
			j("input[name=ke_setting_event_colour_hover]").val('#' + hex);
		}
	});


   
});
	
