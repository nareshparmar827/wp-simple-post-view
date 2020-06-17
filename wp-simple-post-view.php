<?php 
/*
 * Plugin Name:		Post View Count
 * Description:		Using this plugin, see how many views your posts have. [ngd-single-post-view] OR [ngd-single-post-view id="post_id"]
 * Text Domain:		wp-simple-post-view
 * Domain Path:		/languages
 * Version:			1.0
 * WordPress URI:	https://wordpress.org/plugins/wp-simple-post-view/
 * Plugin URI:		https://wordpress.org/plugins/wp-simple-post-view/
 * Contributors: 	naershparmar827
 * Author:			Naresh Parmar
 * Author URI:		https://profiles.wordpress.org/nareshparmar827/
 * Donate Link:		https://www.paypal.me/NARESHBHAIPARMAR
 * License:			GPL-3.0
 * License URI:		https://www.gnu.org/licenses/gpl-3.0.html
 * @copyright:		Naresh Parmar
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NGD_WP_SIMPLE_POST_VIEW_PLUGIN_DIR', plugin_dir_path(__FILE__));

/**
 * Plugin textdomain.
 */

add_action( 'plugins_loaded', 'ngd_wpSimplePostView_textdomain' );
if ( ! function_exists( 'ngd_wpSimplePostView_textdomain' ) ) {

	function ngd_wpSimplePostView_textdomain() {
		load_plugin_textdomain( 'wp-simple-post-view', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

}

/**
 * Plugin activation.
 */

register_activation_hook( __FILE__, 'ngd_wpSimplePostViewActivation' );
if ( ! function_exists( 'ngd_wpSimplePostViewActivation' ) ) {

	function ngd_wpSimplePostViewActivation() {
		// Activation code here.
	}

}

/**
 * Plugin deactivation.
 */

register_deactivation_hook( __FILE__, 'ngd_wpSimplePostViewDeactivation' );
if ( ! function_exists( 'ngd_wpSimplePostViewDeactivation' ) ) {

	function ngd_wpSimplePostViewDeactivation() {
		// Deactivation code here.
	}

}

require_once(NGD_WP_SIMPLE_POST_VIEW_PLUGIN_DIR . "includes/postSimplePostView.php");
require_once(NGD_WP_SIMPLE_POST_VIEW_PLUGIN_DIR . "includes/customFunctions.php");
require_once(NGD_WP_SIMPLE_POST_VIEW_PLUGIN_DIR . "includes/add_post_column.php");