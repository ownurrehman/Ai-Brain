<?php
/**
 * Custom template tags for this theme
 */

/**
 * Open content wrapper in header html
 */
if ( ! function_exists( 'coinsfera_open_content_wrapper' ) ) {

    function coinsfera_open_content_wrapper() {

        $title    = get_the_title();
        $subtitle = get_post_meta( get_the_ID(), 'coinsfera_seo_subtitle', true );

        if ( is_page() && empty( $subtitle ) ) {
            $subtitle = __( 'Page', 'coinsfera' );
        }

        if ( is_single() && empty( $subtitle ) ) {
            $subtitle = __( 'Post', 'coinsfera' );
        }

        if ( ! is_single() && empty( $subtitle ) ) {
            $subtitle = __( 'Blog', 'coinsfera' );
        }

        if ( is_home() ) {
            $title = single_post_title( '', false );
            if ( empty( $subtitle ) ) {
                $subtitle = __( 'Blog', 'coinsfera' );
            }

            if ( is_front_page() ) {
                $title = __( 'Blog', 'coinsfera' );
            }
        }

        if ( is_archive() ) {
            // FIXED: Clean up all archive prefixes (Category, Tag, Tax, and Author)
            if ( is_category() || is_tag() || is_tax() ) {
                $title = single_term_title( '', false );
            } elseif ( is_author() ) {
                $title = get_the_author(); // Safely gets just the author's name
            } else {
                $title = get_the_archive_title();
            }

            $subtitle = __( 'Archive', 'coinsfera' );
        }

        if ( is_search() ) {
            global $wp_query;
            $search_count = $wp_query->found_posts;
            $title        = $search_count . __( ' result found for search \'', 'coinsfera' ) . get_search_query() . '\'';
            $subtitle     = __( 'Search', 'coinsfera' );
        }

        if ( is_404() ) {
            $title    = __( 'Oops! That page can&rsquo;t be found.', 'coinsfera' );
            $subtitle = __( 'Page not found', 'coinsfera' );
        }

        if ( empty( $subtitle ) ) {
            $subtitle = __( 'Blog', 'coinsfera' );
        }

        // START OUTPUT
        $html = '';

        $html .= '<section>
            <div class="inner-page-breadcrumbs">
                <div class="container">';

        $justify = is_single() ? 'justify-content-center' : '';

        $html .= '<div class="row align-items-center mt-7 ' . $justify . '">';

        // col width (fixes left spacing issue)
        $col_class = is_single() ? 'col-md-12' : 'col-md-8';
        $html .= '<div class="' . $col_class . '">';

        // === ALWAYS USE RANKMATH BREADCRUMBS ===
        if ( ! is_single() && function_exists( 'rank_math_the_breadcrumbs' ) ) {
            ob_start();
            rank_math_the_breadcrumbs();
            $breadcrumbs_html = ob_get_clean();

            $html .= '<nav class="rm-breadcrumb-wrapper mb-3" aria-label="breadcrumb">'
                        . $breadcrumbs_html .
                     '</nav>';
        }

        // === BULLETPROOF TITLE DISPLAY LOGIC ===
        $hide_title = false;
        
        // Hide the hardcoded theme title on these specific views so Elementor can take over:
        if ( is_single() || is_category() || is_author() || is_tag() ) {
            $hide_title = true;
        }

        if ( ! $hide_title ) {

            if ( is_category( 'blogs' ) ) {
                $header_title = __( 'Blogs', 'coinsfera' );
            } elseif ( is_category( 'news' ) ) {
                $header_title = __( 'News', 'coinsfera' );
            } else {
                $header_title = $title;
            }

            $html .= '<nav class="mb-4 breadcrumb-title"><h1>' . $header_title . '</h1></nav>';
        }

        $html .= '</div></div></div></div></section>';

        // === CONTENT WRAPPER START ===
        $html .= '
        <section class="content blog-details blog-listing-section">
            <div class="container">
                <div class="row justify-content-center">';

        echo $html;
    }
}

/**
 * Close content wrapper in footer html
 */
if ( ! function_exists( 'coinsfera_close_content_wrapper' ) ) {

    function coinsfera_close_content_wrapper() {

        $html = '</div></div></section>';

        echo $html;
    }
}

/**
 * Display pagination in theme html format
 */
if ( ! function_exists( 'coinsfera_posts_pagination' ) ) {

    function coinsfera_posts_pagination() {

        $args = array(
            'screen_reader_text' => ' ',
            'type'               => 'list',
            'prev_text'          => '<i class="fa fas fa-chevron-left" aria-hidden="true"></i>',
            'next_text'          => '<i class="fas fa-chevron-right" aria-hidden="true"></i>',
        );

        echo '<div id="pagination" class="col-12 d-flex text-center" aria-label="Pagination">'
                . get_the_posts_pagination( $args ) .
             '</div>';
    }
}
?>