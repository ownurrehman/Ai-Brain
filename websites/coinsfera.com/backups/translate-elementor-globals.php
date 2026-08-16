<?php
/**
 * Create TR/RU translations of Elementor global templates used on Buy Bitcoin.
 *
 * Run: wp eval-file translate-elementor-globals.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

function coinsfera_replace_json_strings( $data, $map ) {
	if ( is_string( $data ) ) {
		return array_key_exists( $data, $map ) ? $map[ $data ] : $data;
	}
	if ( ! is_array( $data ) ) {
		return $data;
	}
	foreach ( $data as $k => $v ) {
		$data[ $k ] = coinsfera_replace_json_strings( $v, $map );
	}
	return $data;
}

function coinsfera_wpml_library_translation( $en_id, $lang, $map ) {
	global $sitepress, $wpdb;

	$en_id = (int) $en_id;
	$en    = get_post( $en_id );
	if ( ! $en || ! $sitepress ) {
		WP_CLI::warning( "skip {$en_id}" );
		return 0;
	}

	$type = 'post_elementor_library';
	$trid = $sitepress->get_element_trid( $en_id, $type );
	if ( ! $trid ) {
		$sitepress->set_element_language_details( $en_id, $type, null, $sitepress->get_default_language() );
		$trid = $sitepress->get_element_trid( $en_id, $type );
	}

	$existing = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE trid=%d AND language_code=%s AND element_type=%s",
			$trid,
			$lang,
			$type
		)
	);

	if ( $existing > 0 ) {
		$new_id = $existing;
	} else {
		$new_id = wp_insert_post(
			array(
				'post_title'   => $en->post_title,
				'post_status'  => 'publish',
				'post_type'    => 'elementor_library',
				'post_content' => $en->post_content,
				'post_author'  => $en->post_author,
			),
			true
		);
		if ( is_wp_error( $new_id ) ) {
			WP_CLI::warning( $new_id->get_error_message() );
			return 0;
		}
		$sitepress->set_element_language_details( $new_id, $type, $trid, $lang, $sitepress->get_default_language() );
		$meta = get_post_meta( $en_id );
		foreach ( $meta as $key => $values ) {
			if ( in_array( $key, array( '_edit_lock', '_edit_last', '_elementor_css', '_wpml_post_translation_editor_native' ), true ) ) {
				continue;
			}
			delete_post_meta( $new_id, $key );
			foreach ( $values as $value ) {
				add_post_meta( $new_id, $key, maybe_unserialize( $value ) );
			}
		}
	}

	$json = json_decode( (string) get_post_meta( $en_id, '_elementor_data', true ), true );
	if ( ! is_array( $json ) ) {
		WP_CLI::warning( "no json {$en_id}" );
		return $new_id;
	}
	$json = coinsfera_replace_json_strings( $json, $map );
	update_metadata( 'post', $new_id, '_elementor_data', wp_slash( wp_json_encode( $json ) ) );
	delete_post_meta( $new_id, '_elementor_css' );

	WP_CLI::log( "{$en_id} -> {$lang} {$new_id}" );
	return (int) $new_id;
}

$maps = array(
	27285 => array(
		'tr' => array(
			'Location of Coinsfera OTC ATM in Istanbul, Turkey' => "İstanbul'da Coinsfera OTC ATM Konumu",
		),
		'ru' => array(
			'Location of Coinsfera OTC ATM in Istanbul, Turkey' => 'Расположение OTC ATM Coinsfera в Стамбуле, Турция',
		),
	),
	26211 => array(
		'tr' => array(
			'Follow Us on Social Media' => 'Sosyal Medyada Bizi Takip Edin',
			'Telegram Channel'          => 'Telegram Kanalı',
			'Join Us on Social media, Crypto exchange in Istanbul, Turkey' => "Sosyal medyada bize katılın, İstanbul kripto para borsası",
		),
		'ru' => array(
			'Follow Us on Social Media' => 'Следите за нами в соцсетях',
			'Telegram Channel'          => 'Канал Telegram',
			'Join Us on Social media, Crypto exchange in Istanbul, Turkey' => 'Присоединяйтесь к нам в соцсетях, криптообмен в Стамбуле',
		),
	),
	21436 => array(
		'tr' => array(
			'Email' => 'E-posta',
		),
		'ru' => array(
			'Email' => 'Эл. почта',
		),
	),
);

foreach ( $maps as $en_id => $langs ) {
	foreach ( $langs as $lang => $map ) {
		coinsfera_wpml_library_translation( $en_id, $lang, $map );
	}
}

delete_option( 'wpml_config_files_arr' );
if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
}

WP_CLI::log( '27285 tr=' . apply_filters( 'wpml_object_id', 27285, 'elementor_library', true, 'tr' ) );
WP_CLI::log( '26211 tr=' . apply_filters( 'wpml_object_id', 26211, 'elementor_library', true, 'tr' ) );
