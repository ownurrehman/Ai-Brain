<?php
/**
 * Plugin Name: Justccell ACFML Safety Net
 * Description: Guarantees the acf/load_field_group filter never hands a non-array (e.g. false) to ACFML (WPML). Prevents the admin white-screen fatal "ACFML\Strings\Traversable\Entity::__construct(): Argument #1 ($data) must be of type array, false given" on Pages/Products edit screens. Defense-in-depth; logs any violation so the real hide-logic can be fixed. Hide field groups via acf/location/rule_match, never by returning false.
 * Version: 1.0.0
 * Author: Rank Ray
 * Author URI: https://rankray.com
 *
 * WHY THIS EXISTS
 * Any code that returns false from `acf/load_field_group` (a common trick to hide a
 * group on some screens) fatals under current ACFML, which enumerates *all* field
 * groups during admin bootstrap and requires each to be an array. One false = every
 * edit screen (pages AND products) white-screens. This plugin makes that failure mode
 * impossible: if any filter returns a non-array, we restore the last-known-good group
 * array and log it, instead of letting the site crash.
 *
 * This is site infrastructure. Keep it ACTIVE. Correct way to scope groups: Local JSON
 * location rules or `acf/location/rule_match` (returning true/false there is safe).
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Justccell_ACFML_Safety')) {
    /**
     * Late-priority guard around acf/load_field_group.
     */
    final class Justccell_ACFML_Safety
    {
        /** @var array<string, array<string, mixed>> Last-known-good group, keyed by group key. */
        private static array $cache = [];

        /** @var string|null Key of the group currently moving through the filter chain. */
        private static ?string $current_key = null;

        /** @var bool Whether we already logged this request (avoid log spam). */
        private static bool $logged = false;

        public static function boot(): void
        {
            // Runs FIRST (lowest priority): capture the genuine array before any other filter can null it.
            add_filter('acf/load_field_group', [self::class, 'capture'], PHP_INT_MIN);
            // Runs LAST (highest priority): guarantee an array is returned to ACF/ACFML.
            add_filter('acf/load_field_group', [self::class, 'guard'], PHP_INT_MAX);
        }

        /**
         * @param mixed $group
         * @return mixed
         */
        public static function capture($group)
        {
            if (is_array($group) && isset($group['key']) && is_string($group['key'])) {
                self::$current_key          = $group['key'];
                self::$cache[$group['key']] = $group;
            }
            return $group;
        }

        /**
         * @param mixed $group
         * @return mixed Always an array when we can recover one.
         */
        public static function guard($group)
        {
            if (is_array($group)) {
                return $group;
            }

            // A theme/plugin filter returned a non-array (usually false to "hide" a group).
            // Restore the last-known-good array for the group currently being loaded.
            $restored = (self::$current_key !== null && isset(self::$cache[self::$current_key]))
                ? self::$cache[self::$current_key]
                : null;

            if (!self::$logged && function_exists('error_log')) {
                self::$logged = true;
                error_log(sprintf(
                    '[jc-acfml-safety] acf/load_field_group returned %s for group "%s" — restored to prevent ACFML fatal. Hide groups via acf/location/rule_match, not by returning false.',
                    gettype($group),
                    (string) self::$current_key
                ));
            }

            return $restored !== null ? $restored : $group;
        }
    }

    Justccell_ACFML_Safety::boot();
}
