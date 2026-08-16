<?php
/**
 * Create a WPCode CSS snippet that hides Edit with Elementor on RU/TR.
 *
 * Run: wp eval-file create-hide-elementor-snippet.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

if ( ! class_exists( 'WPCode_Snippet' ) ) {
	WP_CLI::error( 'WPCode is not loaded' );
}

wp_set_current_user( 14 );

$title = 'Hide Edit with Elementor on RU/TR';

$existing = get_posts(
	array(
		'post_type'      => 'wpcode',
		'post_status'    => 'any',
		'title'          => $title,
		'posts_per_page' => 1,
		'fields'         => 'ids',
	)
);

$css = <<<'CSS'
/* Hide "Edit with Elementor" on Russian and Turkish pages only. */
html[lang="ru"] #wp-admin-bar-elementor_edit_page,
html[lang="ru-RU"] #wp-admin-bar-elementor_edit_page,
html[lang="tr"] #wp-admin-bar-elementor_edit_page,
html[lang="tr-TR"] #wp-admin-bar-elementor_edit_page,
html[lang="ru"] #elementor-switch-mode,
html[lang="ru-RU"] #elementor-switch-mode,
html[lang="tr"] #elementor-switch-mode,
html[lang="tr-TR"] #elementor-switch-mode,
html[lang="ru"] #elementor-editor,
html[lang="ru-RU"] #elementor-editor,
html[lang="tr"] #elementor-editor,
html[lang="tr-TR"] #elementor-editor,
html[lang="ru"] #elementor-go-to-edit-page-link,
html[lang="ru-RU"] #elementor-go-to-edit-page-link,
html[lang="tr"] #elementor-go-to-edit-page-link,
html[lang="tr-TR"] #elementor-go-to-edit-page-link,
html[lang="ru"] .elementor-switch-mode,
html[lang="ru-RU"] .elementor-switch-mode,
html[lang="tr"] .elementor-switch-mode,
html[lang="tr-TR"] .elementor-switch-mode {
	display: none !important;
}
CSS;

$snippet = new WPCode_Snippet(
	array(
		'title'      => $title,
		'code'       => $css,
		'code_type'  => 'css',
		'location'   => 'site_wide_header',
		'auto_insert'=> 1,
		'active'     => true,
		'note'       => 'Hides Elementor edit controls on /ru/ and /tr/ pages. English pages are unchanged.',
		'tags'       => array( 'wpml', 'elementor' ),
		'priority'   => 10,
	)
);

if ( $existing ) {
	$snippet->id = (int) $existing[0];
}

$saved = $snippet->save();

if ( ! $saved ) {
	WP_CLI::error( 'Could not save WPCode snippet' );
}

$id = is_int( $saved ) ? $saved : $snippet->get_id();
WP_CLI::log( 'snippet_id=' . $id . ' status=' . get_post_status( $id ) . ' type=' . implode( ',', wp_get_post_terms( $id, 'wpcode_type', array( 'fields' => 'slugs' ) ) ) . ' location=' . implode( ',', wp_get_post_terms( $id, 'wpcode_location', array( 'fields' => 'slugs' ) ) ) );
