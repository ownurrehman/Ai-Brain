<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( class_exists( '\Elementor\Plugin' ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
	WP_CLI::log( 'elementor cache cleared' );
}
