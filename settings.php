<?php
include "plugin-options.php";

if ( $_GET['page'] == PLUGIN_SHORT_NAME."-settings")
{
	if ( 'save' == $_REQUEST['action'] )
	{
		foreach ($options as $value)
		{
			//print $_REQUEST[ $value['id'] ];
			update_option( $value['id'], $_REQUEST[ $value['id'] ] );
		}
		foreach ($options as $value)
		{
			if( isset( $_REQUEST[ $value['id'] ] ) )
			{
				update_option( $value['id'], $_REQUEST[ $value['id'] ]  );
			}
			else
			{
				delete_option( $value['id'] );
			}
		}
		$categories = get_terms("event_category", "hide_empty=0");
		foreach($categories as $x)
		{
			if(isset($_REQUEST[ "ec_cat_color_".$x->term_id ]))
			{
				update_option( "ec_cat_color_".$x->term_id, $_REQUEST[ "ec_cat_color_".$x->term_id ]  );
			}
			else
			{
				delete_option( "ec_cat_color_".$x->term_id );
			}
			
		}
		//header("Location: ".get_bloginfo("url")."/wp-admin/options-general.php?page=".PLUGIN_SHORT_NAME."-settings&saved=true");
		//exit;
	}
	else if( 'reset' == $_REQUEST['action'] )
	{
		foreach ($options as $value)
		{
			delete_option( $value['id'] );
		}
		//header("Location: ".get_bloginfo("url")."/wp-admin/options-general.php?page=".PLUGIN_SHORT_NAME."-settings&reset=true");
		//exit;
	}
}

if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.PLUGIN_LONG_NAME.' settings saved.</strong></p></div>';
if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.PLUGIN_LONG_NAME.' settings reset.</strong></p></div>';
?>

<div class="wrap" >
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" >
		<h2><?php echo PLUGIN_LONG_NAME; ?> Settings</h2>
		<div id="poststuff" class="metabox-holder">
			<p class="submit">
				<input name="save" type="submit" value="Save changes" />
				<input type="hidden" name="action" value="save" />
			</p>    
			<table class="widefat" >
				<?php
				foreach ($options as $value)
				{
					switch ( $value['type'] )
					{
						case "text" :  ?>
							<tr>
								<th scope="row"><?php echo $value['name']; ?></th>
								<td>
								<input style="width:500px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
								<br /><small><?php echo $value['desc']; ?></small>
								</td>
							</tr>
							<?php 
							break;
						
						case "color" :  ?>
							<tr>
								<th scope="row"><?php echo $value['name']; ?></th>
								<td>
								<input style="width:500px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
								<div class="fl colorSelector" id="colorSelector-<?php echo $value['id']; ?>"><div style="background-color: <?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>"></div></div>
								<br /><small><?php echo $value['desc']; ?></small>
								</td>
							</tr>
							<?php 
							break;
							
						case "textarea" :  
							?>
							<tr valign="top">
								<th scope="row"><?php echo $value['name']; ?>:</th>
								<td>
								<textarea style="width:500px;height:100px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" ><?php
								if( get_option($value['id']) != "") {
									echo stripslashes(get_option($value['id']));
								  }else{
									echo $value['std'];
								}?></textarea>
								<br /><?php echo $value['desc']; ?>
								</td>
							</tr>
							<?php 
							break;
						case "checkbox" : 
							?>
							<tr valign="top">
								<th scope="row"><?php echo $value['name']; ?></th>
								<td>
								<?php if(get_option($value['id'])){
								$checked = "checked=\"checked\"";
								  }else{
								$checked = "";
								}
								?>
								<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />
								<?php echo $value['desc']; ?>
								</td>
							</tr>
							<?php 
							break;
						case  "select": 
							?>
							<tr>
								<th scope="row"><?php echo $value['name']; ?></th>
								<td>
								<select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
								<?php foreach ($value['options'] as $option) { ?>
								<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
								<?php } ?>
								</select>
								<br /><small><?php echo $value['desc']; ?></small>
								</td>
							</tr>        
							<?php 
							break;
						case "heading" :
							?>
							<thead>
								<tr valign="top">
								<th colspan="2">
								<?php echo $value['name']; ?>
								</th>
								</tr>
							</thead>
							<?php
							break;
					}  
				}
				
				// GO THROUGH ANY CATEGORIES ADDED FOR EVENTS
				$categories = get_terms("event_category", "hide_empty=0"); 
				if(count($categories) > 0)
				{
					?>
					<thead>
						<tr valign="top">
						<th colspan="2">
						Calendar Category Options
						</th>
						</tr>
					</thead>
					<?php
						
					foreach($categories as $x)
					{
						// GET OPTION FOR THIS IF EXISTS
						//${"{PLUGIN_SHORT_NAME}_cat_{$x->term_id}_color"} = get_option({"{PLUGIN_SHORT_NAME}_cat_{$x->term_id}_color"});
						$ec_cat_color[$x->term_id] = get_option("ec_cat_color_".$x->term_id);
						?>
						<tr>
							<th scope="row">'<?php echo $x->name; ?>' color</th>
							<td>
							<input style="width:500px;" name="ec_cat_color_<?php print $x->term_id; ?>" id="ec_cat_color_<?php print $x->term_id; ?>" type="text" value="<?php print $ec_cat_color[$x->term_id]; ?>" />
							<div class="fl colorSelector" id="colorSelector-ec_cat_color_<?php print $x->term_id; ?>"><div style="background-color: <?php print $ec_cat_color[$x->term_id]; ?>"></div></div>
							<br /><small><?php //echo $value['desc']; ?></small>
							</td>
						</tr>
						<?php
					}
				}
				?>
			</table>
			<p class="submit">
				<input name="save" type="submit" value="Save changes" />
				<input type="hidden" name="action" value="save" />
			</p>        
		</div>
	</form>
</div>