<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}
$snips = get_option( 'wpcode_snippets' );
echo 'type=' . gettype( $snips ) . "\n";
if ( is_array( $snips ) ) {
	echo 'keys=' . implode( ',', array_keys( $snips ) ) . "\n";
	foreach ( $snips as $k => $v ) {
		echo "key={$k} vtype=" . gettype( $v ) . ( is_array( $v ) ? ' count=' . count( $v ) : '' ) . "\n";
		if ( is_array( $v ) ) {
			$first = reset( $v );
			if ( is_object( $first ) || is_array( $first ) ) {
				$sample = is_object( $first ) ? get_object_vars( $first ) : $first;
				echo '  sample_keys=' . implode( ',', array_keys( $sample ) ) . "\n";
			}
			foreach ( $v as $i => $item ) {
				$id    = is_object( $item ) ? ( $item->id ?? $item->ID ?? '' ) : ( $item['id'] ?? $item['ID'] ?? '' );
				$title = is_object( $item ) ? ( $item->title ?? $item->post_title ?? '' ) : ( $item['title'] ?? '' );
				$code  = is_object( $item ) ? ( $item->code ?? $item->post_content ?? '' ) : ( $item['code'] ?? '' );
				$act   = is_object( $item ) ? ( $item->active ?? $item->status ?? '' ) : ( $item['active'] ?? '' );
				$hay   = $title . ' ' . $code;
				if ( false !== stripos( $hay, 'no index' ) || false !== stripos( $hay, 'noindex' ) || false !== stripos( $hay, 'category__in' ) || (int) $id === 28093 ) {
					echo "  HIT i={$i} id={$id} active={$act} title={$title} code=" . substr( (string) $code, 0, 200 ) . "\n";
				}
			}
		} elseif ( is_string( $v ) && ( false !== stripos( $v, '28093' ) || false !== stripos( $v, 'category__in' ) ) ) {
			echo "  string hit key={$k} " . substr( $v, 0, 200 ) . "\n";
		}
	}
} else {
	echo substr( print_r( $snips, true ), 0, 500 ) . "\n";
}
