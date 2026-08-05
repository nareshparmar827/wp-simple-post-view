<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// If check class exists.
if ( ! class_exists( 'NGD_wpSimplePostView_Admin_AddMetaBox' ) ) {
	/**
	 * Declare class.
	 */
	class NGD_wpSimplePostView_Admin_AddMetaBox {
		/**
		 * Calling construct.
		 */
		public function __construct() {
			add_action('add_meta_boxes', array( 'NGD_wpSimplePostView_Admin_AddMetaBox', 'ngd_postViewAddMetaBoxFun'));
			add_action('save_post', array( 'NGD_wpSimplePostView_Admin_AddMetaBox', 'ngd_addPostViewMetaBoxSavePostdata'));
		}

		public static function ngd_postViewAddMetaBoxFun() {
			global $post;
			if( get_post_type( $post->ID ) === 'post' ) {					
				$postView = __( 'Post View', 'wp-simple-post-view' );
			    add_meta_box(
			        'add_post_view', // Unique ID
			        $postView,  // Box title
			        array( 'NGD_wpSimplePostView_Admin_AddMetaBox', 'ngd_addPostViewMetaBoxHTMLFun'),  // Content callback, must be of type callable
			        'post', // Post type
			        'side'
			    );
			}
		}

		public static function ngd_addPostViewMetaBoxHTMLFun($post) {
		    if (isset($post)) {
		        $postView = __( 'Post View', 'wp-simple-post-view' );
		        $value = get_post_meta($post->ID, 'post_view', true); ?>
		        
		        <label for="wporg_field"><strong><?php echo esc_html($postView); ?></strong></label>
		        <?php wp_nonce_field( 'wpspv_action', 'wpspv_field' ); ?>
		        <input type="number" name="post_view" style="width: 70%;" placeholder="0" value="<?php echo esc_attr($value); ?>">
		        
		    <?php }
		}

		public static function ngd_addPostViewMetaBoxSavePostdata($post_id) {

		    // Only for posts
		    if ( get_post_type( $post_id ) !== 'post' ) {
		        return;
		    }

		    // Check if nonce is set and valid
			if ( ! isset( $_POST['wpspv_field'] ) || ! wp_verify_nonce( sanitize_text_field ( wp_unslash( $_POST['wpspv_field'] ) ), 'wpspv_action' ) ) {
			    return;
			}

		    // Prevent autosave
		    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		        return;
		    }

		    // Optional: check user capability
		    if ( ! current_user_can( 'edit_post', $post_id ) ) {
		        return;
		    }

		    // Check for inline edit (bulk edit) and exit
		    $screen  = isset( $_REQUEST['screen'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['screen'] ) ) : '';
		    $action  = isset( $_REQUEST['action'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : '';
		    if ( $screen === 'edit-post' && $action === 'inline-save' ) {
		        return;
		    }

		    // Get and sanitize post_view
		    if ( isset( $_POST['post_view'] ) ) {
		        $post_view_value = intval( wp_unslash( $_POST['post_view'] ) ); // number field, cast to integer
		        update_post_meta( $post_id, 'post_view', $post_view_value );
		    }
		}

	}
}

/**
 * Initialization class.
 */
if ( ! function_exists( 'ngd_wpSimplePostView_Admin_Metabox_init' ) ) {
	function ngd_wpSimplePostView_Admin_Metabox_init() {
		new NGD_wpSimplePostView_Admin_AddMetaBox();
	}
	add_action( 'plugins_loaded', 'ngd_wpSimplePostView_Admin_Metabox_init' );
}