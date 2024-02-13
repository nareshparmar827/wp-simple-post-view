<?php 
/*
 * Plugin Name: Post View Count
 * Description: Using this plugin, see how many views your posts have. [ngd-single-post-view] OR [ngd-single-post-view id="post_id"]
 * Text Domain: wp-simple-post-view
 * Domain Path: /languages
 * Version: 2.0.2
 * Requires PHP: 7.2
 * Requires at least: 5.2
 * WordPress URI: https://wordpress.org/plugins/wp-simple-post-view/
 * Plugin URI: https://wordpress.org/plugins/wp-simple-post-view/
 * Contributors: nareshparmar827, dipakparmar443
 * Author: Naresh Parmar
 * Author URI: https://profiles.wordpress.org/nareshparmar827/
 * Donate Link: https://www.paypal.me/NARESHBHAIPARMAR
 * License: GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Update URI: https://wordpress.org/plugins/wp-simple-post-view/
 * @copyright: Naresh Parmar
*/

/*
{Post View Count} is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

{Post View Count} is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with {Post View Count}. If not, see {https://www.gnu.org/licenses/gpl-3.0.html}.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! defined( 'NGD_WP_SIMPLE_POST_VIEW_PLUGIN_DIR' ) ) {
	define( 'NGD_WP_SIMPLE_POST_VIEW_PLUGIN_DIR', plugin_dir_path(__FILE__));
}
if ( ! defined( 'NGD_WP_SIMPLE_POST_VIEW_URL' ) ) {
	define( 'NGD_WP_SIMPLE_POST_VIEW_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'NGD_WP_SIMPLE_POST_VIEW_BASENAME' ) ) {
	define( 'NGD_WP_SIMPLE_POST_VIEW_BASENAME', plugin_basename( __FILE__ ) );
}

/**
 * Plugin textdomain.
 */
add_action( 'plugins_loaded', 'ngd_wp_simple_post_view_textdomain' );
if ( ! function_exists( 'ngd_wp_simple_post_view_textdomain' ) ) {
	function ngd_wp_simple_post_view_textdomain() {
		load_plugin_textdomain( 'wp-simple-post-view', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}
}

/**
 * Plugin activation.
 */
register_activation_hook( __FILE__, 'ngd_wp_simple_post_view_activation' );
if ( ! function_exists( 'ngd_wp_simple_post_view_activation' ) ) {
	function ngd_wp_simple_post_view_activation() {
		// Activation code here.
	}
}

/**
 * Plugin deactivation.
 */
register_deactivation_hook( __FILE__, 'ngd_wp_simple_post_view_deactivation' );
if ( ! function_exists( 'ngd_wp_simple_post_view_deactivation' ) ) {
	function ngd_wp_simple_post_view_deactivation() {
		// Deactivation code here.
	}
}

require_once( NGD_WP_SIMPLE_POST_VIEW_PLUGIN_DIR . 'includes/post-simple-post-view.php');
if ( is_admin() ) {
	require_once( NGD_WP_SIMPLE_POST_VIEW_PLUGIN_DIR . 'includes/custom-functions.php');
	require_once( NGD_WP_SIMPLE_POST_VIEW_PLUGIN_DIR . 'includes/add-post-column.php');
}

add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'wp_simple_post_view_add_plugin_page_settings_link');
function wp_simple_post_view_add_plugin_page_settings_link( $links ) {	
	$url = add_query_arg( 'wpspv_wpnonce', wp_create_nonce( 'wpspv_action' ), esc_url( admin_url( 'admin.php?page=wp-spv' ) ) );
	$links[] = '<a href="' . esc_url( $url ) . '">' . __('Settings') . '</a>';
	return $links;
}

/**
 * Register a "Post View Settings" menu page.
 */
function wp_simple_post_view_register_menu_page() {
    add_menu_page( __( 'Post View Settings', 'wp-simple-post-view' ), __( 'Post View Settings', 'wp-simple-post-view' ), 'manage_options', 'wp-spv', 'wp_simple_post_view_settings', '' );
    add_action( 'admin_init', 'register_wp_simple_post_view_settings' );
}
add_action( 'admin_menu', 'wp_simple_post_view_register_menu_page' );

function register_wp_simple_post_view_settings() {
	register_setting( 'wp-simple-post-view-settings-group', 'wp_simple_post_view_text' );
}

function wp_simple_post_view_settings(){

	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		die( __( 'Security check.', 'wp-simple-post-view' ) );
		return;
	}
	if( isset( $_REQUEST[ 'wp-spv-reset-settings' ] ) && isset( $_REQUEST[ 'page' ] ) ){		
			
		if ( isset( $_REQUEST[ 'wpspv_field' ] ) && wp_verify_nonce( $_REQUEST[ 'wpspv_field' ], 'wpspv_action' ) ) {
			// process form data
			global $wpdb;
			$sucess = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $wpdb->postmeta WHERE meta_key = %s OR meta_key = %s ", 
				'post_view', 
				'is_post_view'
			)
			);
			if( isset( $sucess ) || $sucess === 0 ){
				$class = 'notice notice-success is-dismissible';
				$message = __( 'Post view data reset successfully!', 'wp-simple-post-view' );		 
				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );				
			}else{
				$class = 'notice notice-error';
				$message = __( 'An error has occurred.', 'wp-simple-post-view' );		 
				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
			}
		} else {
			die( __( 'Security check.', 'wp-simple-post-view' ) );
			exit;
		}
	}
	?>
    <div class="wrap">
        <h1><?php _e( 'Post View Count Settings', 'wp-simple-post-view' ); ?></h1>
        <form method="POST" onclick="return yes_no();">
        	<?php wp_nonce_field( 'wpspv_action', 'wpspv_field' ); ?>
	        <?php submit_button( __( 'Reset Post view Data', 'wp-simple-post-view' ), 'primary', 'wp-spv-reset-settings' ); ?>
	    </form>
	    <script type="text/javascript">
	    	jQuery( document ).ready( function (){
	    		yes_no = function( event ){
		    		var r = confirm('Are you sure you want to reset?');
		    		if( r == true){
		    			return true;
		    		}else{
		    			return false;
		    		}
		    	}
	    	});
	    </script>
    </div>
    <div class="wrap">
		<h1><?php _e( 'Text Edit Settings', 'wp-simple-post-view' ); ?></h1>
		<form method="post" action="options.php">
		    <?php settings_fields( 'wp-simple-post-view-settings-group' ); ?>
		    <?php do_settings_sections( 'wp-simple-post-view-settings-group' ); ?>
		    <?php wp_nonce_field( 'wpspv_action', 'wpspv_field' ); ?>
		    <table class="form-table">
		        <tr valign="top">
		        <th scope="row"><?php _e( 'Post View Text', 'wp-simple-post-view' ); ?></th>		        
		        <?php 
			        $wp_simple_post_view_text = get_option('wp_simple_post_view_text');
			        if( empty( $wp_simple_post_view_text ) ) {
			        	$wp_simple_post_view_text =  _e( 'Post View', 'wp-simple-post-view' );
			        }
		        ?>
		        <td><input type="text" style="width: 60%;" name="wp_simple_post_view_text" value="<?php echo esc_attr( $wp_simple_post_view_text ); ?>" /></td>
		        </tr>		        
		    </table>		    
		    <?php submit_button(); ?>
		</form>
	</div>
    <?php
}