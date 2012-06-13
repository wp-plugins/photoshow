<?php 
/*  
Plugin Name: Photoshow
Plugin URI: http://wordpress.dwbooster.com/galleries/photoshow-advanced
Version: 1.0.1
Author: <a href="http://www.codepeople.net">CodePeople</a>
Description: Photoshow allows to insert images in Wordrpess posts directly from Flickr, without having to pre-select them. To get started: 1) Click the "Activate" link to the left of this description, 2) Sign up for an <a href="http://www.flickr.com/services/api/keys/apply/" target="_blank" >Flickr API key</a>, and 3) Go to your <a href="options-general.php?page=photoshow.php">Photoshow configuration</a> page, and save your API key. To insert the Flickr photos in posts, you must to press the Flickr image over the editor.
*/

include "php/photoshow.clss.php";

if(class_exists("CodePeople\Photoshow")){
	$photoshow_obj = new CodePeople\Photoshow();
	
	//Initialize the admin panel 
	if (!function_exists("CodePeoplePhotoshow_ap")) { 
		function CodePeoplePhotoshow_ap() { 
			global $photoshow_obj; 
			if (!isset($photoshow_obj)) { 
				return; 
			} 
			if (function_exists('add_options_page')) { 
				add_options_page('Photoshow', 'Photoshow', 9, basename(__FILE__), array(&$photoshow_obj, 'printAdminPage')); 
			} 
		}    
	}
	
	if(isset($photoshow_obj)){
		// Plugin deactivation
		register_deactivation_hook(__FILE__, array(&$photoshow_adv_obj, 'deactivePlugin'));
		
		// Set Actions
		add_action('admin_enqueue_scripts', array(&$photoshow_obj, 'adminScripts'), 1);
		add_action('media_buttons', array(&$photoshow_obj, 'setPhotoshowButton'), 100);
		add_action('wp_head', array(&$photoshow_obj, 'loadPhotoshowResources'), 0);
		add_action('plugins_loaded', array(&$photoshow_obj, 'init'));
		add_action('admin_menu', 'CodePeoplePhotoshow_ap');
		
		// Set Filters
		add_filter('mce_css', array(&$photoshow_obj, 'addMCEStyle'));
		add_filter('tiny_mce_before_init', array(&$photoshow_obj, 'allowDiv'));
		$plugin = plugin_basename(__FILE__);
		add_filter("plugin_action_links_".$plugin, array(&$photoshow_obj, 'settingsLink'));
	}
}
?>