<?php
/*
Plugin Name: The 404er 
Plugin URI: http://sidecar.tv/the-404er/
Description: OMG, yes really, 404 every last page on your blog
Version: 1.1.3
Author: sidecar.tv 
Author URI: http://sidecar.tv/
License: GPL
Side Note: I had some fun with variable and function names.  In the end, I regret this.  Code can be funny, but it should be legible above all else.  The comedic value gained is at the expense of it making this easy to follow. 
*/

add_filter('wp_headers','say404washere');
add_filter('status_header','sendthe404tobattle');
add_action('admin_menu', 'tell404howtofight');

/**
 * take the header, toss it out, overwrite every
 * header with a 404
 **/
function sendthe404tobattle($headers){
	if (get_option('enable') == '1'){
		$headers = 'HTTP/1.0 404 Not Found'; 
		return $headers;
	}
}

/**
 * in case this plugin is installed and some can 
 * not figure out why every page is a 404, this should
 * help them figure it out
 **/
function say404washere($headers){
	if (get_option('enable') == '1'){
        $headers['The404er'] = 'This blog uses The 404er WordPress plugin to 404 every page.';
        return $headers;
	}
}

function tell404howtofight() {
	add_options_page('the404battleplan', 'The 404', 'manage_options', 'the404-Owns-this-page-OK', 'the404battleplan');
	
	//call register settings function
	add_action( 'admin_init', 'ammo_cache' );
}


function ammo_cache() {
	//register our settings
	register_setting( 'the404_ammo', 'enable' );
}


function the404battleplan() {

	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	
	$pageHTML =  '<div  class="wrap">';
	
	// init and check stored value
	$yesHTML = $noHTML = '';
	if (get_option('enable') == '' || get_option('enable') == '0' ){
		$noHTML = ' checked="checked" ';
	} else {
		$yesHTML = ' checked="checked" ';
		$pageHTML .= '<div id="the404-warning" class="updated fade"><p><strong>The 404er is 404ing as we speak!</strong> To stop your site from 404ing, disable it below..</p></div>';
	}
	
	$pageHTML .=  '
	
		<div id="icon-options-general" class="icon32"><br></div>
		<h2>The 404er</h2>
		<p>The 404er plugin will send a 404 for every page public page
			on your blog.  You would do this because you\'re a privacy nut,
			you\'re a crazy geek like me, or some other far fetched, 
			waaaaay south of normal reason.</p>
			
		<form method="post" action="options.php">
		<table class="form-table">
		<tr valign="top">
		<th scope="row">Enable: </th>
		<td>
		
		<input type="radio" name="enable" value="1" '.$yesHTML.'/>Yes
		<input type="radio" name="enable" value="0" '.$noHTML.' />No
		
		</td>
		</tr>
		</table>
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="page_options" value="enable" />
		<p class="submit">';
	
	$pageHTML .= wp_nonce_field('update-options'); 
	$pageHTML .= settings_fields( 'the404_ammo' ); 
	$pageHTML .= '<input type="submit" class="button-primary" value="'. __('Save Changes') .'" />
		</p>
		</form>
		
		</div>';
	

	print $pageHTML;

}

?>
