<?php
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

			if(get_post_type($post->ID) == 'post') {
					
					$postView = __( 'Post View', 'wp-simple-post-view' );

				    add_meta_box(
				        'add_post_view',           // Unique ID
				        $postView,  // Box title
				        array( 'NGD_wpSimplePostView_Admin_AddMetaBox', 'ngd_addPostViewMetaBoxHTMLFun'),  // Content callback, must be of type callable
				        'post',                   // Post type
				        'side'
				    );
			}
		}

		public static function ngd_addPostViewMetaBoxHTMLFun($post) {
			if(isset($post)) {
			$postView = __( 'Post View', 'wp-simple-post-view' );
			$value = get_post_meta($post->ID, 'post_view', true); ?>
			<label for="wporg_field"><strong><?php echo $postView;?></strong></label>
		    <input type="number" name="post_view" style="width: 70%;" placeholder="post view" value="<?php echo $value;?>">
			<?php }
		}

		public static function ngd_addPostViewMetaBoxSavePostdata($post_id) {

			if(get_post_type($post_id) == 'post') {

			    if (array_key_exists('post_view', $_POST)) {

			        $postViewValue = '';
			        if ( isset( $_POST['post_view'] ) ) {
						$postViewValue = sanitize_title( $_POST['post_view'] );
					}

			        update_post_meta(
			            $post_id,
			            'post_view',
			            $postViewValue
			        );
			    }
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