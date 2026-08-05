<?php

// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die( esc_html__( 'Security check.', 'wp-simple-post-view' ) );
}