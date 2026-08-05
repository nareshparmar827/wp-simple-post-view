<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
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

		    $server_keys = array(
		        'HTTP_CLIENT_IP',
		        'HTTP_X_FORWARDED_FOR',
		        'HTTP_X_FORWARDED',
		        'HTTP_FORWARDED_FOR',
		        'HTTP_FORWARDED',
		        'REMOTE_ADDR'
		    );

		    foreach ( $server_keys as $key ) {
		        if ( ! empty( $_SERVER[ $key ] ) ) {
		            // Remove slashes and sanitize
		            $ipaddress = sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );

		            // If HTTP_X_FORWARDED_FOR contains multiple IPs, take the first one
		            if ( $key === 'HTTP_X_FORWARDED_FOR' && strpos( $ipaddress, ',' ) !== false ) {
		                $ip_array = explode( ',', $ipaddress );
		                $ipaddress = trim( $ip_array[0] );
		            }

		            break;
		        }
		    }

		    if ( empty( $ipaddress ) ) {
		        $ipaddress = 'UNKNOWN';
		    }

		    return $ipaddress;
		}



		public function ngd_insertProcessPostviewFun() {
			
			global $user_ID, $post;			
			
			// Bail early if $post is not set or not an object
			if ( ! isset( $post ) || ! is_a( $post, 'WP_Post' ) ) {
				return;
			}

			if ( is_int( $post ) ) {
				$post = get_post( $post );
			}
			
			$id = (int) $post->ID;

			// Do not proceed if not a single post view
			if ( ! is_single( $id ) || wp_is_post_revision( $id ) || is_preview() ) {
				return;
			}

			$currentIP = NGD_wpSimplePostView::ngd_getCurrentIPAddressForPostView();
			
			$is_post_view = false;
			$currentIPArr = get_post_meta( $id, 'is_post_view', true );
			
			if( ! empty( $currentIPArr ) ) {		
				if( in_array( $currentIP, $currentIPArr ) ) {
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
		    if ( empty( $postViewValue ) ) {
		        $postViewValue = 0;
		    }

		    // Default label for translation
		    $default_label = __( 'Post View', 'wp-simple-post-view' );

		    // Allow user to override via settings
		    $wp_simple_post_view_text = esc_attr( get_option('wp_simple_post_view_text') );
		    if ( empty( $wp_simple_post_view_text ) ) {
		        $postViewLabel = $default_label;
		    } else {
		        $postViewLabel = $wp_simple_post_view_text; // no translation needed
		    }

		    $postViews = '<div class="formated_post_view"><span>' . esc_html( $postViewLabel ) . ' : </span> ' . esc_html( (int) $postViewValue ) . '</div>';

		    // Escape after applying filter, allowing safe HTML
		    echo wp_kses_post( apply_filters( 'get_post_view', $postViews ) );
		}

		public function ngd_single_post_view_shortcode_fun( $atts ) {

		    global $post;

		    if ( get_post_type( $post->ID ) !== 'post' ) {
		        return;
		    }

		    $attributes = shortcode_atts( array( 'id' => 0 ), $atts );
		    $id = (int) $attributes['id'];
		    if ( $id === 0 ) {
		        $id = get_the_ID();
		    }

		    // Get post view count
		    $postViewValue = get_post_meta( $id, 'post_view', true );
		    $postViewValue = is_numeric( $postViewValue ) ? $postViewValue : 0;

		    // Default label (literal string for translation)
		    $default_label = __( 'Post View', 'wp-simple-post-view' );

		    // Get user-defined label from settings
		    $wp_simple_post_view_text = esc_attr( get_option( 'wp_simple_post_view_text' ) );
		    $postViewLabel = ! empty( $wp_simple_post_view_text ) ? $wp_simple_post_view_text : $default_label;

		    // Build output
		    $postViews = '<div class="formated_post_view"><span>' . esc_html( $postViewLabel ) . ' : </span> ' . esc_html( $postViewValue ) . '</div>';

		    return apply_filters( 'get_post_view', $postViews );
		}


	}

	$NGD_wpSimplePostView = new NGD_wpSimplePostView();
	add_action( 'wp_head', array( $NGD_wpSimplePostView, 'ngd_insertProcessPostviewFun'));
	add_shortcode( 'ngd-single-post-view', array( $NGD_wpSimplePostView, 'ngd_single_post_view_shortcode_fun'));
}