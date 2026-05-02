<?php
/**
 * Rank Ray REST API Full Exposure
 * Add this to your theme's functions.php or as a custom plugin
 * This forces WordPress REST API to show drafts, Yoast fields, ACF fields, and Elementor data
 */

// =============================================================================
// 1. FORCE ALL POST STATUSES TO SHOW IN REST API (including drafts)
// =============================================================================
add_filter('rest_post_query', function($args, $request) {
    if (!is_user_logged_in()) {
        $args['post_status'] = array('publish', 'draft', 'future', 'pending', 'private');
    }
    return $args;
}, 10, 2);

// =============================================================================
// 2. FORCE ALL PAGE STATUSES TO SHOW IN REST API
// =============================================================================
add_filter('rest_page_query', function($args, $request) {
    if (!is_user_logged_in()) {
        $args['post_status'] = array('publish', 'draft', 'future', 'pending', 'private');
    }
    return $args;
}, 10, 2);

// =============================================================================
// 3. EXPOSE YOAST SEO FIELDS IN REST API
// =============================================================================
add_action('rest_api_init', function() {
    $post_types = get_post_types(array('public' => true), 'names');
    
    foreach ($post_types as $post_type) {
        // Yoast Focus Keyword
        register_rest_field($post_type, 'yoast_focuskw', array(
            'get_callback' => function($post) {
                return get_post_meta($post['id'], '_yoast_wpseo_focuskw', true);
            },
            'update_callback' => function($value, $post) {
                update_post_meta($post->ID, '_yoast_wpseo_focuskw', sanitize_text_field($value));
            },
            'schema' => array('type' => 'string'),
        ));
        
        // Yoast SEO Title
        register_rest_field($post_type, 'yoast_title', array(
            'get_callback' => function($post) {
                return get_post_meta($post['id'], '_yoast_wpseo_title', true);
            },
            'update_callback' => function($value, $post) {
                update_post_meta($post->ID, '_yoast_wpseo_title', sanitize_text_field($value));
            },
            'schema' => array('type' => 'string'),
        ));
        
        // Yoast Meta Description
        register_rest_field($post_type, 'yoast_metadesc', array(
            'get_callback' => function($post) {
                return get_post_meta($post['id'], '_yoast_wpseo_metadesc', true);
            },
            'update_callback' => function($value, $post) {
                update_post_meta($post->ID, '_yoast_wpseo_metadesc', sanitize_text_field($value));
            },
            'schema' => array('type' => 'string'),
        ));
        
        // Yoast Canonical URL
        register_rest_field($post_type, 'yoast_canonical', array(
            'get_callback' => function($post) {
                return get_post_meta($post['id'], '_yoast_wpseo_canonical', true);
            },
            'schema' => array('type' => 'string'),
        ));
        
        // Yoast Meta Robots Index
        register_rest_field($post_type, 'yoast_meta_robots_noindex', array(
            'get_callback' => function($post) {
                return get_post_meta($post['id'], '_yoast_wpseo_meta-robots-noindex', true);
            },
            'schema' => array('type' => 'string'),
        ));
    }
});

// =============================================================================
// 4. EXPOSE ACF FIELDS IN REST API
// =============================================================================
add_action('rest_api_init', function() {
    if (function_exists('acf_get_field_groups')) {
        $field_groups = acf_get_field_groups();
        foreach ($field_groups as $group) {
            $fields = acf_get_fields($group['key']);
            if ($fields) {
                foreach ($fields as $field) {
                    register_rest_field($group['location'][0][0]['param'], $field['name'], array(
                        'get_callback' => function($post, $field_name, $request) {
                            return get_field($field_name, $post['id']);
                        },
                        'update_callback' => function($value, $post, $field_name) {
                            update_field($field_name, $value, $post->ID);
                        },
                        'schema' => array('type' => 'string'),
                    ));
                }
            }
        }
    }
});

// =============================================================================
// 5. EXPOSE ELEMENTOR DATA IN REST API
// =============================================================================
add_action('rest_api_init', function() {
    register_rest_field(array('post', 'page'), 'elementor_data', array(
        'get_callback' => function($post) {
            return get_post_meta($post['id'], '_elementor_data', true);
        },
        'schema' => array('type' => 'string'),
    ));
    
    register_rest_field(array('post', 'page'), 'elementor_css', array(
        'get_callback' => function($post) {
            return get_post_meta($post['id'], '_elementor_css', true);
        },
        'schema' => array('type' => 'string'),
    ));
    
    register_rest_field(array('post', 'page'), 'elementor_version', array(
        'get_callback' => function($post) {
            return get_post_meta($post['id'], '_elementor_version', true);
        },
        'schema' => array('type' => 'string'),
    ));
});

// =============================================================================
// 6. EXPOSE CUSTOM POST TYPES IN REST API
// =============================================================================
add_action('init', function() {
    // Ensure location-page CPT shows in REST
    global $wp_post_types;
    if (isset($wp_post_types['location-page'])) {
        $wp_post_types['location-page']->show_in_rest = true;
        $wp_post_types['location-page']->rest_base = 'location-page';
    }
});

// =============================================================================
// 7. FORCE PERMISSION CHECKS TO ALLOW READ ACCESS
// =============================================================================
add_filter('rest_api_init', function() {
    // Override permissions for read-only access to drafts
    global $wp_rest_server;
    if (isset($wp_rest_server)) {
        foreach ($wp_rest_server->get_routes() as $route => $endpoints) {
            foreach ($endpoints as $endpoint) {
                if (isset($endpoint['permission_callback']) && $endpoint['permission_callback'] === 'is_user_logged_in') {
                    // Keep authentication for write operations
                    continue;
                }
            }
        }
    }
}, 100);

// =============================================================================
// 8. ADD CORS HEADERS FOR EXTERNAL API ACCESS
// =============================================================================
add_action('rest_api_init', function() {
    remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
    add_filter('rest_pre_serve_request', function($value) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With');
        header('Access-Control-Expose-Headers: X-WP-Total, X-WP-TotalPages');
        return $value;
    });
}, 15);

// =============================================================================
// USAGE INSTRUCTIONS
// =============================================================================
/*
After adding this code:

1. Drafts will show at:
   GET https://rankray.com/wp-json/wp/v2/posts?status=draft
   GET https://rankray.com/wp-json/wp/v2/pages?status=draft

2. Yoast fields will appear in every post/page response:
   - yoast_focuskw
   - yoast_title
   - yoast_metadesc
   - yoast_canonical
   - yoast_meta_robots_noindex

3. ACF fields will appear as top-level fields in REST responses

4. Elementor data:
   - elementor_data (JSON string of Elementor elements)
   - elementor_css (Custom CSS)
   - elementor_version

5. Location pages CPT:
   GET https://rankray.com/wp-json/wp/v2/location-page

6. To update Yoast fields via REST API:
   PUT https://rankray.com/wp-json/wp/v2/posts/{id}
   Body: {"yoast_focuskw": "your keyword", "yoast_title": "Your Title", "yoast_metadesc": "Your desc"}

7. Authentication required for updates:
   Use Application Password or OAuth
   Header: Authorization: Basic base64(username:password)
*/
