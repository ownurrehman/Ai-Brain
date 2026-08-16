<?php
/**
 * Custom ajax functionalities.
 * Core actions stripped - managed cleanly inside WPCode Snippets.
 */

function coinsfera_ajax_login_init() {
    wp_register_script(
        'coinsfera-ajax-functionality',
        COINSFERA_URI . '/assets/js/ajax-functionality.js',
        array( 'jquery' )
    );
    wp_enqueue_script( 'coinsfera-ajax-functionality' );

    wp_localize_script( 'coinsfera-ajax-functionality', 'ajax_object', array(
        'ajaxurl'     => admin_url( 'admin-ajax.php' ),
        'redirecturl' => '',
    ));
}
add_action( 'init', 'coinsfera_ajax_login_init' );