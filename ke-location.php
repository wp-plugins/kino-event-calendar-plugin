<?php

/**************************************************************************************************
 * 	Originally added by Brad Brighton as part of the location independence revision
 *	photos@sentientfood.com
 *	http://www.sentientfood.com
 *
 * This file MUST be located in the plugin root directory (or other known, relative directory)
 * if it is going to be "included"
 * 
 * 
 * Examples of the paths that come out to help determine which variable/function to use when:
 * 		Assume that WordPress is installed in a directory in $DOCROOT/wordpress
 * 		Assume $DOCROOT is /var/www/html/
 * 
 * 	NOTE: There is no trailing slash on the returned paths!
 * 
 * ke_getPluginLocation():
 * ke_getPluginPath():
 * 		/var/www/html/wordpress/wp-content/plugins/<plugin_dirname>
 *
 * ke_getPluginRelativeLocation():
 * ke_getPluginURL():
 *		/wordpress/wp-content/plugins/<plugin_dirname>
 *
 * ke_getInstallBaseLocation():
 *		/var/www/html/wordpress
 *
 * ke_getMyURLDir():
 * 		
 *
 * ke_getWPURL():
 *		/wordpress
 *
 * ke_getPluginDirname():
 *		<plugin_dirname> 
 *		(a string representing the current name of the directory in which the calling file lives)
 *
 * 
 *
 * FOR SUBSEQUENT DEVELOPERS:
 * 	This code is functional, but is not necessarily reflective of the proper way in WP2.x to do
 *	many of these operations. When the plugin itself reflects best practices, these functions
 *	will likely go away (or NOPed). Alternately, they could be revamped to get the information
 *	from the assured sources and leave the calling code mostly unchanged. --bmb 29-Apr-2010
 *
 * 	A cross-platform reminder for anyone modifying this code...
 *	Since we're dealing with path modification here, it's important to remember that while
 *	PHP calls *accept* any path separated informaiton ('\' or '/') and makes it work correctly
 *	for the deployment platform, various *get* calls do not modify the underlying information,
 *	and will return the path info in the platform-native format.
 *
 *	*** This can cause issues when you're walking the path utilizing explode()! ***
 *	Most of the normal path-related calls will work and you don't need to break the path apart,
 *	but in the cases where you need to walk the tre, you need to know the separator. (Yes, you 
 *	could recursively iterate over parse_url and get what you want, but the tradeoff is that it's
 *	typically less readable for the purposes of simply walking the path.)
 *
 *	This code takes a shortcut and simply takes system-level paths and replaces '\' with
 *	'/' to normalize to posix-style paths prior to manipulation. If this code is ever expected to 
 *	work on Mac OS 9 and prior, another substitution of '/' for ':' would need to be added as well.
 *	--bmb 21-May-2010
 *
 *************************************************************************************************/

//
// !!! NOTE: This is not the correct way to do this but I'm doing it anyway for time.
// See: http://ottopress.com/2010/dont-include-wp-load-please/
//
// ASSUMED: This file will reside at the top level of the plugin dir, and neither the plugin dir nor
//	the content dir (plugins or wp-content) have been moved. This is a bootstrap issue inherent in
//	the approach currently used.
//
include dirname(__FILE__)."../../../../wp-load.php";

/*************************************************************************************************/
function ke_getPluginPath() {

	$pluginLocation = "";
	if (defined(WP_PLUGIN_DIR)) {
		// official way
		$pluginLocation = WP_PLUGIN_DIR."/".ke_getPluginDirname();
	} else {
		// older way of doing this
		$pluginLocation = ke_getInstallBaseLocation()."/wp-content/plugins/".ke_getPluginDirname();
	}
	

	return (unixize($pluginLocation));
}

/*************************************************************************************************/
function unixize($inPath) {
	// Remove backslashes from the $inPath and return the result
	return(str_replace(DIRECTORY_SEPARATOR,'/',$inPath));	// make uniform to unix path sep
}

/*************************************************************************************************/
function ke_getPluginURL() {
	$theURL = "";
	if (defined(WP_PLUGIN_URL)) {
		// official way
		$theURL = WP_PLUGIN_URL."/".ke_getPluginDirname();
	} else {
		// older way of doing this
		$theURL = ke_getWPURL()."/wp-content/plugins/".ke_getPluginDirname();
	}

	return (unixize($theURL));
}

/*************************************************************************************************/
function ke_getPluginLocation() {
	return (ke_getPluginPath());
}

/*************************************************************************************************/
function ke_getPluginRelativeLocation() {
	return (ke_getPluginURL());
}

/*************************************************************************************************/
function ke_getPluginDirpath() {
	// this functions similarly to ke_getPluginLocation but instead of knowing where things
	// are and building a proper path (a future-proof way of doing this), it makes assumptions
	// about the tree structure.
	//
	// ONLY USE THIS when the WP functions aren't available otherwise the code will be
	// unnecessarily fragile and prone to breakage on updates.

	//$ourFullPath = realpath(__FILE__);
	$ourFullPathLocal = $_SERVER['SCRIPT_FILENAME'];
	
	$ourFullPath = str_replace(DIRECTORY_SEPARATOR,'/',$ourFullPathLocal);	// make uniform to unix path sep
	$ourFullPathArray = explode("/",$ourFullPath);
	$numItems = count($ourFullPathArray);
	
	$ourIndex = $numItems-1;	// zero-based
	$pluginsFound = false;
	$pluginDirpath = "";
	while ($ourIndex >= 0 && !$pluginsFound) {
		if ($ourFullPathArray[$ourIndex] == "plugins") {
			$pluginsFound = true;

			$numElements = count($ourFullPathArray);
			
			// could use array_slice here with preserve_keys but that is PHP >= 5.0.2 only
			// so manually work up the new path
			for ($thisIndex = 0; $thisIndex <= ($ourIndex-1); $thisIndex++) {
				$theElement = $ourFullPathArray[$thisIndex];
				if (!(empty($theElement))) {
					$pluginDirpath .= "/".$theElement;
				}
			}
		} else {
			$ourIndex--;
		}
	}
		
	return (unixize($pluginDirpath));
} 

/*************************************************************************************************/
function ke_getPluginDirname() {
	// derive the plugin directory name, assuming the plugins directory is wp-standard "plugins"
	// from where we are right now
	$ourFullPathLocal = realpath(__FILE__);
	//$ourFullPath = $_SERVER['SCRIPT_FILENAME'];

	$ourFullPath = str_replace(DIRECTORY_SEPARATOR,'/',$ourFullPathLocal);	// make uniform to unix path sep
	$ourFullPathArray = explode("/",$ourFullPath, PHP_INT_MAX);
	$numItems = count($ourFullPathArray);
	
	$ourIndex = $numItems-1;
	$pluginsFound = false;
	$pluginDirname = "";
	while ($ourIndex >= 0 && !$pluginsFound) {
		if ($ourFullPathArray[$ourIndex] == "plugins") {
			$pluginsFound = true;
			$pluginDirname = $ourFullPathArray[$ourIndex+1];
		} else {
			$ourIndex--;
		}
	}

	return (unixize($pluginDirname));
}

/*************************************************************************************************/
function ke_getMyURLDir() {
	return (unixize(dirname($_SERVER['SCRIPT_NAME'])));
}

/*************************************************************************************************/
function ke_getAdminURL() {
	
	$theURL = "";
	
	// check to see if the wp-admin dir been moved -- this is only valid inside the WP environ
	if (defined('WP_ADMIN_URL')) {
		// we can use this
		$theURL = WP_ADMIN_URL;
	} else {
		// we have to assume it's in a reasonably stock location
		$theURL = ke_getInstallURL()."/wp-admin";
	}
	
	return (unixize($theURL));
}

/*************************************************************************************************/
function ke_getInstallPath() {
	$thePath = "";
	
	// are we within the WP environ where we can get direct information?
	if (defined(ABSPATH)) {
		$thePath = ABSPATH;		// yes we are -- the value is from wp-config.php
	} else {
		// check to see if wp-content has been moved per http://codex.wordpress.org/Editing_wp-config.php
		if (defined(WP_CONTENT_DIR)) {
			// we're at least partially in WP-land so use WP_SITEURL
			$thePath = $_SERVER['DOCUMENT_ROOT'].parse_url(WP_SITEURL, PHP_URL_PATH);
		} else {
			// we might still be in WP-land but no custom WP_CONTENT_DIR
			if (function_exists("get_bloginfo")) {
				$thePath = $_SERVER['DOCUMENT_ROOT'].parse_url(get_bloginfo('wpurl'), PHP_URL_PATH);
			} else {
				// we're on our own
				//!!! We shouldn't be here after the addition of wp-load to this script
			}
		}
	}
	
	return (unixize($thePath));
}

/*************************************************************************************************/
function ke_getInstallURL() {
	return (ke_getWPURL());
}

/*************************************************************************************************/
function ke_getInstallBaseLocation() {

	// Derive the local-path location of the WordPress install itself if we can
	$wpBaseLocation = "";
	if (function_exists("get_bloginfo")) {
		$theBlogWPURL = get_bloginfo('wpurl');	// WP call: http://codex.wordpress.org/Function_Reference/get_bloginfo
		$theBlogWPURLParsedArray = parse_url($theBlogWPURL);
		$wpBaseLocation = $_SERVER['DOCUMENT_ROOT'].$theBlogWPURLParsedArray["path"];	// NO trailing slash added
	} else {
		// !!! Can't assume this as WP dir might be somewhere else
		// WP calls aren't available. We have to fake it and assume certain characteristics.
		$theRawPluginDirpathLocal = ke_getPluginDirpath();
		$theRawPluginDirpath = str_replace(DIRECTORY_SEPARATOR,'/',$theRawPluginDirpathLocal);	// make uniform to unix path sep
		$theRawPathArray = explode("/", $theRawPluginDirpath);
		
		$numElements = count($theRawPathArray)-1;
		for ($thisIndex = 0; $thisIndex < $numElements; $thisIndex++) {
			$wpBaseLocation .= "/".$theRawPathArray[$thisIndex];
		}
	}

	return (unixize($wpBaseLocation));
}

/*************************************************************************************************/
function ke_getWPURL() {

	$wpURL = "";
	if (function_exists("get_bloginfo")) {
		$theBlogURL = get_bloginfo('wpurl');
		$theBlogURLParsedArray = parse_url($theBlogURL);
		$wpURL = $theBlogURLParsedArray["path"];
	} else {
		// WP calls aren't available. We have to fake it and assume certain characteristics.
		$theRawPluginDirpathLocal = ke_getPluginDirpath();
		$theRawPluginDirpath = str_replace(DIRECTORY_SEPARATOR,'/',$theRawPluginDirpathPathLocal);	// make uniform to unix path sep
		$theRawPathArray = explode("/", $theRawPluginDirpath);
		$theIndex = count($theRawPathArray)-1;	// zero-based
		$wpURL = "/".$theRawPathArray[$theIndex-1];
	}
	
	return (unixize($wpURL));
}

?>