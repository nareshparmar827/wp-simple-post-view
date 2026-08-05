<?php
/*
 * Plugin Name: Post View Count
 * Description: Track how many views your posts have. Use shortcode: [ngd-single-post-view] or [ngd-single-post-view id="post_id"]
 * Version: 3.1
 * Requires PHP: 7.1
 * Requires at least: 6.8
 * Text Domain: wp-simple-post-view
 * Domain Path: /languages
 * Plugin URI: https://wordpress.org/plugins/wp-simple-post-view/
 * Author: Naresh Parmar
 * Author URI: https://profiles.wordpress.org/nareshparmar827/
 * Contributors: nareshparmar827, dipakparmar443
 * License: GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'NGD_WP_SIMPLE_POST_VIEW_PLUGIN_DIR' ) ) {
    define( 'NGD_WP_SIMPLE_POST_VIEW_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'NGD_WP_SIMPLE_POST_VIEW_URL' ) ) {
    define( 'NGD_WP_SIMPLE_POST_VIEW_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'NGD_WP_SIMPLE_POST_VIEW_BASENAME' ) ) {
    define( 'NGD_WP_SIMPLE_POST_VIEW_BASENAME', plugin_basename( __FILE__ ) );
}

/**
 * Plugin Activation.
 */
register_activation_hook( __FILE__, 'ngd_wp_simple_post_view_activation' );
function ngd_wp_simple_post_view_activation() {
    if ( get_option( 'wp_simple_post_view_text' ) === false ) {
        update_option( 'wp_simple_post_view_text', __( 'Post View', 'wp-simple-post-view' ) );
    }
}

/**
 * Plugin Deactivation.
 */
register_deactivation_hook( __FILE__, 'ngd_wp_simple_post_view_deactivation' );
function ngd_wp_simple_post_view_deactivation() {
    // Cleanup tasks if needed.
}

/**
 * Include required files.
 */
require_once NGD_WP_SIMPLE_POST_VIEW_PLUGIN_DIR . 'includes/post-simple-post-view.php';
require_once NGD_WP_SIMPLE_POST_VIEW_PLUGIN_DIR . 'includes/custom-functions.php';
require_once NGD_WP_SIMPLE_POST_VIEW_PLUGIN_DIR . 'includes/add-post-column.php';

/**
 * Add plugin settings link in Plugins page.
 */
add_filter( 'plugin_action_links_' . NGD_WP_SIMPLE_POST_VIEW_BASENAME, 'wp_simple_post_view_add_plugin_page_settings_link' );
function wp_simple_post_view_add_plugin_page_settings_link( $links ) {
    $links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wp-spv' ) ) . '">' . esc_html__( 'Settings', 'wp-simple-post-view' ) . '</a>';
    return $links;
}

/**
 * Register admin menu page.
 */
add_action( 'admin_menu', 'wp_simple_post_view_register_menu_page' );
function wp_simple_post_view_register_menu_page() {
    add_menu_page(
        __( 'Post View Settings', 'wp-simple-post-view' ),
        __( 'Post View Settings', 'wp-simple-post-view' ),
        'manage_options',
        'wp-spv',
        'wp_simple_post_view_settings_page',
        'dashicons-chart-bar'
    );

    add_action( 'admin_init', 'register_wp_simple_post_view_settings' );
}

/**
 * Register settings with sanitization.
 */
function register_wp_simple_post_view_settings() {
    register_setting(
        'wp-simple-post-view-settings-group',
        'wp_simple_post_view_text',
        array(
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => __( 'Post View', 'wp-simple-post-view' ),
        )
    );
}

/**
 * Admin settings page.
 */
function wp_simple_post_view_settings_page() {

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to access this page.', 'wp-simple-post-view' ) );
    }

    // Handle Reset Post View Data
    if ( isset( $_POST['wp-spv-reset-settings'] ) && check_admin_referer( 'wpspv_action', 'wpspv_field' ) ) {

        $wpspv_nonce = isset( $_POST['wpspv_field'] ) ? sanitize_text_field( wp_unslash( $_POST['wpspv_field'] ) ) : '';

        if ( ! wp_verify_nonce( $wpspv_nonce, 'wpspv_action' ) ) {
            wp_die( esc_html__( 'Security check failed.', 'wp-simple-post-view' ) );
        }

        // Delete all post view meta safely
        delete_metadata( 'post', 0, 'post_view', '', true );
        delete_metadata( 'post', 0, 'is_post_view', '', true );

        // Add admin notice using WP core method
        add_settings_error(
            'wp_simple_post_view_messages',
            'wp_simple_post_view_reset',
            __( 'Post view data reset successfully!', 'wp-simple-post-view' ),
            'updated'
        );
    }

    ?>

    <div class="wrap">
        <h1><?php esc_html_e( 'Post View Count Settings', 'wp-simple-post-view' ); ?></h1>

        <?php

        if ( isset( $_POST['wp-spv-reset-settings'] ) && check_admin_referer( 'wpspv_action', 'wpspv_field' ) ) {
            settings_errors( 'wp_simple_post_view_messages' );
        }

        ?>

        <form method="post" onsubmit="return confirm('<?php echo esc_js( __( 'Are you sure you want to reset?', 'wp-simple-post-view' ) ); ?>');">
            <?php wp_nonce_field( 'wpspv_action', 'wpspv_field' ); ?>
            <?php submit_button( __( 'Reset Post View Data', 'wp-simple-post-view' ), 'primary', 'wp-spv-reset-settings' ); ?>
        </form>
    </div>

    <div class="wrap">
        <h1><?php esc_html_e( 'Text Settings', 'wp-simple-post-view' ); ?></h1>
        <form method="post" action="options.php">
            <?php

            settings_fields( 'wp-simple-post-view-settings-group' );

            do_settings_sections( 'wp-simple-post-view-settings-group' );

            if( isset( $_REQUEST['settings-updated'] ) && $_REQUEST['settings-updated'] == true ){
                
                settings_errors();

            }

            ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e( 'Post View Text', 'wp-simple-post-view' ); ?></th>
                    <td>
                        <?php
                        $text = get_option( 'wp_simple_post_view_text', __( 'Post View', 'wp-simple-post-view' ) );
                        ?>
                        <input type="text" name="wp_simple_post_view_text" value="<?php echo esc_attr( $text ); ?>" style="width:60%;" />
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>

    <?php
}