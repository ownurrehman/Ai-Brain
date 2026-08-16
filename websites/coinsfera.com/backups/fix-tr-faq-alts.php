<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

$id   = 11226;
$data = json_decode( (string) get_post_meta( $id, '_elementor_data', true ), true );
$n    = 0;

$walk = function ( &$nodes ) use ( &$walk, &$n ) {
	foreach ( $nodes as &$el ) {
		if ( ! is_array( $el ) ) {
			continue;
		}
		$w = $el['widgetType'] ?? '';
		$s = &$el['settings'];
		if ( 'faq_section' === $w && ! empty( $s['faq_section_items'][10] ) ) {
			$s['faq_section_items'][10]['title'] = "Türkiye'de nakit ile Bitcoin almak yasal mı?";
			$s['faq_section_items'][10]['desc']  = "Türkiye'de Bitcoin almak yasaldır ve kripto para ticareti yatırımcılar ile bireyler tarafından yaygın olarak kullanılmaktadır. Kripto paralar resmi para birimi olarak tanınmasa da, kişiler Bitcoin'i kripto para borsaları ve OTC masaları aracılığıyla serbestçe alabilir, satabilir ve saklayabilir. Türkiye'de Bitcoin nasıl alınır diye araştıran birçok yatırımcı, güvenli ve şeffaf alım için İstanbul'daki güvenilir kripto borsalarında yüz yüze işlemi tercih eder.\n\nCoinsfera, müşterilerin İstanbul'da nakit ile güvenli ve rehberli bir süreçte Bitcoin alabileceği profesyonel bir OTC kripto borsası olarak faaliyet gösterir. İstanbul ofisimiz, alımı tamamlamadan önce cüzdan bilgilerini ve işlem detaylarını teyit ederek nakit paranın Bitcoin'e çevrilmesine yardımcı olur.";
			++$n;
		}
		if ( 'buy_sell_section' === $w ) {
			$s['buy_sell_section_buy_img_alt']  = 'Nakit ile Bitcoin Alın';
			$s['buy_sell_section_sell_img_alt'] = 'Nakit ile Güvenle BTC Alın';
			++$n;
		}
		if ( ! empty( $el['elements'] ) ) {
			$walk( $el['elements'] );
		}
	}
};
$walk( $data );
update_metadata( 'post', $id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
delete_post_meta( $id, '_elementor_css' );
if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
}
WP_CLI::log( 'updated=' . $n );
