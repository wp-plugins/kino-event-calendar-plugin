<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", "on");
$page_identifier = "feeds";
$page_url = "/wp-admin/admin.php?page=kino-event-calendar-plugin/admin/feeds.php";
?>
<div class="wrap">
	<h2>Feeds</h2>
	<?php
	extract($_GET);
	extract($_POST);
	switch($action)
	{
		case "delete":
			for($i=0; $i<count($itemID); $i++)
			{
				$query = "DELETE FROM ".$table_prefix."ke_feeds WHERE feed_id = '".$itemID[$i]."' LIMIT 1";
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
					$query = "UPDATE ".$table_prefix."ke_feeds SET 
					
						feed_title = '".mysql_real_escape_string($feed_title)."', 
						feed_url = '".mysql_real_escape_string($feed_url)."', 
						feed_detail = '".mysql_real_escape_string($feed_detail)."', 
						feed_order = '".mysql_real_escape_string($feed_order)."', 
						feed_status = '".mysql_real_escape_string($feed_status)."', 
						feed_modified = NOW() 
						
					WHERE feed_id = '".$itemID."'";
					
					
					if(db_query($query))
					{
						
						// IF IMPORT THEN DO IT
						ke_add_events($feed_url, $itemID);
						
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
					$query = "INSERT INTO ".$table_prefix."ke_feeds (feed_added, feed_modified) VALUES (NOW(), NOW())";
					db_query($query);
					$itemID = mysql_insert_id();
					
					$redirect = $page_url."&action=".$_GET['action']."&itemID=".$itemID;
					js_redirect($redirect);
					
					exit;
				}
				
				$query = "SELECT * FROM ".$table_prefix."ke_feeds WHERE ".$table_prefix."ke_feeds.feed_id = '".$_GET['itemID']."' LIMIT 1";
				$item = db_get_rows($query);
				$item[0] = array_map(stripslashes, $item[0]);
				extract($item[0]);
			}
			?>
			
			<?php
			/********************
			* ADDING AND EDITING 
			*********************/
			?>
			<form id="feeds-form" method="post" action="<?php print $page_url."&action=".$_GET['action']."&itemID=".$_GET['itemID']; ?>">
				<input type="hidden" name="feed_id" class="textbox long" value="<?php print $note_id; ?>"/>
				<?php
				if(!empty($feed_title))
				{
					?>
					<br class="clear" />
					<h3 class="fl">Editing Feed: <?php print $feed_title; ?></h3>
					<?php
				}
				else
				{
					?>
					<br class="clear" />
					<h3 class="fl">Adding Feed</h3>
					<?php
				}
				?>
				<div class="float-right">
					<br/><br class="clear" />
					<input type="button" class="button submit back" value="Back" /> <input type="submit" class="button submit" value="Submit" />
				</div>
				
				<table class="widefat layout">
					
					<tr>
						<th>Title:</th>
						<td>
							<input type="text" name="feed_title" class="textbox long" value="<?php print $feed_title; ?>"/>
						</td>
					</tr>
					
					<tr>
						<th>Detail:</th>
						<td>
							<textarea name="feed_detail" class="textbox long"><?php print $feed_detail; ?></textarea>
						</td>
					</tr>
					
					<tr>
						<th>URL:</th>
						<td>
							<input type="text" name="feed_url" class="textbox long" value="<?php print $feed_url; ?>"/> <a class="show" id="feed_url_view_link" href="#">view feed</a>
						</td>
					</tr>
					
					<tr>
						<th>Order:</th>
						<td>
							<input type="text" name="feed_order" class="textbox long" value="<?php print $feed_order; ?>"/>
						</td>
					</tr>
					
					<tr>
						<th>Active?:</th>
						<td>
							<input type="checkbox" name="feed_status" value="1" <?php print ($feed_status)?("checked"):(""); ?>/>
						</td>
					</tr>
					
					<tr>
						<th>Import now?:</th>
						<td>
							<input type="checkbox" name="feed_import" value="1" <?php print ($feed_import)?("checked"):(""); ?>/>
						</td>
					</tr>
					
				</table>
				
				<input type="hidden" name="submit" value="1" />
				<br class="clear"/><br/>
				
				<div class="float-right">
					<input type="button" class="button submit back" value="Back" /> <input type="submit" class="button submit" value="Submit" />
				</div>
			</form>
			
			<div id="feed_view_box">
			
			</div>
			<?php
			break;
		
		default: 
			
			?>
			
			<?php
			// CLEANUP
			$query = "DELETE FROM ".$table_prefix."ke_feeds WHERE feed_added = feed_modified";
			db_query($query);
			$query = "ALTER TABLE ".$table_prefix."ke_feeds AUTO_INCREMENT = 1";
			db_query($query);
			
			/****************************************/
			if(isset($_GET['keywords']) && !empty($_GET['keywords']))
			{
				$where .= (empty($where))?(" feed_title LIKE '%".$_GET['keywords']."%' 
											OR feed_url LIKE '%".$_GET['keywords']."%' 
											
											"):(" AND (feed_title LIKE '%".$_GET['keywords']."%' 
											OR feed_url LIKE '%".$_GET['keywords']."%' 
											
											) ");
			}
			
			$where = (empty($where))?(""):(" WHERE ".$where);
			
			$joins = " ";
			$orderby = " ";
	
			$query = "SELECT * FROM ".$table_prefix."ke_feeds ".$joins.$where.$orderby;
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
					
					
					<div class="row">
						<label class="label">Keywords: </label>
						<span class="field">
							<input type="text" class="textbox" name="keywords" value="<?php print $_GET['keywords']; ?>" />
						</span>
					</div>
					
					<input type="hidden" name="section" value="<?php print $_GET['section']; ?>" />
					<input type="hidden" name="search" value="1" />
				</form>
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
				<table class="widefat tabular-data <?php print $page_identifier; ?>-table">
			
					<thead>
						<tr>
							<th>Title</th>
							<th class="ac">Detail</th>
							<th class="ac">URL</th>
							<th class="ac">Modified</th>
							<th class="ac">Active?</th>
							<th class="ar"></th>
						</tr>
					</thead>
					
					<tbody>
					
						<?php
						$num_columns = 6;
						
						if($num_results)
						{
							for($i=($page-1)*$lmt; $i<($page*$lmt); $i++)
							{
								if($i<$num_results)
								{
									$items[$i] = array_map(stripslashes, $items[$i]);
									extract($items[$i]);
									?>
									<tr id="<?php print $feed_id; ?>" class="<?php print ($i % 2)?("alternate"):(""); ?>">
										<td><?php print $feed_title; ?></td>
										<td class="ac"><?php print ($feed_detail)?($feed_detail):("-"); ?></td>
										<td class="ac"><?php print $feed_url; ?></td>
										<td class="ac"><?php print friendly_datetime($feed_modified); ?></td>
										<td class="ac"><?php print ($feed_status)?("Yes"):("-"); ?></td>
										<td class="ar option"><input type="checkbox" value="<?php print $feed_id; ?>" class="checkbox delete" /></td>
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