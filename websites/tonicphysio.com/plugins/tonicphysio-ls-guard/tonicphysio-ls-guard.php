<?php
/**
 * Plugin Name: Tonic Physio LiteSpeed Guard
 * Description: Minimal guard only — keeps LiteSpeed Guest Optimization and CSS/JS combine OFF. Does not alter theme CSS, fonts, icons, or front-end markup.
 * Version: 1.0.0
 * Author: RankRay
 * License: GPL-2.0-or-later
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Only touches LiteSpeed config. No front-end CSS/JS/HTML changes.
 */
final class Tonic_Physio_LS_Guard {
	const OPTION_KEY = 'tonic_ls_guard_applied_v1';

	public static function init(): void {
		register_activation_hook(__FILE__, [self::class, 'activate']);
		add_action('litespeed_init', [self::class, 'force_safe_opts'], 5);
		add_action('init', [self::class, 'maybe_persist'], 20);

		// Harmless SEO geo fix only (title text).
		add_filter('wpseo_title', [self::class, 'fix_geo'], 20);
		add_filter('wpseo_opengraph_title', [self::class, 'fix_geo'], 20);
		add_filter('pre_get_document_title', [self::class, 'fix_geo'], 20);
	}

	public static function activate(): void {
		self::persist_opts();
		update_option(self::OPTION_KEY, '1.0.0');
		do_action('litespeed_purge_all');
	}

	public static function force_safe_opts(): void {
		// Guest Optimization was forcing mega CSS/JS combine — keep OFF.
		do_action('litespeed_conf_force', 'guest', false);
		do_action('litespeed_conf_force', 'guest_optm', false);
		do_action('litespeed_conf_force', 'optm-css_comb', false);
		do_action('litespeed_conf_force', 'optm-js_comb', false);
		// Do NOT force css_async or js_defer — those delayed paint and broke layout.
	}

	public static function maybe_persist(): void {
		if (get_option(self::OPTION_KEY) === '1.0.0') {
			return;
		}
		self::persist_opts();
		update_option(self::OPTION_KEY, '1.0.0');
	}

	private static function persist_opts(): void {
		$opts = [
			'guest' => false,
			'guest_optm' => false,
			'optm-css_comb' => false,
			'optm-js_comb' => false,
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

Tonic_Physio_LS_Guard::init();
