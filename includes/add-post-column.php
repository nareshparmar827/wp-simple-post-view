<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// If check class exists.
if ( ! class_exists( 'NGD_wpSimplePostView_Admin' ) ) {
	/**
	 * Declare class.
	 */
	class NGD_wpSimplePostView_Admin {

		/**
		 * Calling construct.
		 */
		public function __construct() {
			add_filter( 'manage_post_posts_columns', array( 'NGD_wpSimplePostView_Admin', 'ngd_addPostView_filter_posts_columns') );
			add_filter( 'manage_post_posts_custom_column', array( 'NGD_wpSimplePostView_Admin', 'ngd_PostView_post_column'), 10, 2 );
			add_filter( 'manage_edit-post_sortable_columns', array( 'NGD_wpSimplePostView_Admin', 'ngd_register_sortable_columns') );
			add_filter( 'request', array( 'NGD_wpSimplePostView_Admin', 'ngd_hits_column_orderby') );
		}
		
		/**
		 * Filter the request to allow sorting by post view count.
		 *
		 * @param array $vars Query vars.
		 * @return array Modified query vars.
		 */
		public static function ngd_hits_column_orderby( $vars ) {
		    if ( isset( $vars['orderby'] ) && 'post_view' === $vars['orderby'] ) {

		        /*
		         * phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		         * We need to query by meta_key 'post_view' to enable sorting by post views.
		         * This may have performance implications on large sites, but it's the
		         * standard WordPress approach for sorting by custom fields.
		         */
		        $vars = array_merge(
		            $vars,
		            [
		                'meta_key' => 'post_view',
		                'orderby'  => 'meta_value_num',
		            ]
		        );
		        // phpcs:enable
		    }

		    return $vars;
		}

		// Register the columns as sortable
		public static function ngd_register_sortable_columns( $columns ) {
		    $columns['post_view'] = 'post_view';
		    return $columns;
		}
		
		public static function ngd_addPostView_filter_posts_columns( $columns ) {

		    $wp_simple_post_view_text = esc_attr( get_option( 'wp_simple_post_view_text' ) );

		    if ( empty( $wp_simple_post_view_text ) ) {
		        // translators: Default label for the post views column in the posts table.
		        $wp_simple_post_view_text = __( 'Post View', 'wp-simple-post-view' );
		    }

		    $columns['post_view'] = $wp_simple_post_view_text;

		    return $columns;
		}

		public static function ngd_PostView_post_column( $column, $post_id ) {
		    // Post View column
		    if ( 'post_view' === $column ) {
		        $post_view_count = get_post_meta( $post_id, 'post_view', true );

		        // Ensure it's a valid number
		        $post_view_count = is_numeric( $post_view_count ) ? $post_view_count : 0;

		        // Escape output before printing
		        echo esc_html( $post_view_count );
		    }
		}

	}
}

/**
 * Initialization class.
 */
if ( ! function_exists( 'ngd_wpSimplePostView_Admin_init' ) ) {

	function ngd_wpSimplePostView_Admin_init() {
		new NGD_wpSimplePostView_Admin();
	}
	add_action( 'plugins_loaded', 'ngd_wpSimplePostView_Admin_init' );

}
