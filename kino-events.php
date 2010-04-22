<?php
/* 
 * Plugin Name:   Kino Events Plugin
 * Version:       0.1
 * Plugin URI:    http://www.kinocreative.co.uk/wordpress-plugins/kino-events-calendar-plugin-for-wordpress
 * Description:   Events calendar plugin that allows external feeds to be imported via XML/RSS
 * Author:        Richard Telford
 * Author URI:    http://www.kinocreative.co.uk/
 */
 
include_once $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/kino-events-calendar-plugin/ke-functions.php";
include_once $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/kino-events-calendar-plugin/rss_php.php";

function ke_query($params = "")
{
	global $table_prefix;
	
	$where = "";
	$order = " DESC ";
	$orderby = " event_start_date ";
	
	if(!empty($params) && strstr($params, "="))
	{
		$params = unkeyvaluepair($params);
		
		if(!empty($params['numberposts']))
		{
			$limit = " LIMIT ".$params['numberposts'];
		}
		
		if(!empty($params['post_status']))
		{
			$where = (empty($where))?(" event_status = '".mysql_real_escape_string($params['post_status'])."' "):(" AND event_status = '".mysql_real_escape_string($params['post_status'])."' ");
		}
		
		if(!empty($params['orderby']))
		{
			$orderby = " ORDER BY ".$params['orderby']." ";
		}
		
		if(!empty($params['order']))
		{
			$order = $params['order'];
		}
		
		$query = "SELECT * FROM ".$table_prefix."ke_events ".$where.$orderby.$order.$limit;
		$rows = db_get_rows($query);
	}
	else
	{
		$query = "SELECT * FROM ".$table_prefix."ke_events ".$orderby.$order;
		$rows = db_get_rows($query);
	}
	return $rows;
}

function ke_the_content($content)
{
	global $post;
	global $category;
	global $table_prefix;
	
	
	if(get_option("ke_setting_wp_page") == $post->ID)
	{
		// SHOW EVENTS STUFF HERE	
		include_once $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/kino-events-calendar-plugin/ke-front-end.php";
		return;
	}
	return $content;
}

add_filter("the_content","ke_the_content");

/***
* ke_add_events()
* 
*/
if(!function_exists("ke_add_events"))
{
	function ke_add_events($feed_url, $feed_id)
	{
		global $table_prefix;
		
		$rss = ke_parse_feed($feed_url);
		$channel = $rss->getChannel();
		$items = $rss->getItems();
		
		foreach($items as $x)
		{			
			// NEED TO CHECK IF GUID ALREADY EXISTS
			$query = "SELECT * FROM ".$table_prefix."ke_events WHERE event_url = '".mysql_real_escape_string($x['guid'])."' LIMIT 1";
			$result = db_query($query);
			
			if($result && mysql_num_rows($result) > 0)
			{
				// ALREADY EXISTS - DON'T BOTHA YERSELF SWEETHEART
			}
			else
			{
				$event_publish_date = date("Y-m-d H:i:s", strtotime($x['pubDate']));
				
				$query = "INSERT INTO ".$table_prefix."ke_events 
				(
				 
					feed_id, 
					event_channel, 
					event_title, 
					event_detail, 
					event_publish_date, 
					event_url, 
					event_type,
					event_added, 
					event_status
					
				 ) VALUES (
				 
					'".mysql_real_escape_string($feed_id)."', 
					'".mysql_real_escape_string(serialize($channel))."', 
					'".mysql_real_escape_string($x['title'])."', 
					'".mysql_real_escape_string($x['description'])."', 
					'".mysql_real_escape_string($event_publish_date)."', 
					'".mysql_real_escape_string($x['guid'])."', 
					'external', 
					NOW(), 
					0
				 
				 )";
				
				//print $query; exit;
				
				db_query($query);
			}/* END IF*/
					
						
		}/* END FOREACH */
		
	}
}

/***
* ke_parse_feed()
* Dummy function for rss_php
* Could use a different parser/library - we'll see ;)
*/
if(!function_exists("ke_parse_feed"))
{
	function ke_parse_feed($feed_url)
	{
		$rss = new rss_php;
		$rss->load($feed_url);
		return $rss;
	}
}

/***
* ke_get_feeds()
* I'm hungry goddamnit!
*/
if(!function_exists("ke_get_feeds"))
{
	function ke_get_feeds($params = "")
	{
		global $table_prefix;
		
		$orderby = "";
		$where = "";
		$joins = "";
		
		if(!empty($params))
		{
			$params = unkeyvaluepair($params);
			if(isset($params['id']))
			{
				$where .= (!empty($where))?(" AND feed_id = '".$params['id']."' "):(" feed_id = '".$params['id']."' ");
			}
			
			if(isset($params['orderby']))
			{
				$orderby = " ORDER BY ".$params['orderby'];
			}
			
			$where = (!empty($where))?(" WHERE ".$where):("");
		}
		$query = "SELECT * FROM ".$table_prefix."ke_feeds ".$joins.$where.$orderby;
		return db_get_rows($query);
		
	}
}

/***
* ke_get_events()
* 
*/
if(!function_exists("ke_get_events"))
{
	function ke_get_events($params = "")
	{
		global $table_prefix;
		
		$orderby = "";
		$where = "";
		$joins = "";
		
		if(!empty($params))
		{
			$params = unkeyvaluepair($params);
			
			if(isset($params['status']))
			{
				$where .= (!empty($where))?(" AND event_status = '".$params['status']."' "):(" event_status = '".$params['status']."' ");
			}
			
			if(isset($params['orderby']))
			{
				$orderby = " ORDER BY ".$params['orderby'];
			}
			
			$where = (!empty($where))?(" WHERE ".$where):("");
		}
		$query = "SELECT * FROM ".$table_prefix."ke_events ".$joins.$where.$orderby;
		return db_get_rows($query);
		
	}
}

/***
* ke_settings()
* First page for plugin
*/
if(!function_exists("ke_settings"))
{
	function ke_settings()
	{
		global $table_prefix;
		include_once $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/kino-events-calendar-plugin/admin/settings.php";	
	}
}


								
if(!function_exists("ke_get_nested_pages"))
{
	function ke_get_nested_pages($parent_id, &$pages, &$depth)
	{
		global $table_prefix;
		
		$query = "SELECT * FROM ".$table_prefix."posts WHERE post_type = 'page' AND post_parent = '".$parent_id."' ORDER BY menu_order";
		$result = mysql_query($query);
		if($result && mysql_num_rows($result) > 0)
		{
			while($row = mysql_fetch_assoc($result))
			{
				$depth++;
				$pages[] = array_merge($row, array("depth"=>$depth));
				ke_get_nested_pages($row['ID'], $pages, $depth);
				$depth--;
			}
		}
	}
}
								
								

/***
* ke_menu()
* Inserts menu items for plugin
*/
if(!function_exists("ke_menu"))
{
	function ke_menu()
	{
		global $menu;
		
		$plugin_menu_icon = "/wp-content/plugins/kino-events-calendar-plugin/images/menu-single.png";
		
		add_menu_page("Events", "Events", 8, __FILE__, "", $plugin_menu_icon);

		add_submenu_page(__FILE__, "Settings", "Settings", 8, __FILE__, "ke_settings");
		add_submenu_page(__FILE__, "Events", "Events", 8, "kino-events/admin/events.php");
		//add_submenu_page(__FILE__, "Feeds", "Feeds", 8, "kino-events/admin/feeds.php");
		//add_submenu_page(__FILE__, "Sandbox", "Sandbox", 8, "kino-events/admin/sandbox.php");

	}
}

/***
* ke_admin_head()
* Allows elements to be inserted into the WP admin <head> tags
*/
if(!function_exists("ke_admin_head"))
{
	function ke_admin_head()
	{
		?>
		
<!--kino-events-->
<script type="text/javascript" src="/wp-content/plugins/kino-events-calendar-plugin/js/jquery-ui-1.7.2.custom.min.js"></script>
<script type="text/javascript" src="/wp-content/plugins/kino-events-calendar-plugin/js/jquery.jfeed.js"></script>
<script type="text/javascript" src="/wp-content/plugins/kino-events-calendar-plugin/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="/wp-content/plugins/kino-events-calendar-plugin/js/admin.js"></script>
<script type="text/javascript" src="/wp-content/plugins/kino-events-calendar-plugin/colorpicker/js/colorpicker.js"></script>

<link rel="stylesheet" media="screen" type="text/css" href="/wp-content/plugins/kino-events-calendar-plugin/colorpicker/css/colorpicker.css" />
<link rel="stylesheet" type="text/css" href="/wp-content/plugins/kino-events-calendar-plugin/css/admin.css" />
<link href="/wp-content/plugins/kino-events-calendar-plugin/css/smoothness/jquery-ui-1.7.2.custom.css" type="text/css" rel="Stylesheet" class="ui-theme">
<!--/kino-events-->
<?php
	}
}

/***
* ke_head()
* Allows elements to be inserted into the front-end WP <head> tags
*/
if(!function_exists("ke_head"))
{
	function ke_head()
	{
		// NEED TO PASS EVENT TO MAIN.JS.PHP IF SELECTED
		$evt = "";
		if(isset($_GET['evt']) && !empty($_GET['evt']))
		{
			$evt = "?evt=".stripslashes($_GET['evt']);
		}
		?>
		
<!--kino-events-->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript" src="/wp-content/plugins/kino-events-calendar-plugin/js/jquery-ui-1.7.2.custom.min.js"></script>

<script type="text/javascript" src="/wp-content/plugins/kino-events-calendar-plugin/js/tools.tooltip-1.1.2.js"></script>
<script type="text/javascript" src="/wp-content/plugins/kino-events-calendar-plugin/js/tools.tooptip.slide-1.0.0.min.js"></script>
<script type="text/javascript" src="/wp-content/plugins/kino-events-calendar-plugin/js/tools.tooltip.dynamic-1.0.1.min.js"></script>

<script type="text/javascript" src="/wp-content/plugins/kino-events-calendar-plugin/js/calendar.js.php"></script>
<script type="text/javascript" src="/wp-content/plugins/kino-events-calendar-plugin/js/main.js.php<?php print $evt; ?>"></script>

<link rel='stylesheet' type='text/css' href='/wp-content/plugins/kino-events-calendar-plugin/css/calendar.css.php' />
<link rel="stylesheet" type="text/css" href="/wp-content/plugins/kino-events-calendar-plugin/css/main.css" />

<!--[if IE]>
<link rel="stylesheet" type="text/css" href="/wp-content/plugins/kino-events-calendar-plugin/css/ie.css" />
<![endif]-->
<!--/kino-events-->


<?php
	}
}

/* IN CASE ANY SHORTCODES NEEDED
add_shortcode('products', 'lc_products_shortcode');
add_shortcode('store-locator', 'lc_store_locator');
*/

function ke_widget($args)
{
	global $table_prefix;
	$options = get_option("ke_widget");
	if (!is_array( $options ))
	{
		$options = array(
		'title' => 'Events Calendar', 
		'title_class' => 'sidebar-title'
		);
	}
	
	/*
	extract($args);
	echo $before_widget;
	echo $before_title;
	
	echo $options['title'];
	
	echo $after_title;
	*/
	
	
	// DO CALENDAR SHIT NOW! WOO!
	// 
	?>	
	<h2 class="widgettitle"><?php print $options['title']; ?><br/><br/></h2>
	<div id="calendar"></div>
	
	<?php
	// NEED TO GET THE URL FOR THE EVENTS DESTINATION PAGE
	$query = "SELECT * FROM ".$table_prefix."ke_settings 
	WHERE ".$table_prefix."ke_settings.setting_id = 1";
	$ke_settings = db_get_rows($query);
	$event_url = get_permalink($ke_settings[0]['setting_wp_page']);
	?>
	<p><a class="more-link" href="<?php print get_permalink(get_option("ke_setting_wp_page"));?>">See all events</a></p>
	<?php
	//echo $after_widget;
}

function ke_widget_control()
{
	$options = get_option("ke_widget");
	if (!is_array( $options ))
	{
		$options = array(
		'title' => 'Events Calendar', 
		'title_class' => 'sidebar-title'
		);
	}
	
	if ($_POST['ke_submit'])
	{
		
		$options['title'] = htmlspecialchars($_POST['ke_title']);
		$options['title_class'] = $_POST['ke_title_class'];
		update_option("ke_widget", $options);
	}
	
	?>
	<p>
		<label for="ke_title" >Title: </label><br/>
		<input type="text" id="ke_title" name="ke_title" class="widefat" value="<?php echo $options['title'];?>" /><br/><br/>
	<!--
		<label for="ke_title_class" >Title class: </label><br/>
		<input type="text" id="ke_title_class" name="ke_title_class" class="widefat" value="<?php echo $options['title_class'];?>" /><br/><br/>
		-->
		<input type="hidden" id="ke_submit" name="ke_submit" value="1" />
	</p>
	<?php
}

global $ke_admin_email;
$ke_admin_email = get_bloginfo("admin_email");

### activate ke
function ke_activate()
{		
	global $table_prefix;
	global $ke_admin_email;
	
	$ke_db_events = false;
	$ke_db_feeds = false;
	$ke_db_settings = false;
		
	// NEED TO CREATE DATABASE TABLES IF NOT ALREADY THERE
	$query = "SHOW TABLES LIKE '".$table_prefix."ke_events'";
	$result = mysql_query($query);
	
	if($result && mysql_num_rows($result) > 0)
		$ke_db_events = true;
	
	$query = "SHOW TABLES LIKE '".$table_prefix."ke_feeds'";
	$result = mysql_query($query);
	
	if($result && mysql_num_rows($result) > 0)
		$ke_db_feeds = true;
	
	$query = "SHOW TABLES LIKE '".$table_prefix."ke_settings'";
	$result = mysql_query($query);
	
	if($result && mysql_num_rows($result) > 0)
		$ke_db_settings = true;
	
	// IF NONE OF THE TABLES EXIST, WE NEED TO CREATE THEM
	if(!$ke_db_events && !$ke_db_feeds && !$ke_db_settings)
	{
		// -- ----------------------------
		// --  Table structure for '".$table_prefix."ke_settings'
		// -- ----------------------------
		$query = "DROP TABLE IF EXISTS '".$table_prefix."ke_settings'";
		mysql_query($query);
		
		$query = "CREATE TABLE ".$table_prefix."ke_settings (
		  setting_id mediumint(9) DEFAULT '1',
		  setting_wp_page mediumint(9) DEFAULT NULL,
		  setting_feed_frequency varchar(32) DEFAULT NULL,
		  setting_event_colour varchar(32) DEFAULT NULL,
		  setting_event_colour_hover varchar(32) DEFAULT NULL,
		  setting_admin_notify tinyint(4) DEFAULT NULL,
		  setting_admin_email varchar(255) DEFAULT NULL,
		  setting_modified datetime DEFAULT NULL,
		  PRIMARY KEY (setting_id)
		)";
		mysql_query($query);
	
		// -- ----------------------------
		// --  Records of '".$table_prefix."ke_settings'
		// -- ----------------------------
		$query = "INSERT INTO ".$table_prefix."ke_settings VALUES ('1', '0', 'daily', '', '', '1', '".$ke_admin_email."', '2010-01-01 09:00:00')";
		mysql_query($query);
		
		// -- ----------------------------
		// --  Table structure for '".$table_prefix."ke_feeds'
		// -- ----------------------------
		
		$query = "DROP TABLE IF EXISTS '".$table_prefix."ke_feeds'";
		mysql_query($query);
		
		$query = "CREATE TABLE ".$table_prefix."ke_feeds (
		  feed_id mediumint(9) NOT NULL AUTO_INCREMENT,
		  feed_title varchar(255) DEFAULT NULL,
		  feed_url varchar(255) DEFAULT NULL,
		  feed_detail text,
		  feed_order tinyint(4) DEFAULT NULL,
		  feed_status tinyint(4) DEFAULT '0',
		  feed_added datetime DEFAULT NULL,
		  feed_modified datetime DEFAULT NULL,
		  PRIMARY KEY (feed_id)
		)";
		mysql_query($query);
		
		
		// -- ----------------------------
		// --  Table structure for '".$table_prefix."ke_events'
		// -- ----------------------------
		$query = "DROP TABLE IF EXISTS '".$table_prefix."ke_events'";
		mysql_query($query);
		
		$query = "CREATE TABLE ".$table_prefix."ke_events (
		  event_id mediumint(9) NOT NULL AUTO_INCREMENT,
		  feed_id mediumint(9) DEFAULT NULL,
		  event_channel text,
		  event_title varchar(255) DEFAULT NULL,
		  event_slug varchar(255) DEFAULT NULL,
		  event_location varchar(255) DEFAULT NULL,
		  event_type varchar(32) DEFAULT NULL,
		  event_start_date date DEFAULT NULL,
		  event_start_time time DEFAULT NULL,
		  event_end_date date DEFAULT NULL,
		  event_end_time time DEFAULT NULL,
		  event_url varchar(255) DEFAULT NULL,
		  event_detail text,
		  event_order tinyint(4) DEFAULT NULL,
		  event_status tinyint(4) DEFAULT '0',
		  event_publish_date datetime DEFAULT NULL,
		  event_added datetime DEFAULT NULL,
		  event_modified datetime DEFAULT NULL,
		  PRIMARY KEY (event_id)
		)";
		mysql_query($query);
	}
}

//add_action('activate_' . plugin_basename(__FILE__), 'ke_activate' );

register_activation_hook( __FILE__, 'ke_activate' );

//register_sidebar(array('name'=>'events-calendar'));
register_sidebar_widget('Events Calendar', 'ke_widget');
register_widget_control( 'Events Calendar', 'ke_widget_control' );
	
add_action('admin_menu', 'ke_menu');
add_action("wp_head", "ke_head" );
add_action("admin_head", "ke_admin_head");

add_action('wp_footer', 'ke_footer');

function ke_footer()
{
  $content = '<div id="event-tooltip"></div>';
  echo $content;
}

?>
