<?php
/**
 * Plugin Name: JC ACF Guard
 * Description: Caps oversized ACF repeater values to prevent memory fatals on justccell.com.
 * Version: 1.0.0
 * Author: Rank Ray
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @param mixed $value
 * @return mixed
 */
function jc_acf_guard_cap_repeater($value, int $max_rows = 30)
{
    if (!is_array($value)) {
        return $value;
    }
    if (count($value) <= $max_rows) {
        return $value;
    }

    return array_slice($value, 0, $max_rows);
}

foreach (['clone_features', 'clone_details', 'clone_specs'] as $field_name) {
    add_filter('acf/load_value/name=' . $field_name, static function ($value) use ($field_name) {
        $max = $field_name === 'clone_features' ? 30 : 40;
        return jc_acf_guard_cap_repeater($value, $max);
    }, 1);

    add_filter('acf/format_value/name=' . $field_name, static function ($value) use ($field_name) {
        $max = $field_name === 'clone_features' ? 30 : 40;
        return jc_acf_guard_cap_repeater($value, $max);
    }, 1);
}
