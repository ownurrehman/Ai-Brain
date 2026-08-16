<?php
/**
 * Plugin Name: Tonic Physio Performance Seal
 * Description: SAFE MINIMAL — only keeps LiteSpeed Guest Optimization + CSS/JS combine OFF. No theme CSS, fonts, icons, lazyload, or front-end markup changes.
 * Version: 2.0.0-safe
 * Author: RankRay
 * License: GPL-2.0-or-later
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Intentionally does NOT:
 * - print critical CSS
 * - dequeue ElementsKit / icon fonts
 * - force CSS async or JS delay
 * - hide/delay Chaty
 * - replace theme fonts
 * - alter header/footer layout
 */
final class Tonic_Physio_Perf_Seal {
	const OPTION_APPLIED = 'tonic_perf_seal_safe_v2';
	const VERSION = '2.0.0-safe';

	public static function init(): void {
		register_activation_hook(__FILE__, [self::class, 'activate']);
		add_action('litespeed_init', [self::class, 'force_safe_opts'], 5);
		add_action('init', [self::class, 'maybe_persist'], 20);

		// Title-only geo fix (Milton, CA → Ontario). No layout impact.
		add_filter('wpseo_title', [self::class, 'fix_geo'], 20);
		add_filter('wpseo_opengraph_title', [self::class, 'fix_geo'], 20);
		add_filter('pre_get_document_title', [self::class, 'fix_geo'], 20);
	}

	public static function activate(): void {
		delete_option('tonic_perf_seal_litespeed_applied_v4');
		delete_option('tonic_perf_seal_litespeed_applied_v3');
		delete_option('tonic_perf_seal_litespeed_applied_v2');
		delete_option('tonic_perf_seal_litespeed_applied_v1');
		self::persist_opts();
		update_option(self::OPTION_APPLIED, self::VERSION);
		do_action('litespeed_purge_all');
	}

	public static function force_safe_opts(): void {
		do_action('litespeed_conf_force', 'guest', false);
		do_action('litespeed_conf_force', 'guest_optm', false);
		do_action('litespeed_conf_force', 'optm-css_comb', false);
		do_action('litespeed_conf_force', 'optm-js_comb', false);
	}

	public static function maybe_persist(): void {
		if (get_option(self::OPTION_APPLIED) === self::VERSION) {
			return;
		}
		self::persist_opts();
		update_option(self::OPTION_APPLIED, self::VERSION);
	}

	private static function persist_opts(): void {
		$opts = [
			'guest' => false,
			'guest_optm' => false,
			'optm-css_comb' => false,
			'optm-js_comb' => false,
			// Restore normal paint path — undo aggressive settings from v1.x
			'optm-css_async' => false,
			'optm-js_defer' => 0,
		];

		$conf = get_option('litespeed.conf');
		if (is_array($conf)) {
			foreach ($opts as $k => $v) {
				$conf[$k] = $v;
			}
			update_option('litespeed.conf', $conf);
		}

		foreach ($opts as $k => $v) {
			do_action('litespeed_conf_force', $k, $v);
		}

		if (class_exists('LiteSpeed\Purge')) {
			try {
				\LiteSpeed\Purge::purge_all();
			} catch (\Throwable $e) {
				// ignore
			}
		}
		do_action('litespeed_purge_all');
	}

	public static function fix_geo($title) {
		if (!is_string($title) || $title === '') {
			return $title;
		}
		return preg_replace('/\bMilton,\s*CA\b/i', 'Milton, Ontario', $title);
	}
}

Tonic_Physio_Perf_Seal::init();
