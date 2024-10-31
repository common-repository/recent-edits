<?php
/*
Plugin Name: Recent Edits
Plugin URI: http://i3dthemes.net/
Description: The Recent Edits plugin is simple plugin that shows the most recent Pages or Posts that you haved edited, from the Pages or Posts menu.
Version: 1.0
Author: i3dTHEMES
Author URI: http://i3dthemes.net/
*/
/*  Copyright 2014  i3dTHEMES

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}
define( 'RECENTEDITS_VERSION', '1.6.2' );
define( 'RECENTEDITS_RELEASE_DATE', date_i18n( 'F j, Y', '1407877048' ) );
define( 'RECENTEDITS_DIR', plugin_dir_path( __FILE__ ) );
define( 'RECENTEDITS_URL', plugin_dir_url( __FILE__ ) );


if (!class_exists("RecentEdits")) :

class RecentEdits {
	var $settings, $options_page;
	
	function __construct() {	

		add_action('init', array($this,'init') );
		
	//	register_activation_hook( __FILE__, array($this,'activate') );
	//	register_deactivation_hook( __FILE__, array($this,'deactivate') );
	}

	function init() {
		load_plugin_textdomain( 'recent_edits', RECENTEDITS_DIR . 'lang', 
							   basename( dirname( __FILE__ ) ) . '/lang' );
		
		add_action('admin_menu', array($this,'update_administrative_menu'));
		add_action('edit_page_form', array($this,'i3d_add_recent_page_tracking'));


		
	}
	function update_administrative_menu() {
		$recentPages = get_option("i3d_recent_pages");

		if (is_array($recentPages)) {
			remove_submenu_page("edit.php?post_type=page", "post-new.php?post_type=page");
			foreach ($recentPages as $pageID) {
				$pageName = get_the_title($pageID);
				if ($pageName != "" && $pageName != "Auto Draft") {
			  		add_pages_page('', "&#8627; ".$pageName."", 'edit_pages', 'post.php?post='.$pageID."&action=edit");
				}
			}
			add_pages_page(__("Add New", get_template()), __("Add New", get_template()), 'edit_pages', "post-new.php?post_type=page");

		}
	}
	
	function i3d_add_recent_page_tracking($post) {
		$postID = $post->ID;
		$recentPages = get_option("i3d_recent_pages");
		
		if ($post->post_status == "auto-draft") {
		  return;	
		}
		if (!is_array($recentPages)) {
			$recentPages = array();
		}
		
		// if it exists already in the recent pages list, then remove it
		if (in_array($postID, $recentPages)) {
			$keys = array_flip($recentPages);
			$position = $keys["$postID"];
			unset($recentPages[$position]);
		}
		
		
		// add it to the top of the list
		array_unshift($recentPages, $postID);
	
		// reduce the size of the list to just 5
		$recentPages = array_slice($recentPages, 0, 5);
		
		update_option("i3d_recent_pages", $recentPages);
	}
	


} // end class
endif;

// Initialize our plugin object.
global $recent_edits;
if (class_exists("RecentEdits") && !$recent_edits) {
    $recent_edits = new RecentEdits();	
}	
?>