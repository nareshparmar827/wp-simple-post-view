<?php 
/*
 * Plugin Name:		WP Simple Post View
 * Description:		Using this plugin, see how many views your posts have. [single-post-view] OR [single-post-view id="post_id"]
 * Text Domain:		wp-simple-post-view
 * Domain Path:		/languages
 * Version:			1.0
 * WordPress URI:	
 * Plugin URI:		
 * Contributors: 	dipakparmar
 * Author:			Dipak Parmar
 * Author URI:		
 * Donate Link:		
 * License:			GPL-3.0
 * License URI:		https://www.gnu.org/licenses/gpl-3.0.html
 * @copyright:		Dipak Parmar
*/

function getCurrentIPAddressForPostView() {
	
	$ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif(isset($_SERVER['HTTP_X_FORWARDED'])){
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    } elseif(isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    } elseif(isset($_SERVER['HTTP_FORWARDED'])) {
       $ipaddress = $_SERVER['HTTP_FORWARDED'];
    } elseif(isset($_SERVER['REMOTE_ADDR'])) {
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    } else {
        $ipaddress = 'UNKNOWN';
    }
    return $ipaddress;
}

add_action( 'wp_head', 'insertProcessPostviewFun');

function insertProcessPostviewFun() {
	
	global $user_ID, $post;
	
	
	if ( is_int( $post ) ) {
		$post = get_post( $post );
	}
	
	$id = (int) $post->ID;
	$currentIP = getCurrentIPAddressForPostView();
	
	$is_post_view = false;
	$currentIPArr = get_post_meta( $id, 'is_post_view', true );
	
	if(!empty($currentIPArr)) {		
		if(in_array($currentIP, $currentIPArr)) {
			$is_post_view = true;
			return;
		} else {
			$currentIPArr = get_post_meta( $id, 'is_post_view', true );
			$currentIPArr[] = $currentIP;
		}
	} else {
		$currentIPArr = array();
	}

	$currentIPArr = ($is_post_view == false) ? array($currentIP) : $currentIPArr;
	
	if ( ! wp_is_post_revision( $post ) && ! is_preview() ) {
		if ( is_single() ) {
			$id = (int) $post->ID;

			$post_views = 0;
			if ( !$post_views = get_post_meta( $id, 'post_view', true ) ) {
				$post_views = 0;
			}

			update_post_meta( $id, 'post_view', $post_views + 1 );
			update_post_meta( $id, 'is_post_view', $currentIPArr );
		}
	}
}

function getPostView() {
	$postViewValue = get_post_meta( get_the_ID(), 'post_view', true );
	if(empty($postViewValue)) {
		$postViewValue = 0;
	}

	$postViews = (int) $postViewValue;
	$postViewLabel = __( 'Post View', 'wp-simple-post-view' );
	$postViews = '<div class="formated_post_view"><span>'. $postViewLabel .' : </span> '.$postViews.'</div>';
	echo apply_filters('get_post_view', $postViews);
}

add_shortcode( 'single-post-view', 'single_post_view_shortcode_fun');
function single_post_view_shortcode_fun( $atts ) {

	global $user_ID, $post;

	if(get_post_type($post->ID) != 'post') {
		return;
	}

	$attributes = shortcode_atts( array( 'id' => 0 ), $atts );
	$id = (int) $attributes['id'];
	if( $id === 0) {
		$id = get_the_ID();
	}

	$postViewValue = get_post_meta( $id, 'post_view', true );
	if(empty($postViewValue)) {
		$postViewValue = 0;
	}

	$postViews = (int) $postViewValue;
	$postViewLabel = __( 'Post View', 'wp-simple-post-view' );
	$postViews = '<div class="formated_post_view"><span>'. $postViewLabel .' : </span> '.$postViews.'</div>';
	return apply_filters( 'get_post_view', $postViews );
}

define( 'WP_SIMPLE_POST_VIEW_PLUGIN_DIR', dirname( __FILE__ ) );
require_once(plugin_dir_path(__FILE__) . "customFunctions.php");
require_once(plugin_dir_path(__FILE__) . "add_post_column.php");
