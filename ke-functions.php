<?php
if(!function_exists("js_redirect"))
{
	function js_redirect($redirect_url)
	{
		?>
		<script type="text/javascript">location.href="<?php print $redirect_url; ?>";</script>
		<?php
	}
}

if(!function_exists("friendly_datetime"))
{
	function friendly_datetime($datetime, $format = "")
	{
		$timestamp = strtotime($datetime);
		
		if(empty($format))
		{
			$today = mktime(0, 0, 0, date("m") , date("d"), date("Y"));
			$tomorrow  = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
			$yesterday  = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));
			
			if($timestamp >= $today && $timestamp < $tomorrow)
			{
				$format = '\T\o\d\a\y, g.ia';
			}
			elseif($timestamp >= $yesterday && $timestamp < $today)
			{
				$format = '\Y\e\s\t\e\r\d\a\y, g.ia';
			}
			else
			{
				$format = 'D d-M-y, g.ia';
			}
		}
		return date($format , $timestamp);
	}
}


if(!function_exists("db_get_rows"))
{
	function db_get_rows($qry)
	{
		global $wpdb;
		return $wpdb->get_results($qry, ARRAY_A);
	}
}
	
if(!function_exists("db_query"))
{
	function db_query($qry)
	{
		return mysql_query($qry);
	}
}

if(!function_exists("keyvaluepair"))
{
	function keyvaluepair($data)
	{
		$output = "";
		foreach($data as $key => $value)
		{
			$output .= "&" . $key . "=". urlencode($value);
		}
		$output = substr($output,1);
		return $output;
	}
}

if(!function_exists("unkeyvaluepair"))
{
	function unkeyvaluepair($string)
	{
		$array = array();
		$pairs = explode("&",$string);
		foreach($pairs as $pair)
		{
			list($key,$value) 	= explode("=",$pair,2);
			$array[$key] 		= urldecode($value);
		}
		return $array;
	}
}