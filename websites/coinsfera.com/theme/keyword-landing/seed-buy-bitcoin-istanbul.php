<?php
/**
 * Seed a draft page on the Keyword Landing template.
 *
 * Run with: wp eval-file seed-buy-bitcoin-istanbul.php
 *
 * Idempotent: re-running updates the same draft rather than creating another.
 * Creates a DRAFT only. Nothing is published and no existing page is modified.
 */

if ( ! defined( 'WP_CLI' ) ) {
	exit( "Run through WP-CLI.\n" );
}

/**
 * One draft per design. Identical content in every one, so the only thing
 * being compared is the design.
 */
$variants = array(
	'desk'      => array(
		'slug'  => 'buy-bitcoin-istanbul-design-a-desk',
		'title' => 'Buy Bitcoin in Istanbul - Design A (OTC Desk)',
	),
	'concierge' => array(
		'slug'  => 'buy-bitcoin-istanbul-design-b-concierge',
		'title' => 'Buy Bitcoin in Istanbul - Design B (Istanbul Concierge)',
	),
	'neo'       => array(
		'slug'  => 'buy-bitcoin-istanbul-design-c-neo',
		'title' => 'Buy Bitcoin in Istanbul - Design C (Neo Fintech)',
	),
	'ledger'    => array(
		'slug'  => 'buy-bitcoin-istanbul-design-d-ledger',
		'title' => 'Buy Bitcoin in Istanbul - Design D (Swiss Ledger)',
	),
);

$maps     = 'https://www.google.com/maps?ftid=0x14cab9eaf6c4d8b3:0xb19442e13f909950';
$contact  = 'https://www.coinsfera.com/contact-us/';
$about    = 'https://www.coinsfera.com/about-us/';
$sell_btc = 'https://www.coinsfera.com/sell-bitcoin-in-istanbul/';
$buy_usdt = 'https://www.coinsfera.com/buy-tether-in-istanbul/';
$buy_eth  = 'https://www.coinsfera.com/buy-ethereum-in-istanbul/';
$hub      = 'https://www.coinsfera.com/istanbul/';

$fields = array(

	'cfkl_banner_tagline'    => 'Istanbul OTC desk · Trading since 2015',
	'cfkl_banner_heading'    => 'Buy Bitcoin in Istanbul with cash, in person',
	'cfkl_banner_subtext'    => 'Walk into our Beyoglu office, agree the rate face to face, and leave with Bitcoin in a wallet you control. No account to open, no withdrawal limits to wait out, no sending money to a stranger online.',
	'cfkl_banner_cta_label'  => "Get today's Bitcoin rate",
	'cfkl_banner_cta_url'    => $contact,
	'cfkl_hero_image'        => 27737,
	'cfkl_banner_stats'      => array(
		array( 'value' => 'Since 2015', 'label' => 'Serving Istanbul' ),
		array( 'value' => '500+', 'label' => 'Coins supported' ),
		array( 'value' => '4.9 / 5', 'label' => 'Google rating' ),
		array( 'value' => '~30 min', 'label' => 'Typical visit' ),
	),

	/* Office block. The Maps URL is the Place FTID lifted from the map embed
	   already running on the contact page, so it points at the Google listing
	   rather than back at the website. */
	'cfkl_banner_office_label'   => 'Our Istanbul office',
	'cfkl_banner_card_title'     => "Mueyyedzade, Necatibey Cd. No.51/A,\nBeyoglu, Istanbul 34425",
	'cfkl_banner_card_btn_label' => 'View on Google Maps',
	'cfkl_banner_card_btn_url'   => $maps,
	'cfkl_banner_office_rating'  => '4.9',

	/* Calculator. The spread is what the desk adds to the market rate, so the
	   quoted number is the number the visitor pays. */
	'cfkl_calc_title'            => 'Work out what your Bitcoin will cost',
	'cfkl_calc_text'             => 'Live market price with our desk margin already included. Change the amount to see what you would pay today, before you come in.',
	'cfkl_calc_spread_buy'       => 1.5,
	'cfkl_calc_spread_sell'      => 1.5,
	'cfkl_calc_default_coin'     => 'BTC',
	'cfkl_calc_default_currency' => 'usd',
	'cfkl_calc_whatsapp'         => '905374140909',
	'cfkl_calc_cta_label'        => 'Send this amount to the desk',
	'cfkl_calc_note'             => 'Indicative figure. The desk confirms your final rate before anything is settled and holds it while you complete the trade. Network fees are shown at cost before we send.',

	'cfkl_rates_title' => 'Rates at the Istanbul desk right now',
	'cfkl_rates_text'  => 'Market prices, refreshed every few minutes. Your quote includes our margin and is confirmed in person before you commit to anything.',
	'cfkl_rates_coins' => array( 'BTC', 'USDT', 'ETH', 'XRP' ),

	'cfkl_coins_title' => 'Coins traded over the counter',
	'cfkl_coins_text'  => 'These are the ones that move most often at the desk. We handle more than 500 in total, so ask if the coin you want is not here.',
	'cfkl_coins_list'  => array(
		array( 'symbol' => 'BTC', 'name' => 'Bitcoin', 'url' => 'https://www.coinsfera.com/buy-bitcoin-in-istanbul/' ),
		array( 'symbol' => 'USDT', 'name' => 'Tether', 'url' => $buy_usdt ),
		array( 'symbol' => 'ETH', 'name' => 'Ethereum', 'url' => $buy_eth ),
		array( 'symbol' => 'USDC', 'name' => 'USD Coin', 'url' => $hub ),
		array( 'symbol' => 'XRP', 'name' => 'XRP', 'url' => $hub ),
		array( 'symbol' => 'SOL', 'name' => 'Solana', 'url' => $hub ),
		array( 'symbol' => 'BNB', 'name' => 'BNB', 'url' => $hub ),
		array( 'symbol' => 'TRX', 'name' => 'Tron', 'url' => $hub ),
	),

	'cfkl_compare_title'  => 'The four ways to buy Bitcoin in Istanbul',
	'cfkl_compare_text'   => 'The same purchase, handled three different ways. This is the honest version, including where another route beats us.',
	'cfkl_compare_col_us' => 'Coinsfera desk',
	'cfkl_compare_col_b'  => 'Online exchange',
	'cfkl_compare_col_c'  => 'Bitcoin ATM',
	'cfkl_compare_rows'   => array(
		array(
			'label' => 'Time until you hold the coins',
			'us'    => 'Same visit, about 30 minutes',
			'b'     => 'One to five days with verification',
			'c'     => 'Minutes',
		),
		array(
			'label' => 'Cash accepted',
			'us'    => 'Lira, dollars or euro',
			'b'     => 'No',
			'c'     => 'Small amounts only',
		),
		array(
			'label' => 'Rate known before you pay',
			'us'    => 'Quoted and held',
			'b'     => 'Shown, then slippage on size',
			'c'     => 'Often only at the end',
		),
		array(
			'label' => 'Typical total cost',
			'us'    => 'One margin, tighter as size grows',
			'b'     => 'Trading fee, spread and withdrawal',
			'c'     => 'Five to twelve per cent',
		),
		array(
			'label' => 'Six figure orders',
			'us'    => 'Priced as a single block',
			'b'     => 'Split orders and review flags',
			'c'     => 'Not possible',
		),
		array(
			'label' => 'Who holds your coins',
			'us'    => 'You, sent to your own wallet',
			'b'     => 'The platform until you withdraw',
			'c'     => 'You',
		),
		array(
			'label' => 'Someone accountable',
			'us'    => 'The desk you met in person',
			'b'     => 'A support queue',
			'c'     => 'Nobody on site',
		),
		array(
			'label' => 'Best for',
			'us'    => 'Cash, size and same-day settlement',
			'b'     => 'Small recurring buys',
			'c'     => 'Tiny amounts in a hurry',
		),
	),

	'cfkl_fees_title' => 'What you pay, in full',
	'cfkl_fees_text'  => 'One margin, quoted before you agree. Everything else below is either at cost or genuinely nothing.',
	'cfkl_fees_rows'  => array(
		array(
			'label' => 'Desk margin',
			'value' => 'In your quote',
			'note'  => 'Built into the rate you agree, and quoted tighter as the amount grows',
		),
		array(
			'label' => 'Account or signup fee',
			'value' => 'None',
			'note'  => 'There is no platform to join and no account to fund',
		),
		array(
			'label' => 'Deposit or funding fee',
			'value' => 'None',
			'note'  => 'You settle directly at the desk, in cash or by arranged transfer',
		),
		array(
			'label' => 'Withdrawal fee',
			'value' => 'None',
			'note'  => 'Coins go straight to a wallet you control, not a custodial balance',
		),
		array(
			'label' => 'Blockchain network fee',
			'value' => 'At cost',
			'note'  => 'Shown to you before we broadcast the transaction',
		),
		array(
			'label' => 'Walking away',
			'value' => 'None',
			'note'  => 'Decline the quote and nothing is owed',
		),
	),

	/* Rating and count are the live figures from the Google Business Profile.
	   Quoted reviews are left empty on purpose: they have to be real, so the
	   desk pastes them in from the profile rather than a writer inventing them. */
	'cfkl_reviews_title'  => 'Rated by people who walked in',
	'cfkl_reviews_rating' => '4.9',
	'cfkl_reviews_count'  => '1,043',
	'cfkl_reviews_url'    => $maps,

	'cfkl_office_title' => 'Visiting the office',
	'cfkl_office_text'  => 'We are on Necatibey Caddesi in Karakoy, a few minutes from the Tophane tram stop and an easy walk from Galata. Come in during opening hours, or message ahead and the desk will have your quote ready when you arrive.',
	'cfkl_office_hours' => array(
		array( 'days' => 'Monday to Saturday', 'hours' => '09:00 - 18:00' ),
		array( 'days' => 'Sunday', 'hours' => 'Closed' ),
	),
	'cfkl_office_directions' => array(
		array(
			'label' => 'By tram',
			'desc'  => 'T1 to Tophane, then a three minute walk along Necatibey Caddesi.',
		),
		array(
			'label' => 'By metro',
			'desc'  => 'M2 to Sishane, then downhill through Galata, roughly ten minutes on foot.',
		),
		array(
			'label' => 'By ferry',
			'desc'  => 'Karakoy terminal is a seven minute walk, which is the pleasant way to arrive from the old city.',
		),
		array(
			'label' => 'By car',
			'desc'  => 'Paid car parks sit on Kemeralti and Necatibey Caddesi. Street parking is scarce on weekdays.',
		),
	),
	'cfkl_office_map' => 'https://www.google.com/maps?q=Coinsfera+Necatibey+Caddesi+51+Karakoy+Istanbul&output=embed&hl=en&z=16',

	'cfkl_intro_title' => 'Buying Bitcoin in Istanbul without the online guesswork',
	'cfkl_intro_text'  => '<p>Most people who search for how to buy Bitcoin in Istanbul end up choosing between three awkward options: an international exchange that holds their money behind verification queues and withdrawal limits, a peer-to-peer listing where they have to trust a stranger, or a Bitcoin ATM charging a spread they only discover at the end of the transaction.</p>'
		. '<p>Coinsfera works differently because we are an actual over-the-counter desk with an actual address. You come to our office in Beyoglu, we quote you a rate before anything is agreed, and the coins move to your wallet while you are sitting with us. If the rate does not suit you, you walk away having lost nothing but the trip.</p>'
		. '<p>That matters most on larger orders. A five or six figure purchase on a retail exchange usually means splitting the order, tripping review flags, or accepting slippage. Handled as an OTC trade, it is a single agreed price and a single settlement.</p>',

	'cfkl_trust_title'  => 'Why buyers pick a physical desk over an app',
	'cfkl_trust_text'   => 'An app can be perfectly good for buying small amounts over time. It is a poor fit when the amount is significant, when you want the coins today, or when you would simply rather look someone in the eye before moving your money.',
	'cfkl_trust_image'  => 27711,
	'cfkl_trust_points' => array(
		array(
			'title' => 'An office you can visit before you commit',
			'desc'  => 'We have operated from the same Beyoglu district since 2015. You can come in, ask questions and leave without trading. Our Google rating sits at 4.9 out of 5 from customers who did exactly that.',
		),
		array(
			'title' => 'The rate is quoted before you hand anything over',
			'desc'  => 'You are told the price for your specific amount up front, including everything. There is no percentage revealed at the confirmation screen and no spread that widens once you have already deposited.',
		),
		array(
			'title' => 'The coins land in your own wallet',
			'desc'  => 'We send to a wallet address you control and confirm the transaction with you before you leave. Nothing stays in a custodial balance waiting on a withdrawal approval.',
		),
	),

	'cfkl_steps_title' => 'How buying Bitcoin at our Istanbul desk works',
	'cfkl_steps'       => array(
		array(
			'title' => 'Tell us the amount',
			'desc'  => 'Message or call the desk with the amount you want to buy and how you intend to pay. We come back with a live quote.',
			'image' => 27518,
		),
		array(
			'title' => 'Agree the rate',
			'desc'  => 'The quoted price is all-in for that amount. Larger orders are priced tighter. Nothing is committed until you say yes.',
			'image' => 27517,
		),
		array(
			'title' => 'Come to the office',
			'desc'  => 'Visit us in Beyoglu at a time that suits you. Bring your identification and your payment. Have your wallet address ready.',
			'image' => 27516,
		),
		array(
			'title' => 'Leave with your Bitcoin',
			'desc'  => 'We settle on the spot and send to your wallet. You watch the transaction confirm before you go. Most visits take about half an hour.',
			'image' => 27515,
		),
	),

	'cfkl_req_title' => 'What to bring with you',
	'cfkl_req_text'  => 'Preparing three things in advance turns a visit into a short one. If you are unsure about any of them, ask the desk beforehand and we will walk you through it.',
	'cfkl_req_cards' => array(
		array(
			'title' => 'Photo identification',
			'desc'  => 'A passport or national ID. We are a regulated business and verify who we trade with, which is also what protects you.',
			'image' => 27516,
		),
		array(
			'title' => 'Your wallet address',
			'desc'  => 'Bring the receiving address for a wallet you control, ideally as a QR code. If you do not have a wallet yet, tell us and we will help you set one up properly.',
			'image' => 27517,
		),
		array(
			'title' => 'Your payment',
			'desc'  => 'Cash in Turkish lira, US dollars or euro, or a bank transfer arranged in advance. Confirm the method with the desk when you get your quote.',
			'image' => 27518,
		),
	),

	'cfkl_features_title' => 'What the desk gives you that an exchange does not',
	'cfkl_features'       => array(
		array(
			'title' => 'Same-day settlement',
			'desc'  => 'Agree a price in the morning and hold the coins the same afternoon. No verification backlog standing between you and your own money.',
			'image' => 27517,
		),
		array(
			'title' => 'Pricing that improves with size',
			'desc'  => 'Large orders are quoted as a single block rather than eating through an order book, so the price you are shown is the price you get.',
			'image' => 27518,
		),
		array(
			'title' => 'A person who stays reachable',
			'desc'  => 'You deal with the same desk before, during and after the trade. If something needs checking, you are not filing a support ticket.',
			'image' => 27516,
		),
		array(
			'title' => 'English, Turkish and Russian',
			'desc'  => 'Our team handles the entire conversation in whichever of the three you are most comfortable with, including the technical parts.',
			'image' => 27515,
		),
	),

	'cfkl_services_title' => 'Also handled at our Istanbul office',
	'cfkl_services'       => array(
		array(
			'title' => 'Sell Bitcoin for cash',
			'desc'  => 'Bring Bitcoin, leave with lira, dollars or euro in hand, priced the same transparent way.',
			'url'   => $sell_btc,
			'icon'  => 27518,
		),
		array(
			'title' => 'Buy and sell USDT',
			'desc'  => 'Tether against cash or bank transfer, which is usually the fastest route in and out of the market.',
			'url'   => $buy_usdt,
			'icon'  => 27517,
		),
		array(
			'title' => 'Buy Ethereum',
			'desc'  => 'ETH over the counter on the same terms, settled to your own wallet during your visit.',
			'url'   => $buy_eth,
			'icon'  => 27516,
		),
		array(
			'title' => 'Everything we do in Istanbul',
			'desc'  => 'More than 500 coins, consultancy for larger positions, and the full list of services at the Beyoglu desk.',
			'url'   => $hub,
			'icon'  => 27515,
		),
	),

	'cfkl_faq_title'  => 'Questions people ask before visiting',
	'cfkl_faq_schema' => 1,
	'cfkl_faq_items'  => array(
		array(
			'title' => 'Do I need an account or registration to buy Bitcoin with you?',
			'desc'  => 'No. There is no platform to sign up for and no account to fund. You are trading directly with the desk, so the only verification involved is the identity check we carry out in person as a regulated business.',
		),
		array(
			'title' => 'Can I pay in cash?',
			'desc'  => 'Yes. We accept cash in Turkish lira, US dollars and euro, and we can also arrange a bank transfer if you prefer. Confirm which method you plan to use when you request your quote so the desk has the settlement ready.',
		),
		array(
			'title' => 'Is there a minimum or maximum amount?',
			'desc'  => 'Small retail purchases are welcome and large orders are our speciality. Very large trades are quoted individually, so contact the desk in advance and we will confirm pricing and availability for the size you have in mind.',
		),
		array(
			'title' => 'How is your rate calculated?',
			'desc'  => 'We quote against the live market price with our margin already included, so the number you are given is the number you pay. Larger amounts are quoted more tightly. Nothing is added at the end.',
		),
		array(
			'title' => 'Do I need to bring a wallet?',
			'desc'  => 'You need a receiving address for a wallet you control, which is what we send the Bitcoin to. If you do not have one yet, tell us when you book and we will help you set one up and explain how to back it up before you leave.',
		),
		array(
			'title' => 'Do I need an appointment?',
			'desc'  => 'You can walk in, but we recommend messaging first. Getting a quote in advance means the desk has your amount prepared and your visit takes minutes rather than an hour, especially for larger orders.',
		),
		array(
			'title' => 'Where exactly is the office?',
			'desc'  => 'Mueyyedzade, Necatibey Cd. No.51/A in Beyoglu, central Istanbul. The contact page has a map, directions and our opening hours.',
		),
	),

	'cfkl_cta_title' => 'Ready to buy Bitcoin in Istanbul today?',
	'cfkl_cta_label' => 'Talk to the desk',
	'cfkl_cta_url'   => $contact,
);

foreach ( $variants as $design => $variant ) {

	$existing = get_posts( array(
		'name'        => $variant['slug'],
		'post_type'   => 'page',
		'post_status' => array( 'draft', 'publish', 'pending', 'private' ),
		'numberposts' => 1,
	) );

	if ( $existing ) {
		$page_id = $existing[0]->ID;
		wp_update_post( array(
			'ID'         => $page_id,
			'post_title' => $variant['title'],
		) );
	} else {
		$page_id = wp_insert_post( array(
			'post_title'   => $variant['title'],
			'post_name'    => $variant['slug'],
			'post_type'    => 'page',
			'post_status'  => 'draft',
			'post_content' => '',
		), true );

		if ( is_wp_error( $page_id ) ) {
			WP_CLI::error( $page_id->get_error_message() );
		}
	}

	update_post_meta( $page_id, '_wp_page_template', 'page-templates/template-keyword-landing.php' );

	foreach ( $fields as $name => $value ) {
		update_field( $name, $value, $page_id );
	}

	update_field( 'cfkl_design', $design, $page_id );

	WP_CLI::log( sprintf(
		'%-10s id %-6d %s',
		$design,
		$page_id,
		get_preview_post_link( $page_id )
	) );
}

WP_CLI::success( 'Seeded ' . count( $variants ) . ' drafts on the Keyword Landing template. All remain drafts.' );
