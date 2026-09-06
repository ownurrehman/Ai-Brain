<?php
/**
 * ACFML fatal guard — never return non-array from acf/load_field_group.
 *
 * Merged from legacy jc-acfml-safety plugin (2026-09-06). Hide field groups via
 * acf/location/rule_match or JSON location rules, never by returning false.
 *
 * @package Justccell_Features
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Justccell_ACFML_Safety')) {
    final class Justccell_ACFML_Safety
    {
        /** @var array<string, array<string, mixed>> */
        private static array $cache = [];

        private static ?string $current_key = null;

        private static bool $logged = false;

        public static function boot(): void
        {
            add_filter('acf/load_field_group', [self::class, 'capture'], PHP_INT_MIN);
            add_filter('acf/load_field_group', [self::class, 'guard'], PHP_INT_MAX);
        }

        /** @param mixed $group */
        public static function capture($group)
        {
            if (is_array($group) && isset($group['key']) && is_string($group['key'])) {
                self::$current_key          = $group['key'];
                self::$cache[$group['key']] = $group;
            }

            return $group;
        }

        /** @param mixed $group */
        public static function guard($group)
        {
            if (is_array($group)) {
                return $group;
            }

            $restored = (self::$current_key !== null && isset(self::$cache[self::$current_key]))
                ? self::$cache[self::$current_key]
                : null;

            if (!self::$logged && function_exists('error_log')) {
                self::$logged = true;
                error_log(sprintf(
                    '[justccell-features] acf/load_field_group returned %s for group "%s" — restored to prevent ACFML fatal. Use acf/location/rule_match, not false.',
                    gettype($group),
                    (string) self::$current_key
                ));
            }

            if (is_array($restored)) {
                return $restored;
            }

            // Last resort: never pass false/non-array to ACFML (would white-screen every edit screen).
            return [
                'key'      => 'group_jc_acfml_safety_fallback',
                'title'    => 'ACFML safety fallback',
                'fields'   => [],
                'location' => [],
                'active'   => false,
            ];
        }
    }

    Justccell_ACFML_Safety::boot();
}
