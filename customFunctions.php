<?php
function postViewAddMetaBoxFun() {
	global $post;

	if(get_post_type($post->ID) == 'post') {
			
			$postView = __( 'Post View', 'wp-simple-post-view' );

		    add_meta_box(
		        'add_post_view',           // Unique ID
		        $postView,  // Box title
		        'addPostViewMetaBoxHTMLFun',  // Content callback, must be of type callable
		        'post',                   // Post type
		        'side'
		    );
	}
}
add_action('add_meta_boxes', 'postViewAddMetaBoxFun');

function addPostViewMetaBoxHTMLFun($post) {
	if(isset($post)) {
	$postView = __( 'Post View', 'wp-simple-post-view' );
	$value = get_post_meta($post->ID, 'post_view', true); ?>
	<label for="wporg_field"><strong><?php echo $postView;?></strong></label>
    <input type="number" name="post_view" style="width: 70%;" placeholder="post view" value="<?php echo $value;?>">
	<?php }
}

function addPostViewMetaBoxSavePostdata($post_id) {

	if(get_post_type($post_id) == 'post') {

	    if (array_key_exists('post_view', $_POST)) {

	        $postViewValue = !empty($_POST['post_view']) ? $_POST['post_view'] : '';

	        update_post_meta(
	            $post_id,
	            'post_view',
	            $postViewValue
	        );
	    }
	}
}
add_action('save_post', 'addPostViewMetaBoxSavePostdata');