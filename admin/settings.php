<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", "on");
$page_identifier = "settings";
$page_url = "/wp-admin/admin.php?page=kino-events/kino-events.php";
?>
<div class="wrap">
	<h2>Settings</h2>
	<?php
	extract($_GET);
	extract($_POST);
	switch($action)
	{
		case "delete":
			/*for($i=0; $i<count($itemID); $i++)
			{
				$query = "DELETE FROM ".$table_prefix."ke_settings WHERE event_id = '".$itemID[$i]."' LIMIT 1";
				db_query($query);
			}
			$redirect = $page_url;
			js_redirect($redirect);
			*/
			break;
			
		default:
		case "add":
		case "edit":
			
			if(isset($_POST['submit']))
			{	
				if(count($errors) == 0)
				{
					
					/*$query = "UPDATE ".$table_prefix."ke_settings SET 
					
						setting_wp_page = '".mysql_real_escape_string($setting_wp_page)."', 
						setting_feed_frequency = '".mysql_real_escape_string($setting_feed_frequency)."', 
						setting_admin_notify = '".mysql_real_escape_string($setting_admin_notify)."', 
						setting_admin_email = '".mysql_real_escape_string($setting_admin_email)."', 
						setting_event_colour = '".mysql_real_escape_string($setting_event_colour)."', 
						setting_event_colour_hover = '".mysql_real_escape_string($setting_event_colour_hover)."', 
						
						setting_modified = NOW() 
						
					WHERE setting_id = '1'";*/
					
					// ADD THIS TO WP_OPTIONS INSTEAD
					
					update_option("ke_setting_wp_page", $ke_setting_wp_page);
					update_option("ke_setting_wp_category", $ke_setting_wp_category);
					update_option("ke_setting_feed_frequency", $ke_setting_feed_frequency);
					update_option("ke_setting_admin_notify", $ke_setting_admin_notify);
					update_option("ke_setting_admin_email", $ke_setting_admin_email);
					update_option("ke_setting_event_colour", $ke_setting_event_colour);
					update_option("ke_setting_event_colour_hover", $ke_setting_event_colour_hover);
					
					js_redirect($redirect);
					exit;
					
					/*if(db_query($query))
					{
						$redirect = $page_url;
						js_redirect($redirect);
						exit;
					}
					print mysql_error();*/
				}
			}
			else
			{
				/*if(!isset($_GET['itemID']))
				{
					$query = "INSERT INTO ".$table_prefix."ke_settings (event_added, event_modified) VALUES (NOW(), NOW())";
					db_query($query);
					$itemID = mysql_insert_id();
					
					$redirect = $page_url."&action=".$_GET['action']."&itemID=".$itemID;
					js_redirect($redirect);
					
					exit;
				}*/
				
				/*$query = "SELECT * FROM ".$table_prefix."ke_settings WHERE ".$table_prefix."ke_settings.setting_id = 1 LIMIT 1";
				$item = db_get_rows($query);
				if(is_array($item[0]))
				{
					
					$item[0] = array_map(stripslashes, $item[0]);
					extract($item[0]);
				}*/
				
				$ke_setting_wp_page = get_option("ke_setting_wp_page");
				$ke_setting_wp_category = get_option("ke_setting_wp_category");
				$ke_setting_feed_frequency = get_option("ke_setting_feed_frequency");
				$ke_setting_admin_notify = get_option("ke_setting_admin_notify");
				$ke_setting_admin_email = get_option("ke_setting_admin_email");
				$ke_setting_event_colour = get_option("ke_setting_event_colour");
				$ke_setting_event_colour_hover = get_option("ke_setting_event_colour_hover");
				
			}
			?>
			
			<?php
			/********************
			* ADDING AND EDITING 
			*********************/
			?>
			<form id="feeds-form" method="post" action="<?php print $page_url."&action=".$_GET['action']."&itemID=".$_GET['itemID']; ?>">
				<input type="hidden" name="setting_id" class="textbox long" value="<?php print $setting_id; ?>"/>
				<input type="hidden" name="itemID" class="textbox long" value="<?php print $setting_id; ?>"/>
				
				<br class="clear" />
				<h3 class="fl">Editing settings:</h3>
				
				<div class="float-right">
					<br/><br class="clear" />
					<input type="button" class="button submit back" value="Back" /> <input type="submit" class="button submit" value="Submit" />
				</div>
				
				<table class="widefat layout">
					
					
							
					<tr>
						<th>Events page:</th>
						<td>
							<?php
							$ke_pages = array();
							$parent_id = 0;
							$depth = -1;
							
							ke_get_nested_pages($parent_id, $ke_pages, $depth);
							?>
							<select name="ke_setting_wp_page" class="dropdown long">
								<option value="">Please select a page to show events on</option>
								<?php
								foreach($ke_pages as $x)
								{
									?><option value="<?php print $x['ID']; ?>" <?php print ($ke_setting_wp_page == $x['ID'])?("selected"):(""); ?>><?php print str_repeat("-", $x['depth']).$x['post_title']; ?></option><?php
								}
								?>
							</select>
							
						</td>
					</tr>
					
					<?php
					/*
					<tr>
						<th>Events category:</th>
						<td>
							<?php
							$ke_categories = get_categories();
							?>
							<select name="ke_setting_wp_category" class="dropdown long">
								<option value="">Please select the events category</option>
								<?php
								foreach($ke_categories as $x)
								{
									?><option value="<?php print $x->term_id; ?>" <?php print ($ke_setting_wp_category == $x->term_id)?("selected"):(""); ?>><?php print $x->cat_name; ?></option><?php
								}
								?>
							</select>
							
						</td>
					</tr>
					*/
					?>
					
					<?php
					/*
					<tr>
						<th>Events category:</th>
						<td>
							<input name="ke_setting_wp_category" class="textbox long" value="<?php print $ke_setting_wp_category; ?>" />
						</td>
					</tr>
					*/
					?>
					
					<!--<tr>
						<th>Feed frequency:</th>
						<td>
							<select name="setting_feed_frequency" class="dropdown long">
								<option value="hourly" <?php print ($setting_feed_frequency == "hourly")?("selected"):(""); ?>>Hourly</option>
								<option value="daily" <?php print ($setting_feed_frequency == "daily")?("selected"):(""); ?>>Daily</option>
								<option value="weekly" <?php print ($setting_feed_frequency == "weekly")?("selected"):(""); ?>>Weekly</option>
							</select>
						</td>
					</tr>
					-->
					
					<!--
					<tr>
						<th>Feed ID:</th>
						<td>
							<input type="text" name="feed_id" class="textbox long" value="<?php print $feed_id; ?>"/>
						</td>
					</tr>
					-->
					
					<tr>
						<th>Event colour:</th>
						<td>
							<input type="text" name="ke_setting_event_colour" class="fl textbox long" value="<?php print $ke_setting_event_colour; ?>" />
							<div class="fl colorSelector" id="colorSelector"><div style="background-color: #0000ff"></div></div>
						</td>
					</tr>
					
					<tr>
						<th>Event colour 2 (hover/selected):</th>
						<td>
							<input type="text" name="ke_setting_event_colour_hover" class="fl textbox long" value="<?php print $ke_setting_event_colour_hover; ?>" />
							<div class="fl colorSelector" id="colorSelectorHover"><div style="background-color: #0000ff"></div></div>
						</td>
					</tr>
					
					<!--<tr>
						<th>Admin email:</th>
						<td>
							<input type="text" name="setting_admin_email" class="textbox long" value="<?php //print $setting_admin_email; ?>"/>
						</td>
					</tr>
					
					<tr>
						<th>Notify?:</th>
						<td>
							<input type="checkbox" name="setting_admin_notify" value="1" <?php //print ($setting_admin_notify)?("checked"):(""); ?>/>
						</td>
					</tr>
					-->
					
				</table>
				
				<input type="hidden" name="submit" value="1" />
				<br class="clear"/><br/>
				
				<div class="float-right">
					<input type="button" class="button submit back" value="Back" /> <input type="submit" class="button submit" value="Submit" />
				</div>
			</form>
			<?php
			break;
		
	}
	?>
</div>