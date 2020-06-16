<?php 
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
		}

		public function ngd_addPostView_filter_posts_columns( $columns ) {
  
		  $columns['post_view'] = __( 'Post View', 'wp-simple-post-view' );
		  return $columns;
		}

		public function ngd_PostView_post_column( $column, $post_id ) {
		  // Post View column
		  if ( 'post_view' === $column ) {
		  	$post_view_count = get_post_meta($post_id, 'post_view', true);
		    echo (!empty($post_view_count)) ? $post_view_count : 0;
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