<?php
/*
Plugin Name: Masala
Plugin URI: http://avatari.net/public/wordpress/masala/
Description: Searches content of known format uploads and adds to metadata field
Version: 0.1
Author: Alex Nano
Author URI: http://about.me/alexnano
License: GPL2
*/

/*  Copyright 2063 Alex Nano  (email : nanodust@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// primary wordpress action triggers

add_action("add_attachment","setContentMetadata");
add_action("delete_attachment","delContentMetadata");

/// helper functions

function py_slice($input, $slice) {
    $arg = explode(':', $slice);
    $start = intval($arg[0]);
    if ($start < 0) {
        $start += strlen($input);
    }
    if (count($arg) === 1) {
        return substr($input, $start, 1);
    }
    if (trim($arg[1]) === '') {
        return substr($input, $start);
    }
    $end = intval($arg[1]);
    if ($end < 0) {
        $end += strlen($input);
    }
    return substr($input, $start, $end - $start);
    }


// log function - from http://fuelyourcoding.com/simple-debugging-with-wordpress/

if(!function_exists('_log')){
  function _log( $message ) {
   if( WP_DEBUG === true ){
      if( is_array( $message ) || is_object( $message ) ){
        error_log( print_r( $message, true ) );
      } else {
        error_log( $message );
      }
    }
  }
}


function setContentMetadata($post_id){

	_log('starting to analyze attachment for post # '.$post_id);

	global $wpdb;
	// put these in config for others... meanwhile here they are
	$java = "/usr/bin/java";
	$tika = "/usr/local/bin/tika-app-1.2.jar";
	$wp_base = "/var/www/html-ndn";
	$allowed = array("pdf","doc");
	
	// get attachment URL from postID
	
	$row = $wpdb->get_row('select * from wp_ndn_posts where id like '.$post_id);
	
	_log('attachment filename is '.$row->guid);
	
	$url = $row->guid;

	// make sure it's a PDF or DOC. 
	// Apache Tika can do more, but that's all we're concerned with right now.
		
	$ext = pathinfo($url, PATHINFO_EXTENSION);
	_log('attachment extention is  '.$ext);
	if(!in_array($ext,$allowed)) return true;
	
	
	// it's an allowed extension, let's continue processing... 
	
	// get absolute path from url
	
	$middle = strpos($url, "/wp-content/");
	$filename = py_slice($url,$middle.":");
	$absPath = $wp_base.$filename;
	$command = $java." -jar ".$tika." -t ".$absPath;
		
	
	_log('executing tika '.$command);
	
	// run through tika - thanks apache !! http://tika.apache.org/
	
	// this could take a while, so set execution time to nearly a minute... 
	// but note original value so we can reset it after
	
	$og = ini_get('max_execution_time'); 
	set_time_limit(45);
	
	// use proc_open as output is multi-line, exec() returns too soon
	
	$descriptorspec = array(
	   0 => array("pipe", "r"),  
	   1 => array("pipe", "w"), 
	   2 => array("file", "/tmp/error-output.txt", "a")
	);
	$process = proc_open($command, $descriptorspec, $pipes);
	$fileContents = "";
 	while (!feof($pipes[1])) {
    	 $fileContents.=fgets($pipes[1], 1024);
   	}
   	fclose($pipes[1]);
   	
   	// should really do some error handling here if it's not zero.
   	$return_value = proc_close($process);
	
	$tikaResult = $fileContents;

	// set execution time back to what it was previously
	set_time_limit($og);
	
	_log('tika said: '.$tikaResult." whee!!!");

	// set custom metadata
	add_post_meta( $post_id, "tika-content", $tikaResult);
	
	// set official metadata - didn't quite work
	//add_post_meta( $post_id, "_wp_attachment_metadata", "data" );
 
}


function delContentMetadata($post_id){

	global $wpdb;
	
	_log('deleting content metadata for '.$post_id);
	
	#delete only the metadata that we've written... leave the rest that WP seems to generate
	$result = $wpdb->query('DELETE FROM `wp_ndn_postmeta` WHERE `meta_key` like "tika-content" AND `post_id` like '.$post_id);
	
	_log('delete worked ?'.$result);

}


// options menu

add_action( 'admin_menu', 'my_plugin_menu' );

// Add a new submenu under Settings:
function my_plugin_menu() {
 add_options_page(__('Masala Settings','menu-test'), __('Masala Settings','menu-test'), 'manage_options', 'masla-settings', 'mt_settings_page');

}

// mt_settings_page() displays the page content for the Test settings submenu
function mt_settings_page() {
    echo "<h2>" . __( 'Masla Settings', 'menu-test' ) . "</h2>";
    echo "I did not yet add settings to official wordpress settings API. <br>";
    echo "however, it's still easy to edit - just do so manually in the masala.php.<p> ";
    echo "see <a href='http://avatari.net/public/wordpress/masala/' target='_blank'>plugin home page / readme </a>for instructions... meanwhile,";
    echo "the values you will want or need to edit for it to work: <p>";
    echo '	$java = /usr/bin/java<br>
	$tika = /usr/local/bin/tika-app-1.2.jar<br>
	$wp_base = /var/www/html-ndn<br>';
}


?>
