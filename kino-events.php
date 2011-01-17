<?php
/* 
 * Plugin Name:   Kino Events Calendar Plugin
 * Version:       3.0.1.1
 * Plugin URI:    http://www.kinocreative.co.uk/wordpress-plugins/kino-events-calendar-plugin-for-wordpress
 * Description:   Events calendar plugin
 * Author:        Richard Telford
 * Author URI:    http://www.kinocreative.co.uk/
 */
 
include "config.php";


$old_version = false;
if(floatval($GLOBALS['wp_version']) < 3)
{
	$old_version = true;
}


function events_shortcode_func($atts)
{
	ob_start(); // start buffer
	include $_SERVER['DOCUMENT_ROOT'].PLUGIN_PATH."/template.php";
	$content = ob_get_contents(); // assign buffer contents to variable
	ob_end_clean(); // end buffer and remove buffer contents
	return $content;
}

add_shortcode('events', 'events_shortcode_func');

if(function_exists( 'add_theme_support' ))
{
	add_theme_support( 'post-thumbnails' );
}

if(!function_exists("time_to_12hr"))
{
	function time_to_12hr($time)
	{
		$time = explode(":", $time);
		return date("g.ia", mktime(intval($time[0]), intval($time[1]), intval($time[2])));
	}
}

if(!function_exists("GetDays"))
{
	function GetDays($sStartDate, $sEndDate){
	  // Firstly, format the provided dates.
	  // This function works best with YYYY-MM-DD
	  // but other date formats will work thanks
	  // to strtotime().
	  $sStartDate = gmdate("Y-m-d", strtotime($sStartDate)); 
	  $sEndDate = gmdate("Y-m-d", strtotime($sEndDate));
	
	  // Start the variable off with the start date
	  $aDays[] = $sStartDate;
	
	  // Set a 'temp' variable, sCurrentDate, with
	  // the start date - before beginning the loop
	  $sCurrentDate = $sStartDate;
	
	  // While the current date is less than the end date
	  while($sCurrentDate < $sEndDate){
		// Add a day to the current date
		$sCurrentDate = gmdate("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
	
		// Add this new day to the aDays array
		$aDays[] = $sCurrentDate;
	  }
	
	  // Once the loop has finished, return the
	  // array of days.
	  return count($aDays);
	}
}

/**
 * KinoEvents Widget
 */
class KinoEvents extends WP_Widget {
    /** constructor */
    function KinoEvents()
	{
		
		/* Widget settings. */
		$widget_opts = array( 'classname' => 'kino-events', 'description' => 'Event calendar widget.' );

		/* Widget control settings. */
		//$control_opts = array( 'width' => 300, 'height' => 350, 'id_base' => 'event-widget' );

		/* Create the widget. */
		//$this->WP_Widget( 'example-widget', 'Example Widget', $widget_opts, $control_ops );
		$this->WP_Widget( 'kino-events', 'Event Calendar', $widget_opts );

    }

    /** @see WP_Widget::widget */
    function widget($args, $instance)
	{		
        extract( $args );
		
		$title = esc_attr($instance['title']);
		
		print $before_widget;
		print $before_title.$title.$after_title;
		print "<div id='ke-calendar'></div><p class='kccredit'><a href='http://www.kinocreative.co.uk'>Design by Kino Creative</a>";
		print $after_widget;
		
	}

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance)
	{				
        return $new_instance;
    }

    /** @see WP_Widget::form */
    function form($instance)
	{			
		$title = esc_attr($instance['title']);
        ?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?><input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" class="long widefat" value="<?php print $title; ?>"/></label></p>
		<?php
    }

} // class KinoEvents

function post_type_events()
{
	register_post_type('events', array(
		'labels' => array(
			'name' => _x('Events', 'post type general name'),
			'singular_name' => _x('Event', 'post type singular name'),
			'add_new' => _x('Add New', 'events'),
			'add_new_item' => __('Add New Event'),
			'edit_item' => __('Edit Event'),
			'new_item' => __('New Event'),
			'view_item' => __('View Event'),
			'search_items' => __('Search Events'),
			'not_found' =>  __('No event/s found'),
			'not_found_in_trash' => __('No event/s found in Trash'), 
			'parent_item_colon' => ''),
		'public' => true,
		'show_ui' => true, // UI in admin panel
		'_builtin' => false, // It's a custom post type, not built in!
		'capability_type' => 'post',
		'hierarchical' => false,
		'rewrite' => array("slug" => "events"), // Permalinks format or set to true
		//'menu_icon' => '/wp-content/themes/pelicanpr/images/admin-icon-case-studies.png',
		'supports' => array('title','editor','excerpt','thumbnail')

	));	
}


function taxonomies_events() 
{
	$labels = array(
		'name' => _x( 'Categories', 'taxonomy general name' ),
		'singular_name' => _x( 'Category', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Categories' ),
		'popular_items' => __( 'Popular Categories' ),
		'all_items' => __( 'All Categories' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit Category' ), 
		'update_item' => __( 'Update Category' ),
		'add_new_item' => __( 'Add New Category' ),
		'new_item_name' => __( 'New Category Name' ),
		'separate_items_with_commas' => __( 'Separate categories with commas' ),
		'add_or_remove_items' => __( 'Add or remove categories' ),
		'choose_from_most_used' => __( 'Choose from the most used categories' ),
	); 	
	
	register_taxonomy('event_category', 'events', array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'event-category' ),
	));
}

function events_edit_columns($columns)
{
	$columns = array(
		"cb" => "<input type=\"checkbox\" />",
		"title" => "Title",
		"location" => "Location",
		"start_date" => "Start date",
		"start_time" => "Start time	",
		"end_date" => "End date",
		"end_time" => "End time",
		"detail" => "Detail",
		"author" => "Author",
		"date" => "Date"
	);
	return $columns;
}

function events_custom_columns($column)
{
	global $post;
	$custom = get_post_custom();
	
	switch ($column)
	{
	
		case "location":
		
			echo $custom["_event_location"][0];
			break;
			
		case "start_date":
		
			echo date("d/m/Y", strtotime($custom["_event_start_date"][0]));
			break;

		case "start_time":
			if(empty($custom["_event_start_time"][0]) || $custom["_event_all_day"][0] == 1)
			{
				echo "-";
			}
			else
			{
				echo date("g.ia", strtotime($custom["_event_start_time"][0]));
			}
			break;

		case "end_date":
		
			echo date("d/m/Y", strtotime($custom["_event_end_date"][0]));
			break;

		case "end_time":
			if(empty($custom["_event_end_time"][0]) || $custom["_event_all_day"][0] == 1)
			{
				echo "-";
			}
			else
			{
				echo date("g.ia", strtotime($custom["_event_end_time"][0]));
			}
			break;
		
		case "detail":
			echo substr(strip_tags($post->post_content), 0, 64);
			break;
			
		
		

		
			

		/*case "status":
		
			echo ($custom["_user_status"][0] == 1)?("Yes"):("-");
			break;*/
	}
}

function event_meta_options()
{
	global $post;
	
	$custom 				= get_post_custom($post->ID);
	
	$_event_location		= $custom["_event_location"][0];
	$_event_all_day			= $custom["_event_all_day"][0];
	$_event_recurring		= $custom["_event_recurring"][0];
	$_event_frequency		= $custom["_event_frequency"][0];
	$_event_start_date		= $custom["_event_start_date"][0];
	$_event_start_time		= $custom["_event_start_time"][0];
	$_event_end_date		= $custom["_event_end_date"][0];
	$_event_end_time		= $custom["_event_end_time"][0];
	$_event_color			= $custom["_event_color"][0];
	$_event_hover_color		= $custom["_event_hover_color"][0];
	
	list($_event_start_time_hh, $_event_start_time_mm) = explode(":", $_event_start_time);
	list($_event_end_time_hh, $_event_end_time_mm) = explode(":", $_event_end_time);
	
	?>
	<div class="row">
		<span class="label"><label><strong>Location:</strong></label></span>
		<span class="field">
			<input type="text"  name="_event_location" value="<?php print $_event_location; ?>" />
		</span>
		<br class="clear" />
	</div>
	
	<div class="row">
		<span class="label"><label><strong>All day?:</strong></label></span>
		<span class="field">
			<input type="checkbox" name="_event_all_day" value="1" <?php print ($_event_all_day )?("checked"):(""); ?> />
		</span>
		<br class="clear" />
	</div>
	
	<div class="row">
		<span class="label"><label><strong>Recurring?:</strong></label></span>
		<span class="field">
			<input type="checkbox" name="_event_recurring" value="1" <?php print ($_event_recurring)?("checked"):(""); ?> />
		</span>
		<br class="clear" />
	</div>
	
	<div class="row recurring">
		<span class="label"><label><strong>Frequency:</strong></label></span>
		<span class="field">
			<select name="_event_frequency">
				<option value="daily" <?php print ($_event_frequency == "daily")?("selected"):(""); ?>>every day</option>
				<option value="weekly" <?php print ($_event_frequency == "weekly")?("selected"):(""); ?>>every week</option>
				<option value="fortnightly" <?php print ($_event_frequency == "fortnightly")?("selected"):(""); ?>>every 2 weeks</option>
				<option value="monthly" <?php print ($_event_frequency == "monthly")?("selected"):(""); ?>>every month</option>
			</select>
		</span>
		<br class="clear" />
	</div>
	
	<div class="row">
		<span class="label"><label><strong>Start date:</strong></label></span>
		<span class="field">
			<input type="text" class="date" name="_event_start_date" value="<?php print (!empty($_event_start_date))?(date("d/m/Y", strtotime($_event_start_date))):(""); ?>" />
		</span>
		<br class="clear" />
	</div>
	
	<div class="row not-all-day">
		<span class="label"><label><strong>Start time:</strong></label></span>
		<span class="field">
			<select name="_event_start_time_hh">
				
				<?php
				for($i=0; $i<24; $i++)
				{
					?><option value="<?php printf("%02s", $i); ?>" <?php print ($_event_start_time_hh == sprintf("%02s", $i))?("selected"):(""); ?>><?php printf("%02s", $i); ?></option><?php
				}
				?>
			</select>:
			<select name="_event_start_time_mm">
			
				<?php
				for($i=0; $i<60; $i+=5)
				{
					?><option value="<?php printf("%02s", $i); ?>" <?php print ($_event_start_time_mm == sprintf("%02s", $i))?("selected"):(""); ?>><?php printf("%02s", $i); ?></option><?php
				}
				?>
			</select>
		</span>
		<br class="clear" />
	</div>
	
	<div class="row end-date">
		<span class="label"><label><strong>End date:</strong></label></span>
		<span class="field">
			<input type="text" class="date" name="_event_end_date" value="<?php print (!empty($_event_end_date))?(date("d/m/Y", strtotime($_event_end_date))):(""); ?>" />&nbsp;&nbsp;<a href="#" class="set-duration">or specify duration in days</a>
		</span>
		<br class="clear" />
	</div>
	
	<div class="row duration">
		<span class="label"><label><strong>Duration:</strong></label></span>
		<span class="field">
			<input type="text" name="_event_duration" value="<?php print $_event_duration; ?>" />&nbsp;&nbsp;<a href="#" class="set-end-date">or specify end date</a>
		</span>
		<br class="clear" />
	</div>
	
	<div class="row not-all-day">
		<span class="label"><label><strong>End time:</strong></label></span>
		<span class="field">
			<select name="_event_end_time_hh">
				
				<?php
				for($i=0; $i<24; $i++)
				{
					?><option value="<?php printf("%02s", $i); ?>" <?php print ($_event_end_time_hh == sprintf("%02s", $i))?("selected"):(""); ?>><?php printf("%02s", $i); ?></option><?php
				}
				?>
			</select>:
			<select name="_event_end_time_mm">
				
				<?php
				for($i=0; $i<60; $i+=5)
				{
					?><option value="<?php printf("%02s", $i); ?>" <?php print ($_event_end_time_mm == sprintf("%02s", $i))?("selected"):(""); ?>><?php printf("%02s", $i); ?></option><?php
				}
				?>
			</select>
		</span>
		<br class="clear" />
	</div>
	<?php
	/*
	<div class="row">
		<span class="label"><label><strong>Color:</strong></label></span>
		<span class="field">
			<input type="text" name="_event_color" value="<?php print $_event_color; ?>" />
			<div class="fl colorSelector" id="colorSelector"><div style="background-color: #0000ff"></div></div>
		</span>
		<br class="clear" />
	</div>
	
	<div class="row">
		<span class="label"><label><strong>Hover color:</strong></label></span>
		<span class="field">
			<input type="text" name="_event_hover_color" value="<?php print $_event_hover_color; ?>" />
			<div class="fl colorSelector" id="colorSelectorHover"><div style="background-color: #0000ff"></div></div>
		</span>
		<br class="clear" />
	</div>
	*/
	?>
	<?php
}

function save_event()
{
	global $post;
	
	// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
	// to do anything
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		return $post_id;
	
	
	// Check permissions
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
		return $post_id;
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) )
		return $post_id;
	}
	
	$_POST["_event_start_date"] 	= (!preg_match("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/", $_POST["_event_start_date"]))?(" NULL "):(preg_replace("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/si", "$3-$2-$1", $_POST["_event_start_date"]));
	$_POST["_event_end_date"] 		= (!preg_match("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/", $_POST["_event_end_date"]))?(" NULL "):(preg_replace("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/si", "$3-$2-$1", $_POST["_event_end_date"]));

	$_POST["_event_start_time"] 	= $_POST["_event_start_time_hh"].":".$_POST["_event_start_time_mm"].":00";
	$_POST["_event_end_time"]		= $_POST["_event_end_time_hh"].":".$_POST["_event_end_time_mm"].":00";
	
	// IF DURATION IS SET INSTEAD OF END DATE - NEED TO CALCULATE THE END DATE
	if(!empty($_POST["_event_duration"]))
	{
		$tmp = str_replace("'", "", $_POST["_event_start_date"]);
		
		// WEIRD AND WONDERFUL WAY OF CALCULATING NEW END DATE
		$timestamp = mktime(0, 0, 0, date("m", strtotime($tmp)), date("d", strtotime($tmp)) + $_POST["_event_duration"] - 1, date("Y", strtotime($tmp)) ) ;
		$_POST["_event_end_date"] = date("Y-m-d", $timestamp);
		
		// CLEAR EVENT DURATION
		$_POST["_event_end_duration"] = 0;
		
	}
	
	// IF AN ALL DAY EVENT - SET START/END TIMES FROM 00:00:00 - 23:59:59 (OR NOT SHOW TIME?)
	if($_POST["_event_all_day"])
	{
		$_POST["_event_start_time"] = "00:00:00";
		$_POST["_event_end_time"] = "23:59:59";
	}

	update_post_meta($post->ID, "_event_location", $_POST["_event_location"]);
	update_post_meta($post->ID, "_event_all_day", $_POST["_event_all_day"]);
	update_post_meta($post->ID, "_event_recurring", $_POST["_event_recurring"]);
	update_post_meta($post->ID, "_event_frequency", $_POST["_event_frequency"]);
	update_post_meta($post->ID, "_event_start_date", $_POST["_event_start_date"]);
	update_post_meta($post->ID, "_event_start_time", $_POST["_event_start_time"]);
	update_post_meta($post->ID, "_event_end_date", $_POST["_event_end_date"]);
	update_post_meta($post->ID, "_event_end_duration", $_POST["_event_end_duration"]);
	update_post_meta($post->ID, "_event_end_time", $_POST["_event_end_time"]);
	update_post_meta($post->ID, "_event_color", $_POST["_event_color"]);
	update_post_meta($post->ID, "_event_hover_color", $_POST["_event_hover_color"]);
}

function ec_head()
{
	?>
<!--kino-events-->
<script type="text/javascript">
var plugin_path = '<?php print PLUGIN_PATH; ?>';
var ec_color = '<?php print get_option(PLUGIN_SHORT_NAME."_color"); ?>';
var ec_hover_color = '<?php print get_option(PLUGIN_SHORT_NAME."_hover_color"); ?>';
var event_id;
</script>
	
<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php print PLUGIN_PATH; ?>/js/jquery-ui-1.7.2.custom.min.js"></script>

<script type="text/javascript" src="<?php print PLUGIN_PATH; ?>/js/tools.tooltip-1.1.2.js"></script>
<script type="text/javascript" src="<?php print PLUGIN_PATH; ?>/js/tools.tooptip.slide-1.0.0.min.js"></script>
<script type="text/javascript" src="<?php print PLUGIN_PATH; ?>/js/tools.tooltip.dynamic-1.0.1.min.js"></script>

<script type="text/javascript" src="<?php print PLUGIN_PATH; ?>/js/calendar.js"></script>
<script type="text/javascript" src="<?php print PLUGIN_PATH; ?>/js/main.js"></script>

<link rel='stylesheet' type='text/css' href='<?php print PLUGIN_PATH; ?>/css/calendar.css' />
<link rel="stylesheet" type="text/css" href="<?php print PLUGIN_PATH; ?>/css/main.css" />

<!--[if IE]>
<link rel="stylesheet" type="text/css" href="<?php print PLUGIN_PATH; ?>/css/ie.css" />
<![endif]-->

<style type="text/css">
.fc-event,
.fc-agenda .fc-event-time,
.fc-event a {
border-style: solid; 
border-color: <?php print get_option("ec_color"); ?>;
background-color: <?php print get_option("ec_color"); ?>;
color: #333;  
}

.fc-event a:hover,
.fc-event-selected,
.fc-agenda .fc-event-selected .fc-event-time,
.fc-event-selected a {
border-style: solid; 
border-color: <?php print get_option("ec_hover_color"); ?>;     /* default BORDER color (probably the same as background-color) */
background-color: <?php print get_option("ec_hover_color"); ?>; /* default BACKGROUND color */
color: <?php print get_option("ec_hover_color"); ?>;            /* default TEXT color */
}

.fc-event a {
overflow: hidden;
font-size: .85em;
text-decoration: none;
cursor: pointer;
color: <?php print get_option("ec_color"); ?>;
}

.fc-event a:hover {
background-color: <?php print get_option("ec_hover_color"); ?>;
}
</style>
<!--/kino-events-->
	<?php
}


function admin_head()
{
	?>
	<link rel="stylesheet" media="screen" type="text/css" href="<?php print PLUGIN_PATH; ?>/colorpicker/css/colorpicker.css" />
	<link href="<?php print PLUGIN_PATH; ?>/css/smoothness/jquery-ui-1.7.2.custom.css" type="text/css" rel="Stylesheet" class="ui-theme">
	<link type="text/css" rel="stylesheet" href="<?php print PLUGIN_PATH; ?>/css/admin.css"  />
	
	<script type="text/javascript">
	var plugin_path = '<?php print PLUGIN_PATH; ?>';
	var ec_color = '<?php print get_option(PLUGIN_SHORT_NAME."_color"); ?>';
	var ec_hover_color = '<?php print get_option(PLUGIN_SHORT_NAME."_hover_color"); ?>';	
	</script>
	<script type="text/javascript" src="<?php print PLUGIN_PATH; ?>/js/admin.js"></script>
	<script type="text/javascript" src="<?php print PLUGIN_PATH; ?>/js/jquery-ui-1.7.2.custom.min.js"></script>
	<script type="text/javascript" src="<?php print PLUGIN_PATH; ?>/colorpicker/js/colorpicker.js"></script>
	<?php	
}

function ec_footer()
{
	
	// GO THROUGH ANY CATEGORIES ADDED FOR EVENTS
	$categories = get_terms("event_category", "hide_empty=0"); 
	if(count($categories) > 0)
	{
		?>
		<style type="text/css">
		<?php
		foreach($categories as $x)
		{
			$ec_cat_color[$x->term_id] = get_option("ec_cat_color_".$x->term_id);
			?>
			.fc-event.<?php print $x->slug; ?>,
			.fc-event.<?php print $x->slug; ?> a {
				border-color: <?php print $ec_cat_color[$x->term_id]; ?>;
				background-color: <?php print $ec_cat_color[$x->term_id]; ?>;
			}
			
			.fc-event.<?php print $x->slug; ?> a:hover,
			.fc-event-selected.<?php print $x->slug; ?>,
			.fc-agenda .fc-event-selected.<?php print $x->slug; ?> .fc-event-time,
			.fc-event-selected.<?php print $x->slug; ?> a {
				border-color: <?php print $ec_cat_color[$x->term_id]; ?>;    
				background-color: <?php print $ec_cat_color[$x->term_id]; ?>; 
				color: <?php print $ec_cat_color[$x->term_id]; ?>;            
			}
			<?php
		}
		?>
		</style>
		<?php
	}
		
}

function admin_init()
{
	//wp_enqueue_script('jquery');  
	add_meta_box("event-meta", "Event Details", "event_meta_options", "events", "advanced", "high");
}

function plugin_options()
{
	include "settings.php";	
}

function plugin_menu()
{
	$page_title = PLUGIN_LONG_NAME;
	$menu_title = PLUGIN_LONG_NAME;
	$capability = "manage_options";
	$menu_slug = PLUGIN_SHORT_NAME."-settings";
	$function = "plugin_options";
	
	add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
}

function ec_activate()
{
	global $table_prefix;
	global $wpdb;
	global $old_version;
	
	update_option("ec_color", '#666666');
	update_option("ec_hover_color", '#666666');
	
	// LOOK FOR THE OLD EVENTS TABLE
	$query = "SELECT * FROM {$table_prefix}ke_events";
	$result = mysql_query($query);
	
	if($result && mysql_num_rows($result) > 0)
	{
		while($row = mysql_fetch_object($result))
		{
			//$query = "INSERT INTO $wpdb->posts";
			
			$post = array(
			  'comment_status' => 'closed', // 'closed' means no comments.
			  'ping_status' => 'closed', // 'closed' means pingbacks or trackbacks turned off
			  //'post_category' => [ array(<category id>, <...>) ] //Add some categories.
			  'post_title' => $row->event_title,  //The full text of the post.
			  'post_content' => $row->event_detail,  //The full text of the post.
			  'post_status' => 'publish', //Set the status of the new post. 
			  'post_type' => 'events' //Sometimes you want to post a page.
			);  
			
			$event_id = wp_insert_post($post);
			
			// UPDATE POST META
			update_post_meta($event_id, "_event_location", $row->event_location);
			update_post_meta($event_id, "_event_all_day", $row->event_all_day);
			update_post_meta($event_id, "_event_recurring", $row->event_recurring);
			update_post_meta($event_id, "_event_frequency", $row->event_recurring_frequency);
			update_post_meta($event_id, "_event_start_date", $row->event_start_date);
			update_post_meta($event_id, "_event_start_time", $row->event_start_time);
			update_post_meta($event_id, "_event_end_date", $row->event_end_date);
			update_post_meta($event_id, "_event_end_duration", $row->event_end_duration);
			update_post_meta($event_id, "_event_end_time", $row->event_end_time);
		}
	}
	
	if(!$old_version)
	{
		$query = "DROP TABLE IF EXISTS {$table_prefix}ke_events";
		$result = mysql_query($query);
		
		$query = "DROP TABLE IF EXISTS {$table_prefix}ke_settings";
		$result = mysql_query($query);
		
		$query = "DROP TABLE IF EXISTS {$table_prefix}ke_feeds";
		$result = mysql_query($query);
	}
}

add_action("activate_plugin", "ec_activate");
add_action('wp_head', "ec_head");
add_action('wp_footer', "ec_footer");
add_action('widgets_init', create_function('', 'return register_widget("KinoEvents");'));
add_action("admin_init", "admin_init");
add_action('admin_head', "admin_head");
add_action('admin_menu', 'plugin_menu');
add_action('init', 'post_type_events');
add_action('save_post', 'save_event');
add_action("manage_posts_custom_column",  "events_custom_columns");
add_filter("manage_edit-events_columns", "events_edit_columns");
add_action( 'init', 'taxonomies_events', 0 );
?>