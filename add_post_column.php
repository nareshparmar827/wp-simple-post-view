<?php 
add_filter( 'manage_post_posts_columns', 'addPostView_filter_posts_columns' );
function addPostView_filter_posts_columns( $columns ) {
  
  $columns['post_view'] = __( 'Post View', 'wp-simple-post-view' );
  return $columns;
}

add_action( 'manage_post_posts_custom_column', 'PostView_post_column', 10, 2);
function PostView_post_column( $column, $post_id ) {
  // Post View column
  if ( 'post_view' === $column ) {
  	$post_view_count = get_post_meta($post_id, 'post_view', true);
    echo (!empty($post_view_count)) ? $post_view_count : 0;
  }
}

