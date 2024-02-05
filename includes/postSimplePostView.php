<?php
// If check class exists.
if ( ! class_exists( 'NGD_wpSimplePostView' ) ) {
	
	/**
	 * Declare class.
	 */
	class NGD_wpSimplePostView {

		/**
		 * Calling construct.
		 */
		public function __construct() {
			
		}

		public function ngd_getCurrentIPAddressForPostView() {
			
			$ipaddress = '';
		    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
		        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		    } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		    } elseif(isset($_SERVER['HTTP_X_FORWARDED'])){
		        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		    } elseif(isset($_SERVER['HTTP_FORWARDED_FOR'])) {
		        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		    } elseif(isset($_SERVER['HTTP_FORWARDED'])) {
		       $ipaddress = $_SERVER['HTTP_FORWARDED'];
		    } elseif(isset($_SERVER['REMOTE_ADDR'])) {
		        $ipaddress = $_SERVER['REMOTE_ADDR'];
		    } else {
		        $ipaddress = 'UNKNOWN';
		    }
		    return $ipaddress;
		}



		public function ngd_insertProcessPostviewFun() {
			
			global $user_ID, $post;
			
			
			if ( is_int( $post ) ) {
				$post = get_post( $post );
			}
			
			$id = (int) $post->ID;
			$currentIP = NGD_wpSimplePostView::ngd_getCurrentIPAddressForPostView();
			
			$is_post_view = false;
			$currentIPArr = get_post_meta( $id, 'is_post_view', true );
			
			if(!empty($currentIPArr)) {		
				if(in_array($currentIP, $currentIPArr)) {
					$is_post_view = true;
					return;
				} else {
					$currentIPArr = get_post_meta( $id, 'is_post_view', true );
					$currentIPArr[] = $currentIP;
				}
			} else {
				$currentIPArr = array();
			}

			$currentIPArr = ($is_post_view == false) ? array($currentIP) : $currentIPArr;
			
			if ( ! wp_is_post_revision( $post ) && ! is_preview() ) {
				if ( is_single() ) {
					$id = (int) $post->ID;

					$post_views = 0;
					if ( !$post_views = get_post_meta( $id, 'post_view', true ) ) {
						$post_views = 0;
					}
					$post_views = $post_views + 1;
					update_post_meta( $id, 'post_view', $post_views );
					update_post_meta( $id, 'is_post_view', $currentIPArr );
				}
			}
		}

		public function ngd_getPostView() {
			$postViewValue = get_post_meta( get_the_ID(), 'post_view', true );
			if(empty($postViewValue)) {
				$postViewValue = 0;
			}

			$wp_simple_post_view_text = esc_attr( get_option('wp_simple_post_view_text') );
            if( empty( $wp_simple_post_view_text ) ) {
        	  $wp_simple_post_view_text = 'Post View';
            }

			$postViews = (int) $postViewValue;
			$postViewLabel = __( $wp_simple_post_view_text, 'wp-simple-post-view' );
			$postViews = '<div class="formated_post_view"><span>'. $postViewLabel .' : </span> '.$postViews.'</div>';
			echo apply_filters('get_post_view', $postViews);
		}


		public function ngd_single_post_view_shortcode_fun( $atts ) {

			global $user_ID, $post;

			if(get_post_type($post->ID) != 'post') {
				return;
			}

			$attributes = shortcode_atts( array( 'id' => 0 ), $atts );
			$id = (int) $attributes['id'];
			if( $id === 0) {
				$id = get_the_ID();
			}

			$postViewValue = get_post_meta( $id, 'post_view', true );
			if(empty($postViewValue)) {
				$postViewValue = 0;
			}

			$wp_simple_post_view_text = esc_attr( get_option('wp_simple_post_view_text') );
            if( empty( $wp_simple_post_view_text ) ) {
        	  $wp_simple_post_view_text = 'Post View';
            }
            
			$postViews = (int) $postViewValue;
			$postViewLabel = __( $wp_simple_post_view_text, 'wp-simple-post-view' );
			$postViews = '<div class="formated_post_view"><span>'. $postViewLabel .' : </span> '.$postViews.'</div>';
			return apply_filters( 'get_post_view', $postViews );
		}

	}

	$NGD_wpSimplePostView = new NGD_wpSimplePostView();
	add_action( 'wp_head', array( $NGD_wpSimplePostView, 'ngd_insertProcessPostviewFun'));
	add_shortcode( 'ngd-single-post-view', array( $NGD_wpSimplePostView, 'ngd_single_post_view_shortcode_fun'));
}