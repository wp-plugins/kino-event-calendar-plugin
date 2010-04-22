<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", "on");
$page_identifier = "events";
$page_url = "/wp-admin/admin.php?page=kino-events/admin/events.php";
?>
<div class="wrap">
	<h2>Events</h2>
	<?php
	extract($_GET);
	extract($_POST);
	switch($action)
	{
		case "delete":
			for($i=0; $i<count($itemID); $i++)
			{
				$query = "DELETE FROM ".$table_prefix."ke_events WHERE event_id = '".$itemID[$i]."' LIMIT 1";
				db_query($query);
			}
			$redirect = $page_url;
			js_redirect($redirect);
			break;
			
		case "add":
		case "edit":
			
			if(isset($_POST['submit']))
			{	
				if(count($errors) == 0)
				{
					$event_start_date 	= (!preg_match("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/", $event_start_date))?(" NULL "):("'".mysql_real_escape_string(preg_replace("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/si", "$3-$2-$1", $event_start_date))."'");
					$event_end_date 	= (!preg_match("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/", $event_end_date))?(" NULL "):("'".mysql_real_escape_string(preg_replace("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/si", "$3-$2-$1", $event_end_date))."'");

					$event_start_time 	= "'".mysql_real_escape_string($event_start_time_hh.":".$event_start_time_mm)."'";
					$event_end_time		= "'".mysql_real_escape_string($event_end_time_hh.":".$event_end_time_mm)."'";
					
					$event_slug = strtolower($event_title);
					$event_slug = stripslashes($event_slug);
					$event_slug = str_replace(" ", "-", $event_slug);
					$event_slug = str_replace(",", "-", $event_slug);
					$event_slug = rawurlencode($event_slug);
					
					
					// NEED TO GET THE URL FOR THE EVENTS DESTINATION PAGE
					$query = "SELECT * FROM ".$table_prefix."ke_settings 
					WHERE ".$table_prefix."ke_settings.setting_id = 1";
					$ke_settings = db_get_rows($query);
					
					$event_url = get_permalink($ke_settings[0]['setting_wp_page'])."?evt=".$event_slug;
					
					
					$query = "UPDATE ".$table_prefix."ke_events SET 
					
						feed_id = '".mysql_real_escape_string($feed_id)."', 
						event_title = '".mysql_real_escape_string($event_title)."', 
						event_slug = '".mysql_real_escape_string($event_slug)."', 
						event_location = '".mysql_real_escape_string($event_location)."', 
						event_type = '".mysql_real_escape_string($event_type)."', 
						event_start_date = ".$event_start_date.", 
						event_start_time = ".$event_start_time.", 
						event_end_date = ".$event_end_date.", 
						event_end_time = ".$event_end_time.", 
						event_url = '".mysql_real_escape_string($event_url)."', 
						event_detail = '".mysql_real_escape_string($event_detail)."', 
						event_order = '".mysql_real_escape_string($event_order)."', 
						event_status = '".mysql_real_escape_string($event_status)."', 
						event_modified = NOW() 
						
					WHERE event_id = '".$itemID."'";
					
					/*$event_start_date = str_replace("'", "", $event_start_date);
					$event_start_time = str_replace("'", "", $event_start_time);
					$event_end_date = str_replace("'", "", $event_end_date);
					$event_end_time = str_replace("'", "", $event_end_time);
					
					// Create post object
					$my_post = array();
					$my_post['post_title'] = $event_title;
					$my_post['post_content'] = $event_detail;
					$my_post['post_status'] = 'publish';
					$my_post['post_author'] = $user_ID;
					//$my_post['post_date'] = $event_start_date." ".$event_start_time;
					$my_post['post_category'] = array(get_option("ke_setting_wp_category"));
					
					// Insert the post into the database
					$post_id = wp_insert_post( $my_post );
					
					update_post_meta($post_id, "ke_location", $event_location);
					update_post_meta($post_id, "ke_start_date", $event_start_date);
					update_post_meta($post_id, "ke_start_time", $event_start_time);
					update_post_meta($post_id, "ke_end_date", $event_end_date);
					update_post_meta($post_id, "ke_end_time", $event_end_time);
					*/
					
					if(db_query($query))
					{
						$redirect = $page_url;
						js_redirect($redirect);
						exit;
					}
					print mysql_error();
				}
			}
			else
			{
				if(!isset($_GET['itemID']))
				{
					$query = "INSERT INTO ".$table_prefix."ke_events (event_added, event_modified) VALUES (NOW(), NOW())";
					db_query($query);
					$itemID = mysql_insert_id();
					
					$redirect = $page_url."&action=".$_GET['action']."&itemID=".$itemID;
					js_redirect($redirect);
					
					exit;
				}
				
				$query = "SELECT * FROM ".$table_prefix."ke_events WHERE ".$table_prefix."ke_events.event_id = '".$_GET['itemID']."' LIMIT 1";
				$item = db_get_rows($query);
				$item[0] = array_map(stripslashes, $item[0]);
				
				extract($item[0]);
				
				list($event_start_time_hh, $event_start_time_mm, $event_start_time_ss) = explode(":", $event_start_time);
				list($event_end_time_hh, $event_end_time_mm, $event_end_time_ss) = explode(":", $event_end_time);
			}
			?>
			
			<?php
			/********************
			* ADDING AND EDITING 
			*********************/
			?>
			<form id="feeds-form" method="post" action="<?php print $page_url."&action=".$_GET['action']."&itemID=".$_GET['itemID']; ?>">
				<input type="hidden" name="event_id" class="textbox long" value="<?php print $note_id; ?>"/>
				<?php
				if(!empty($event_title))
				{
					?>
					<br class="clear" />
					<h3 class="fl">Editing Event: <?php print $event_title; ?></h3>
					<?php
				}
				else
				{
					?>
					<br class="clear" />
					<h3 class="fl">Adding Event</h3>
					<?php
				}
				?>
				<div class="float-right">
					<br/><br class="clear" />
					<input type="button" class="button submit back" value="Back" /> <input type="submit" class="button submit" value="Submit" />
				</div>
				
				<script type="text/javascript">
				tinyMCE.init({
					mode : "textareas",
					editor_selector : "rich-textarea",
					theme : "advanced",
					skin : "o2k7",
					skin_variant : "silver",
					plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",
				
					// Theme options
					theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,bullist,numlist,|,cut,copy,paste,pastetext,pasteword,|,outdent,indent,blockquote,|,link,unlink,anchor,cleanup,code,|,search,replace,|,undo,redo,|,hr,|,sub,sup",
					theme_advanced_buttons2 : "styleselect,formatselect,tablecontrols,image",
					theme_advanced_buttons3 : "",
					theme_advanced_buttons4 : "",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					theme_advanced_resizing : true, 
					convert_urls : false, 
					relative_urls : false,
					
					valid_elements : "ul,li,ol,a[href|target],strong/b,p,hr,h3,img[src|width|height|alt|border|title|style|class|align],table[*],tbody[*],td[*],th[*],tr[*],thead[*],tfoot[*]",
	
					// Example content CSS (should be your site CSS)
					//content_css : "/acp/includes/modules/cms/editor.css?time=<?php print mktime(); ?>", 
					width: "100%",
					height: 300,
					
					document_base_url : "http://www.kinodev.co.uk/wp-content/plugins/kino-events-calendar-plugin/uploads/events/"

				});
				
				tinyMCE.init({
					mode : "textareas",
					editor_selector : "rich-textarea-basic",
					theme : "advanced",
					skin : "o2k7",
					skin_variant : "silver",
					plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",
				
					// Theme options
					theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,link,unlink,cleanup,code",
					theme_advanced_buttons2 : "",
					theme_advanced_buttons3 : "",
					theme_advanced_buttons4 : "",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					theme_advanced_resizing : true, 
					convert_urls : false, 
					relative_urls : false,
					convert_newlines_to_brs : true,
					valid_elements : "-strong/b,br",
					verify_html : true,
					cleanup : true,
					force_br_newlines : true,
       			 	forced_root_block : '', // Needed for 3.x
	
	
					// Example content CSS (should be your site CSS)
					// content_css : "/acp/includes/modules/cms/editor.css?time=<?php print mktime(); ?>", 
					width: "100%",
					height: 150,
					
					document_base_url : "http://www.kinodev.co.uk/wp-content/plugins/kino-events-calendar-plugin/uploads/events/"

				});
				</script>
				<table class="widefat layout">
					
					<tr class="hide">
						<th>Type:</th>
						<td>
							<select name="event_type" class="dropdown long">
								<option value="nano" <?php print ($event_type == "nano")?("selected"):(""); ?>>NanoCentral</option>
								<option value="external" <?php print ($event_type == "external")?("selected"):(""); ?>>External</option>
							</select>
						</td>
					</tr>
					
					<!--
					<tr>
						<th>Feed ID:</th>
						<td>
							<input type="text" name="feed_id" class="textbox long" value="<?php print $feed_id; ?>"/>
						</td>
					</tr>
					-->
					
					<tr>
						<th>Title:</th>
						<td>
							<input type="text" name="event_title" class="textbox long" value="<?php print $event_title; ?>"/>
						</td>
					</tr>
					
					<tr>
						<th>Location:</th>
						<td>
							<input type="text" name="event_location" class="textbox long" value="<?php print $event_location; ?>"/>
						</td>
					</tr>
					
					<tr>
						<th>Start date:</th>
						<td>
							<input type="text" name="event_start_date" class="date textbox long" value="<?php print (!empty($event_start_date))?(date("d/m/Y", strtotime($event_start_date))):(""); ?>"/>
						</td>
					</tr>
					
					<tr>
						<th>Start time:</th>
						<td>
							<select name="event_start_time_hh">
								<option value="">hh</option>
								<?php
								for($i=0; $i<24; $i++)
								{
									?><option value="<?php printf("%02s", $i); ?>" <?php print ($event_start_time_hh == sprintf("%02s", $i))?("selected"):(""); ?>><?php printf("%02s", $i); ?></option><?php
								}
								?>
							</select>:
							<select name="event_start_time_mm">
								<option value="">mm</option>
								<?php
								for($i=0; $i<60; $i+=5)
								{
									?><option value="<?php printf("%02s", $i); ?>" <?php print ($event_start_time_mm == sprintf("%02s", $i))?("selected"):(""); ?>><?php printf("%02s", $i); ?></option><?php
								}
								?>
							</select>
						</td>
					</tr>
					
					<tr>
						<th>End date:</th>
						<td>
							<input type="text" name="event_end_date" class="date textbox long" value="<?php print (!empty($event_end_date))?(date("d/m/Y", strtotime($event_end_date))):(""); ?>"/>
						</td>
					</tr>
					
					<tr>
						<th>End time:</th>
						<td>
							<select name="event_end_time_hh">
								<option value="">hh</option>
								<?php
								for($i=0; $i<24; $i++)
								{
									?><option value="<?php printf("%02s", $i); ?>" <?php print ($event_end_time_hh == sprintf("%02s", $i))?("selected"):(""); ?>><?php printf("%02s", $i); ?></option><?php
								}
								?>
							</select>:
							<select name="event_end_time_mm">
								<option value="">mm</option>
								<?php
								for($i=0; $i<60; $i+=5)
								{
									?><option value="<?php printf("%02s", $i); ?>" <?php print ($event_end_time_mm == sprintf("%02s", $i))?("selected"):(""); ?>><?php printf("%02s", $i); ?></option><?php
								}
								?>
							</select>
						</td>
					</tr>
					
					<tr>
						<th>Detail:</th>
						<td>
							<textarea name="event_detail" class="rich-textarea-basic long"><?php print $event_detail; ?></textarea>
						</td>
					</tr>
					
					<!--
					<tr>
						<th>URL:</th>
						<td>
							<input type="text" name="event_url" class="textbox long" value="<?php print $event_url; ?>"/>
						</td>
					</tr>
					-->
					
					<!--
					<tr>
						<th>Order:</th>
						<td>
							<input type="text" name="event_order" class="textbox long" value="<?php print $event_order; ?>"/>
						</td>
					</tr>
					-->
					<tr>
						<th>Active?:</th>
						<td>
							<input type="checkbox" name="event_status" value="1" <?php print ($event_status)?("checked"):(""); ?>/>
						</td>
					</tr>
					
				</table>
				
				<input type="hidden" name="event_url" class="textbox long" value="<?php print $event_url; ?>"/>
				<input type="hidden" name="submit" value="1" />
				<br class="clear"/><br/>
				
				<div class="float-right">
					<input type="button" class="button submit back" value="Back" /> <input type="submit" class="button submit" value="Submit" />
				</div>
			</form>
			<?php
			break;
		
		default: 
			
			?>
			
			<?php
			// CLEANUP
			$query = "DELETE FROM ".$table_prefix."ke_events WHERE event_added = event_modified";
			db_query($query);
			$query = "ALTER TABLE ".$table_prefix."ke_events AUTO_INCREMENT = 1";
			db_query($query);
			
			/****************************************/
			if(isset($_GET['keywords']) && !empty($_GET['keywords']))
			{
				$where .= (empty($where))?(" event_title LIKE '%".$_GET['keywords']."%' 
											OR event_url LIKE '%".$_GET['keywords']."%' 
											
											"):(" AND (event_title LIKE '%".$_GET['keywords']."%' 
											OR event_url LIKE '%".$_GET['keywords']."%' 
											
											) ");
			}
			
			// FEED ID
			if(isset($_GET['feed_id']) && !empty($_GET['feed_id']))
			{
				$where .= (empty($where))?(" feed_id = '".$_GET['feed_id']."' "):(" AND feed_id = '".$_GET['feed_id']."' ");
				$_SESSION['feed_id'] = $_GET['feed_id'];
				
			}
			elseif(isset($_GET['feed_id']) && empty($_GET['feed_id']))
			{
				unset($_SESSION['feed_id']);
			}
			elseif(isset($_SESSION['feed_id']) && !empty($_SESSION['feed_id']))
			{
				$where .= (empty($where))?(" feed_id = '".$_SESSION['feed_id']."' "):(" AND feed_id = '".$_SESSION['feed_id']."' ");
			}
			
			$where = (empty($where))?(""):(" WHERE ".$where);
			
			$joins = " ";
			$orderby = " ";
	
			$query = "SELECT * FROM ".$table_prefix."ke_events ".$joins.$where.$orderby;
			//print $query;
			$items = db_get_rows($query);
			
			/*****************************************************
			* PAGINATION - SEE main.inc.php FOR SPECIAL 'FOR' LOOP
			******************************************************/
			$num_results = count($items);
			$lmt = 15;
			$page = (!isset($_GET['pg']) || empty($_GET['pg']))?(1):($_GET['pg']);
			$num_pages = ceil($num_results / $lmt);
			
			// CREATE PAGE LINKS
			$pagination = "";
			for($i=0; $i<$num_pages; $i++)
			{
				if(($i+1) == $page)
				{
					$pagination .= " ".($i+1);
				}
				else
				{
					$pagination .= " <a href='".$page_url."&keywords=".$_GET['keywords']."&search=".$_GET['search']."&pg=".($i+1)."'>".($i+1)."</a>";
				}
			}
			
			$pagination = substr($pagination, 1);
			
			$prev_page = "";
			$prev_page = ($page>1)?("&nbsp;<a href='".$page_url."&keywords=".$_GET['keywords']."&search=".$_GET['search']."&pg=".($page-1)."'>&lt; prev</a>"):("");
			$next_page = ($page < $num_pages)?("&nbsp;<a href='".$page_url."&keywords=".$_GET['keywords']."&search=".$_GET['search']."&pg=".($page+1)."'>next &gt;</a>"):("");
			
			$pagination .= $prev_page.$next_page;
			/* END ************************************************/
				
			
			?>
			<div>
				<form method="get" action="<?php print $page_url; ?>" class="fr">
					<input type="hidden" name="page" value="<?php print $_GET['page']; ?>" />
					
					<?php
					/*<div class="row">
						<label class="label">Feeds:</label>
						<span class="field fr">
							<select name="feed_id">
								<option value="">All feeds</option>
								<?php
								$feeds = ke_get_feeds();
								foreach($feeds as $x)
								{
									?><option value="<?php print $x['feed_id']; ?>" <?php print ($feed_id == $x['feed_id'])?("selected"):(""); ?>><?php print $x['feed_title']; ?></option><?php
								}
								?>
							</select>
						</span>
						<br class="clear" />
					</div>
					*/
					?>
					
					<div class="row">
						<label class="label">Keywords: </label>
						<span class="field fr">
							<input type="text" class="textbox" name="keywords" value="<?php print $_GET['keywords']; ?>" />
						</span>
						<br class="clear" />
					</div>
					
					<div class="row">
						<span class="field fr">
							<input type="submit" class="button" value="Search" />
						</span>
						<br class="clear" />
					</div>
					
					
					<input type="hidden" name="section" value="<?php print $_GET['section']; ?>" />
					<input type="hidden" name="search" value="1" />
				</form>
				<br class="clear"/><br/>
				<div class="fr">
					<input type="button" class="button <?php print $page_identifier; ?>-delete" value="Delete"/> <input type="submit" class="button <?php print $page_identifier; ?>-add" value="Add" />
				</div>
				<?php
				if($pagination)
				{
					?>
					<div class="fl">
						<p>Pages <?php print $pagination; ?></p>
					</div>
					<?php
				}
				?>
				<br class="clear"/>
				<table class="widefat tabular-data <?php print $page_identifier; ?>-table">
			
					<thead>
						<tr>
							<th>Title</th>
							<!--<th class="ac">Type</th>-->
							<th class="ac">Location</th>
							<th class="ac">Start date</th>
							<th class="ac">Start time</th>
							<th class="ac">End date</th>
							<th class="ac">End time</th>
							<th class="ac">Detail</th>
							<!--<th class="ac">URL</th>-->
							<th class="ac">Modified</th>
							<th class="ac">Publish date</th>
							<th class="ac">Active?</th>
							<th class="ar"></th>
						</tr>
					</thead>
					
					<tbody>
					
						<?php
						$num_columns = 12;
						
						if($num_results)
						{
							for($i=($page-1)*$lmt; $i<($page*$lmt); $i++)
							{
								if($i<$num_results)
								{
									$items[$i] = array_map(stripslashes, $items[$i]);
									extract($items[$i]);
									?>
									<tr id="<?php print $event_id; ?>" class="<?php print ($i % 2)?("alternate"):(""); ?>">
										<td><?php print $event_title; ?></td>
										<!--<td class="ac"><?php print $event_type; ?></td>-->
										<td class="ac"><?php print $event_location; ?></td>
										<td class="ac"><?php print $event_start_date; ?></td>
										<td class="ac"><?php print $event_start_time; ?></td>
										<td class="ac"><?php print $event_end_date; ?></td>
										<td class="ac"><?php print $event_end_time; ?></td>
										<td class="ac"><?php print ($event_detail)?(substr(strip_tags($event_detail), 0, 50)):("-"); ?></td>
										<!--<td class="ac"><?php print $event_url; ?></td>-->
										<td class="ac"><?php print friendly_datetime($event_modified); ?></td>
										<td class="ac"><?php print friendly_datetime($event_added); ?></td>
										<td class="ac"><?php print ($event_status)?("Yes"):("-"); ?></td>
										<td class="ar option"><input type="checkbox" value="<?php print $event_id; ?>" class="checkbox delete" /></td>
									</tr>
									<?php
								}
							}
						}
						else
						{
							?>
							<tr class="option">
								<td colspan="<?php print $num_columns; ?>" class="ac">No results found</td>
							</tr>
							<?php
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="<?php print $num_columns; ?>">&nbsp;</th>
						</tr>
					</tfoot>
					
				</table>
				<br class="clear"/>
				<div class="fr">
					<input type="button" class="button <?php print $page_identifier; ?>-delete" value="Delete"/> <input type="submit" class="button <?php print $page_identifier; ?>-add" value="Add" />
				</div>
				<?php
				if($pagination)
				{
					?>
					<div class="fl">
						<p>Pages <?php print $pagination; ?></p>
					</div>
					<?php
				}
				?>
				<br class="clear"/>
			</div>
			<?php
			break;
	}
	?>
</div>