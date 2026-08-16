<?php
/**
 * Age-based noindex for News posts (older than 3 years).
 *
 * Replaces the WPCode snippet that noindexed every news post on every request.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * News category IDs in all languages.
 *
 * @return int[]
 */
function coinsfera_news_category_ids() {

	$ids = array( 8 );
	foreach ( array( 'ru', 'tr' ) as $lang ) {
		$tid = (int) apply_filters( 'wpml_object_id', 8, 'category', false, $lang );
		if ( $tid > 0 ) {
			$ids[] = $tid;
		}
	}

	return array_values( array_unique( $ids ) );
}

/**
 * Original (English) publish date for a post translation group.
 *
 * @param int $post_id Post ID.
 * @return string MySQL datetime or empty.
 */
function coinsfera_news_original_date( $post_id ) {

	$default = apply_filters( 'wpml_default_language', 'en' );
	$orig    = (int) apply_filters( 'wpml_object_id', (int) $post_id, 'post', true, is_string( $default ) ? $default : 'en' );
	if ( $orig <= 0 ) {
		$orig = (int) $post_id;
	}

	return (string) get_post_field( 'post_date', $orig );
}

/**
 * Whether a news post should be noindexed (older than 3 years).
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function coinsfera_news_should_noindex( $post_id ) {

	$date = coinsfera_news_original_date( $post_id );
	if ( '' === $date ) {
		return false;
	}

	return strtotime( $date ) < strtotime( '-3 years' );
}

/**
 * Apply Yoast noindex meta for one news post.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function coinsfera_news_sync_noindex_meta( $post_id ) {

	$post_id = (int) $post_id;
	if ( $post_id <= 0 || 'post' !== get_post_type( $post_id ) ) {
		return;
	}

	if ( ! has_term( coinsfera_news_category_ids(), 'category', $post_id ) ) {
		return;
	}

	if ( coinsfera_news_should_noindex( $post_id ) ) {
		update_post_meta( $post_id, '_yoast_wpseo_meta-robots-noindex', '1' );
		$noindex = 1;
	} else {
		delete_post_meta( $post_id, '_yoast_wpseo_meta-robots-noindex' );
		$noindex = 0;
	}

	global $wpdb;
	$wpdb->update(
		$wpdb->prefix . 'yoast_indexable',
		array(
			'is_robots_noindex' => $noindex,
			'is_public'         => $noindex ? 0 : 1,
		),
		array(
			'object_id'   => $post_id,
			'object_type' => 'post',
		),
		array( '%d', '%d' ),
		array( '%d', '%s' )
	);
}

/**
 * Keep noindex in sync when a news post is saved.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function coinsfera_news_on_save( $post_id ) {

	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	coinsfera_news_sync_noindex_meta( $post_id );
}
add_action( 'save_post_post', 'coinsfera_news_on_save', 20 );

/**
 * Daily pass so posts that cross the 3-year line get noindexed.
 *
 * @return void
 */
function coinsfera_news_cron_refresh() {

	$ids = get_posts(
		array(
			'category__in'     => coinsfera_news_category_ids(),
			'numberposts'      => -1,
			'post_status'      => 'publish',
			'post_type'        => 'post',
			'fields'           => 'ids',
			'suppress_filters' => true,
		)
	);

	foreach ( $ids as $id ) {
		coinsfera_news_sync_noindex_meta( (int) $id );
	}
}

/**
 * Schedule the daily news noindex refresh.
 *
 * @return void
 */
function coinsfera_news_cron_setup() {

	if ( ! wp_next_scheduled( 'coinsfera_news_noindex_cron' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'coinsfera_news_noindex_cron' );
	}
}
add_action( 'init', 'coinsfera_news_cron_setup', 20 );
add_action( 'coinsfera_news_noindex_cron', 'coinsfera_news_cron_refresh' );
