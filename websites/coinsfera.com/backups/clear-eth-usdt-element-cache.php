<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

foreach ( array( 11237, 11570 ) as $id ) {
	delete_post_meta( $id, '_elementor_element_cache' );
	WP_CLI::log( "cleared element cache {$id}" );
}

if ( class_exists( '\Elementor\Plugin' ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
	WP_CLI::log( 'elementor files cache cleared' );
}
