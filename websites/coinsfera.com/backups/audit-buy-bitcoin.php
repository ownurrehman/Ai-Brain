<?php
/**
 * Audit Buy Bitcoin EN/TR buttons and leftover English on TR.
 *
 * Run: wp eval-file audit-buy-bitcoin.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

function cfkl_walk( $nodes, $cb ) {
	if ( ! is_array( $nodes ) ) {
		return;
	}
	foreach ( $nodes as $n ) {
		if ( ! is_array( $n ) ) {
			continue;
		}
		$cb( $n );
		if ( ! empty( $n['elements'] ) ) {
			cfkl_walk( $n['elements'], $cb );
		}
	}
}

function cfkl_looks_html( $s ) {
	return is_string( $s ) && ( false !== strpos( $s, '<' ) || false !== strpos( $s, '&lt;' ) );
}

function cfkl_looks_english( $s ) {
	if ( ! is_string( $s ) || '' === trim( $s ) ) {
		return false;
	}
	if ( preg_match( '/[а-яА-ЯёЁ]/u', $s ) || preg_match( '/[çğıöşüÇĞİÖŞÜ]/u', $s ) ) {
		return false;
	}
	return (bool) preg_match( '/\b(the|and|with|buy|sell|bitcoin|contact|requirements|how to|premium|choose|benefits|cash|exchange|istanbul|turkey)\b/i', $s );
}

foreach ( array( 2036 => 'EN', 11226 => 'TR', 6644 => 'RU' ) as $id => $lang ) {
	$data = json_decode( (string) get_post_meta( $id, '_elementor_data', true ), true );
	WP_CLI::log( "==== {$lang} {$id} ====" );
	cfkl_walk(
		$data,
		function ( $n ) use ( $lang ) {
			$w = $n['widgetType'] ?? '';
			if ( ( $n['elType'] ?? '' ) !== 'widget' ) {
				return;
			}
			$s = $n['settings'] ?? array();
			$flat = array();
			$stack = array( $s );
			while ( $stack ) {
				$cur = array_pop( $stack );
				if ( ! is_array( $cur ) ) {
					continue;
				}
				foreach ( $cur as $k => $v ) {
					if ( is_string( $v ) ) {
						$flat[ $k ] = $v;
					} elseif ( is_array( $v ) ) {
						$stack[] = $v;
					}
				}
			}
			foreach ( $flat as $k => $v ) {
				if ( cfkl_looks_html( $v ) && preg_match( '/btn|button|label|title|text/i', (string) $k ) ) {
					WP_CLI::log( "HTML {$w}.{$k} = " . substr( $v, 0, 140 ) );
				}
			}
			if ( 'TR' === $lang ) {
				foreach ( $flat as $k => $v ) {
					if ( cfkl_looks_english( $v ) && strlen( $v ) > 12 && ! preg_match( '/(_id|url|css|class|color|font|size|unit)$/i', (string) $k ) ) {
						WP_CLI::log( "EN  {$w}.{$k} = " . substr( preg_replace( '/\s+/', ' ', $v ), 0, 120 ) );
					}
				}
			}
		}
	);
}
