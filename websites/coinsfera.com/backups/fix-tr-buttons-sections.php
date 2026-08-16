<?php
/**
 * Fix Buy Bitcoin TR: strip HTML from the banner button, translate leftover
 * English widgets using existing Coinsfera Turkish copy.
 *
 * Run: wp eval-file fix-tr-buttons-sections.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

$tr_id = 11226;
$bak   = WP_CONTENT_DIR . '/uploads/cfkl-backups/buy-bitcoin-wpml-pilot-20260816';
if ( ! is_dir( $bak ) ) {
	wp_mkdir_p( $bak );
}

$data = json_decode( (string) get_post_meta( $tr_id, '_elementor_data', true ), true );
if ( ! is_array( $data ) ) {
	WP_CLI::error( 'TR Elementor JSON missing' );
}

file_put_contents(
	$bak . '/tr-11226-_elementor_data-before-button-fix.json',
	get_post_meta( $tr_id, '_elementor_data', true )
);

$buy_sell = array(
	'buy_sell_section_title'                 => "Türkiye'de Bitcoin Alım Satım Hizmetleri",
	'buy_sell_section_buy_title'             => 'Nakit ile Bitcoin Alın',
	'buy_sell_section_buy_desc'              => "Coinsfera ile İstanbul, Türkiye'de nakit karşılığı Bitcoin almak artık çok kolay.",
	'buy_sell_section_buy_btn_title'         => 'WhatsApp',
	'buy_sell_section_sell_title'            => 'Nakit ile Güvenle BTC Alın',
	'buy_sell_section_sell_desc'             => 'Coinsfera kripto para ofisimizde İstanbul’da nakit ile anında ve güvenli BTC alımı sunuyoruz.',
	'buy_sell_section_sell_btn_title'        => 'Şimdi arayın',
	'buy_sell_section_consultancy_title'     => 'Kripto Para Danışmanlığı',
	'buy_sell_section_consultancy_desc'      => 'Kripto para birimleri hakkında yeterli bilgiye sahip olmasanız da uzmanlarımız süreç boyunca size yardımcı olacaktır.',
	'buy_sell_section_consultancy_btn_title' => 'Telegram',
);

$buy_sell_items = array( 'Müşteriler', 'İşlemler', 'Geri bildirim' );

$how_to = array(
	'how_to_buy_section_title'   => 'Coinsfera Bitcoin ATM Borsasının Avantajları',
	'how_to_buy_section_btn_lbl' => 'Başlayın',
);

$how_to_items = array(
	array(
		'title' => 'Güvenli',
		'desc'  => 'İstanbul’da Bitcoin almak için güvenli ve özel bir ortam sunuyoruz. İşleminiz başından sonuna kadar korunur.',
	),
	array(
		'title' => 'Anında işlem',
		'desc'  => 'İstanbul’da dakikalar içinde nakit karşılığı Bitcoin alın. Ekibimiz süreci hızlı ve kolay hale getirir.',
	),
	array(
		'title' => 'En iyi kurlar',
		'desc'  => 'Gizli ücret olmadan İstanbul’daki en iyi Bitcoin kurlarını alın. Fiyatlandırmamız her zaman açık ve adildir.',
	),
);

$changed = 0;

$walk = function ( &$nodes ) use ( &$walk, &$changed, $buy_sell, $buy_sell_items, $how_to, $how_to_items ) {
	if ( ! is_array( $nodes ) ) {
		return;
	}
	foreach ( $nodes as &$n ) {
		if ( ! is_array( $n ) ) {
			continue;
		}
		$w = $n['widgetType'] ?? '';
		$s = &$n['settings'];
		if ( ! is_array( $s ) ) {
			$s = array();
		}

		if ( 'cryptocurrency_inner_banner' === $w ) {
			if ( ! empty( $s['cryptocurrency_inner_banner_btn_lbl'] ) ) {
				$clean = trim( wp_strip_all_tags( html_entity_decode( (string) $s['cryptocurrency_inner_banner_btn_lbl'], ENT_QUOTES, 'UTF-8' ) ) );
				if ( $clean !== $s['cryptocurrency_inner_banner_btn_lbl'] ) {
					$s['cryptocurrency_inner_banner_btn_lbl'] = $clean;
					++$changed;
				}
			}
			if ( empty( $s['cryptocurrency_inner_banner_btn_icon'] ) || empty( $s['cryptocurrency_inner_banner_btn_icon']['value'] ) ) {
				$s['cryptocurrency_inner_banner_btn_icon'] = array(
					'value'   => 'fas fa-phone-volume',
					'library' => 'fa-solid',
				);
				++$changed;
			}
		}

		if ( 'buy_sell_section' === $w ) {
			foreach ( $buy_sell as $k => $v ) {
				if ( ( $s[ $k ] ?? '' ) !== $v ) {
					$s[ $k ] = $v;
					++$changed;
				}
			}
			if ( ! empty( $s['buy_sell_section_items'] ) && is_array( $s['buy_sell_section_items'] ) ) {
				foreach ( $s['buy_sell_section_items'] as $i => $item ) {
					if ( isset( $buy_sell_items[ $i ] ) && ( $item['title'] ?? '' ) !== $buy_sell_items[ $i ] ) {
						$s['buy_sell_section_items'][ $i ]['title'] = $buy_sell_items[ $i ];
						++$changed;
					}
				}
			}
		}

		if ( 'how_to_buy_section' === $w ) {
			foreach ( $how_to as $k => $v ) {
				if ( ( $s[ $k ] ?? '' ) !== $v ) {
					$s[ $k ] = $v;
					++$changed;
				}
			}
			if ( ! empty( $s['how_to_buy_section_items'] ) && is_array( $s['how_to_buy_section_items'] ) ) {
				foreach ( $s['how_to_buy_section_items'] as $i => $item ) {
					if ( empty( $how_to_items[ $i ] ) ) {
						continue;
					}
					foreach ( $how_to_items[ $i ] as $ik => $iv ) {
						if ( ( $item[ $ik ] ?? '' ) !== $iv ) {
							$s['how_to_buy_section_items'][ $i ][ $ik ] = $iv;
							++$changed;
						}
					}
				}
			}
		}

		if ( 'heading' === $w && ! empty( $s['title'] ) ) {
			$map = array(
				'Location of our crypto exchange office' => 'Kripto para ofisimizin konumu',
				'Video Location of Coinsfera Bitcoin OTC ATM in Istanbul, Türkiye' => "İstanbul, Türkiye'deki Coinsfera Bitcoin OTC ATM ofisinin video konumu",
			);
			$title = trim( (string) $s['title'] );
			$title = str_replace( 'Tu00fcrkiye', 'Türkiye', $title );
			foreach ( $map as $en => $tr ) {
				if ( 0 === strcasecmp( rtrim( $title ), rtrim( $en ) ) ) {
					$s['title'] = $tr;
					++$changed;
				}
			}
			if ( false !== stripos( $title, 'Video Location of Coinsfera' ) ) {
				$s['title'] = "İstanbul, Türkiye'deki Coinsfera Bitcoin OTC ATM ofisinin video konumu";
				++$changed;
			}
		}

		if ( 'text-editor' === $w && ! empty( $s['editor'] ) && false !== stripos( $s['editor'], 'Bitcoin Shop Istanbul' ) ) {
			$s['editor'] = '<h4 style="text-align: center;">İstanbul\'da Bitcoin Mağazası</h4>';
			++$changed;
		}

		if ( 'global' === $w && (int) ( $n['templateID'] ?? 0 ) === 26211 ) {
			$n['widgetType'] = 'homepage_community_section';
			unset( $n['templateID'] );
			$s['homepage_community_section_title'] = 'Sosyal Medyada Bizi Takip Edin';
			++$changed;
		}

		if ( 'homepage_community_section' === $w && ( $s['homepage_community_section_title'] ?? '' ) === 'Follow Us on Social Media' ) {
			$s['homepage_community_section_title'] = 'Sosyal Medyada Bizi Takip Edin';
			++$changed;
		}

		if ( ! empty( $n['elements'] ) ) {
			$walk( $n['elements'] );
		}
	}
};

$walk( $data );

$json = wp_json_encode( $data );
update_metadata( 'post', $tr_id, '_elementor_data', wp_slash( $json ) );
delete_post_meta( $tr_id, '_elementor_css' );

if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
}

$live = (string) get_post_meta( $tr_id, '_elementor_data', true );
WP_CLI::log( 'changes=' . $changed );
WP_CLI::log( 'btn_html=' . ( false !== strpos( $live, '<i class="fas fa-phone-volume">' ) ? 'still_present' : 'stripped' ) );
WP_CLI::log( 'buy_sell_tr=' . ( false !== strpos( $live, "Türkiye'de Bitcoin Alım Satım" ) || false !== strpos( $live, 'T\u00fcrkiye\'de Bitcoin' ) || false !== strpos( $live, 'Nakit ile Bitcoin Al' ) ? 'yes' : 'no' ) );
WP_CLI::log( 'how_to_en=' . ( false !== strpos( $live, 'Benefits of Coinsfera Bitcoin ATM Exchange' ) ? 'yes' : 'no' ) );
WP_CLI::log( 'follow_en=' . ( false !== strpos( $live, 'Follow Us on Social Media' ) ? 'yes' : 'no' ) );
