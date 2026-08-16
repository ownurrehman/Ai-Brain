<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

function faqs( $id ) {
	$j = json_decode( (string) get_post_meta( $id, '_elementor_data', true ), true );
	$out = array();
	$walk = function ( $nodes ) use ( &$walk, &$out ) {
		foreach ( (array) $nodes as $n ) {
			if ( ! is_array( $n ) ) {
				continue;
			}
			if ( ( $n['widgetType'] ?? '' ) === 'faq_section' ) {
				foreach ( (array) ( $n['settings']['faq_section_items'] ?? array() ) as $i => $it ) {
					$out[] = array(
						'title' => $it['title'] ?? '',
						'desc'  => $it['desc'] ?? $it['content'] ?? $it['answer'] ?? '',
						'keys'  => array_keys( $it ),
					);
				}
			}
			if ( ! empty( $n['elements'] ) ) {
				$walk( $n['elements'] );
			}
		}
	};
	$walk( $j );
	return $out;
}

foreach ( array( 2036, 11226, 11248 ) as $id ) {
	WP_CLI::log( "== $id " . get_the_title( $id ) . " ==" );
	foreach ( faqs( $id ) as $i => $f ) {
		if ( $i < 8 && 11248 !== $id ) {
			continue;
		}
		WP_CLI::log( "$i keys=" . implode( ',', $f['keys'] ) );
		WP_CLI::log( '  Q=' . $f['title'] );
		WP_CLI::log( '  A=' . substr( wp_strip_all_tags( $f['desc'] ), 0, 180 ) );
	}
}
