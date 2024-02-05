<?php 
/*
 * Plugin Name:		Post View Count
 * Description:		Using this plugin, see how many views your posts have. [ngd-single-post-view] OR [ngd-single-post-view id="post_id"]
 * Text Domain:		wp-simple-post-view
 * Domain Path:		/languages
 * Version:			1.8.2
 * WordPress URI:	https://wordpress.org/plugins/wp-simple-post-view/
 * Plugin URI:		https://wordpress.org/plugins/wp-simple-post-view/
 * Contributors: 	nareshparmar827, dipakparmar443
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

add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'wp_simple_post_view_add_plugin_page_settings_link');
function wp_simple_post_view_add_plugin_page_settings_link( $links ) {
	$links[] = '<a href="' .
		admin_url( 'admin.php?page=wp-spv' ) .
		'">' . __('Settings') . '</a>';
	return $links;
}

/**
 * Register a custom menu page.
 */
function wp_simple_post_view_register_my_custom_menu_page() {
    add_menu_page(
        __( 'Post View Settings', 'textdomain' ),
        'Post View Settings',
        'manage_options',
        'wp-spv',
        'wp_simple_post_view_settings',
        '');

    add_action( 'admin_init', 'register_wp_simple_post_view_settings' );
}
add_action( 'admin_menu', 'wp_simple_post_view_register_my_custom_menu_page' );

function register_wp_simple_post_view_settings() {
	//register our settings
	register_setting( 'wp-simple-post-view-settings-group', 'wp_simple_post_view_text' );
}

function wp_simple_post_view_settings(){

	if( isset( $_REQUEST['wp-spv-save-settings'] ) && isset( $_REQUEST['page'] ) ){		
		global $wpdb;
		$q = "DELETE  FROM {$wpdb->prefix}postmeta WHERE meta_key='post_view' or meta_key='is_post_view'";
		$sucess = $wpdb->query($q);
		if( isset($sucess) || $sucess === 0 ){
			?>
		    <div class="notice notice-success is-dismissible">
		        <p><?php _e( 'Success!', 'sample-text-domain' ); ?></p>
		    </div>
		    <?php
		}else{
			$class = 'notice notice-error';
		    $message = __( 'An error has occurred.', 'wp-simple-post-view' );		 
		    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
		}

	}

	?>
    <div class="wrap">
        <h1><?php _e( 'Post View Count Settings', 'wp-simple-post-view' ); ?></h1>
        <form method="POST" action="<?php echo admin_url( 'admin.php?page=wp-spv' ); ?>" onclick="return yes_no();">	        
	        <?php submit_button( __( 'Reset Post view Data', 'wp-simple-post-view' ), 'primary', 'wp-spv-save-settings' ); ?>
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
		<h1>Text Edit Settings</h1>

		<form method="post" action="options.php">
		    <?php settings_fields( 'wp-simple-post-view-settings-group' ); ?>
		    <?php do_settings_sections( 'wp-simple-post-view-settings-group' ); ?>
		    <table class="form-table">
		        <tr valign="top">
		        <th scope="row">Post View Text</th>
		        
		        <?php $wp_simple_post_view_text = esc_attr( get_option('wp_simple_post_view_text') );
		        if( empty( $wp_simple_post_view_text ) ) {
		        	$wp_simple_post_view_text = 'Post View';
		        }
		        ?>
		        <td><input type="text" style="width: 60%;" name="wp_simple_post_view_text" value="<?php echo $wp_simple_post_view_text; ?>" /></td>
		        </tr>
		        
		    </table>
		    
		    <?php submit_button(); ?>

		</form>
		</div>
    <?php
}