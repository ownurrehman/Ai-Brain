<?php
/**
 * Per-page ACF groups. Each screen only lists fields that render on that URL.
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Legal page group — Local JSON + DB only (Phase 3 Batch 1).
 * @see acf-json/group_jc_legal_pages.json
 */
function justccell_register_acf_legal_pages(): void
{
    // Intentionally empty: group_jc_legal_pages loads from acf-json/ + wp-admin DB.
}
