#!/usr/bin/env php
<?php
/**
 * Update Yoast SEO fields for Tonic Physio pages
 * Run via: curl -X POST https://tonicphysio.com/wp-content/tonic-yoast-update.php
 */

// Load WordPress
require_once dirname(dirname(dirname(__FILE__))) . '/wp-load.php';

// Verify authentication (simple nonce or key check could be added here)
if (!current_user_can('edit_pages')) {
    // Try to authenticate via application password
    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
        header('WWW-Authenticate: Basic realm="WordPress"');
        header('HTTP/1.0 401 Unauthorized');
        die('Unauthorized');
    }
    
    $user = wp_authenticate($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
    if (is_wp_error($user) || !current_user_can('edit_pages', $user)) {
        header('HTTP/1.0 403 Forbidden');
        die('Forbidden');
    }
}

header('Content-Type: application/json');

$pages = [
    [
        'id' => 11603,
        'name' => 'B-Pulse Pelvic Floor',
        'title' => 'B-Pulse Pelvic Floor Strengthening Milton | Tonic Physio',
        'description' => 'B-Pulse pelvic floor strengthening in Milton at Tonic Physio. Expert treatment for postpartum recovery, incontinence & pelvic pain. Book consultation.'
    ],
    [
        'id' => 6971,
        'name' => 'Joint Pain and Stiffness',
        'title' => 'Joint Pain Treatment Milton | Tonic Physio',
        'description' => 'Relieve joint pain and stiffness in Milton at Tonic Physio. Expert physiotherapy for arthritis, injury & chronic pain. Book your appointment.'
    ],
    [
        'id' => 1791,
        'name' => 'Orthopedic Physiotherapy',
        'title' => 'Orthopedic Physiotherapy Milton | Tonic Physio',
        'description' => 'Expert orthopedic physiotherapy in Milton at Tonic Physio. Joint & muscle rehab, post-surgery recovery & pain relief. Book assessment today.'
    ],
    [
        'id' => 1793,
        'name' => 'Pediatric Physiotherapy',
        'title' => 'Pediatric Physiotherapy Milton | Tonic Physio',
        'description' => 'Pediatric physiotherapy in Milton at Tonic Physio. Expert care for children with developmental delays, injuries & mobility issues. Book now.'
    ],
    [
        'id' => 6587,
        'name' => 'Hot Stone Massage',
        'title' => 'Hot Stone Massage Milton | Tonic Physio',
        'description' => 'Hot stone massage in Milton at Tonic Physio. Therapeutic heat therapy for muscle tension, stress relief & relaxation. Book your session.'
    ]
];

$results = [];

foreach ($pages as $page) {
    $post_id = $page['id'];
    $post = get_post($post_id);
    
    if (!$post) {
        $results[] = [
            'id' => $post_id,
            'name' => $page['name'],
            'success' => false,
            'error' => 'Page not found'
        ];
        continue;
    }
    
    // Update Yoast SEO meta fields
    update_post_meta($post_id, '_yoast_wpseo_focuskw', '');
    update_post_meta($post_id, '_yoast_wpseo_title', $page['title']);
    update_post_meta($post_id, '_yoast_wpseo_metadesc', $page['description']);
    
    // Clear Yoast's primary term cache if exists
    delete_post_meta($post_id, '_yoast_wpseo_primary_focus_keyword');
    
    // Update the post modified time
    wp_update_post([
        'ID' => $post_id,
        'post_modified' => current_time('mysql'),
        'post_modified_gmt' => current_time('mysql', 1)
    ]);
    
    // Verify the update
    $saved_title = get_post_meta($post_id, '_yoast_wpseo_title', true);
    $saved_desc = get_post_meta($post_id, '_yoast_wpseo_metadesc', true);
    
    $results[] = [
        'id' => $post_id,
        'name' => $page['name'],
        'success' => ($saved_title === $page['title'] && $saved_desc === $page['description']),
        'saved_title' => $saved_title,
        'saved_description' => $saved_desc,
        'expected_title' => $page['title'],
        'expected_description' => $page['description']
    ];
}

echo json_encode(['results' => $results, 'timestamp' => current_time('mysql')]);
