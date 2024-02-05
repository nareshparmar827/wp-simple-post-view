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
			add_filter( 'manage_edit-post_sortable_columns', array( 'NGD_wpSimplePostView_Admin', 'ngd_register_sortable_columns') );
			add_filter( 'request', array( 'NGD_wpSimplePostView_Admin', 'ngd_hits_column_orderby') );
		}
		
		//Add filter to the request to make the hits sorting process numeric, not string
		public static function ngd_hits_column_orderby( $vars ) {
		    if ( isset( $vars['orderby'] ) && 'post_view' == $vars['orderby'] ) {

		    	$isPostCountExists = get_post_meta( 'post_view' );

		        if( ! isset( $isPostCountExists ) && empty( $isPostCountExists ) ) {
		        	return $vars;
		        }
		        
		        $vars = array_merge( $vars, array(
		            'meta_key' => 'post_view',
		            'orderby' => 'meta_value_num'
		        ) );
		    }

		    return $vars;
		}

		// Register the columns as sortable
		public static function ngd_register_sortable_columns( $columns ) {
		    $columns['post_view'] = 'post_view';
		    return $columns;
		}
		
		public static function ngd_addPostView_filter_posts_columns( $columns ) {
  		  
  		  $wp_simple_post_view_text = esc_attr( get_option('wp_simple_post_view_text') );
          if( empty( $wp_simple_post_view_text ) ) {
        	$wp_simple_post_view_text = 'Post View';
          }

		  $columns['post_view'] = __( $wp_simple_post_view_text, 'wp-simple-post-view' );
		  return $columns;
		}

		public static function ngd_PostView_post_column( $column, $post_id ) {
		  // Post View column
		  if ( 'post_view' === $column ) {
		  	$post_view_count = get_post_meta($post_id, 'post_view', true);
		  	if( ! empty( $post_view_count ) ){
		  		if( is_numeric( $post_view_count ) ){
		  			echo $post_view_count;
		  		}else{
		  			echo 0;
		  		}
		  	}else{
		  		echo 0;
		  	}
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
