<?php
/**
 * Template part for displaying footer style 1
 *
 * @package Coinsfera_WordPress_Theme
 */
?>

<footer class="footer">
    <div class="footer-bg">
        <div class="container">
            <div class="footer-navs">
                <div class="row">

                    <!-- =========================
                         Footer Column 1 (Logo + Desc + Button + Social)
                    ========================== -->
                    <div class="col-lg-3">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                            <?php
                            $footer_logo = get_theme_mod( 'coinsfera_footer_logo' );
                            if ( $footer_logo ) {
                                $attachment_id = attachment_url_to_postid( $footer_logo );
                                $alt_text = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
                                if ( empty( $alt_text ) ) {
                                    $alt_text = get_bloginfo( 'name' );
                                }
                                echo '<img src="' . esc_url( $footer_logo ) . '" alt="' . esc_attr( $alt_text ) . '" loading="lazy" />';
                            }
                            ?>
                        </a>

                        <?php
                        // === Footer description (translatable)
                        $footer_desc = get_theme_mod( 'coinsfera_footer_desc' );
                        if ( function_exists( 'icl_t' ) ) {
                            $footer_desc = icl_t( 'Coinsfera Footer', 'Footer Description', $footer_desc );
                        }
                        if ( $footer_desc ) {
                            echo '<p class="footer-subtext">' . wp_kses_post( $footer_desc ) . '</p>';
                        }
                        ?>

                        <div class="mt-3">
                            <?php
                            // === Footer button (translatable)
                            $footer_btn_lbl  = get_theme_mod( 'coinsfera_footer_btn_lbl' );
                            $footer_btn_link = get_theme_mod( 'coinsfera_footer_btn_link' );

                            if ( function_exists( 'icl_t' ) ) {
                                $footer_btn_lbl  = icl_t( 'Coinsfera Footer', 'Footer Button Label', $footer_btn_lbl );
                                $footer_btn_link = icl_t( 'Coinsfera Footer', 'Footer Button Link', $footer_btn_link );
                            }

                            if ( $footer_btn_lbl ) {
                                echo '<a href="' . esc_url( $footer_btn_link ) . '" class="text-warning bottom-border-links">' . esc_html( $footer_btn_lbl ) . '</a>';
                            }
                            ?>
                        </div>

                        <div class="social_icons">
                            <ul class="list_social_icons">
                                <?php
                                $socials = [
                                    'coinsfera_footer_fb'       => 'facebook-f',
                                    'coinsfera_footer_tw'       => 'twitter',
                                    'coinsfera_footer_instagram'=> 'instagram',
                                    'coinsfera_footer_lnkin'    => 'linkedin',
                                    'coinsfera_footer_yt'       => 'youtube',
                                    'coinsfera_footer_medium'   => 'medium',
                                    'coinsfera_footer_reddit'   => 'reddit',
                                ];

                                foreach ( $socials as $key => $icon ) {
                                    $link = get_theme_mod( $key );
                                    if ( $link ) {
                                        echo '<li><a href="' . esc_url( $link ) . '" target="_blank" rel="noopener noreferrer"><i class="fab fa-' . esc_attr( $icon ) . '"></i></a></li>';
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    </div>

                    <!-- =========================
                         Footer Column 2 (Main Menu)
                    ========================== -->
                    <div class="col-lg-6 col-md-4 mt-4 mt-lg-0">
                        <div class="d-flex align-items-center footer-collapse mt-2">
                            <?php
                            $footer_menu_title = get_theme_mod( 'coinsfera_footer_menu_title' );
                            if ( function_exists( 'icl_t' ) ) {
                                $footer_menu_title = icl_t( 'Coinsfera Footer', 'Footer Menu Title', $footer_menu_title );
                            }
                            if ( $footer_menu_title ) {
                                echo '<h4 class="footer-list-title mb-0">' . esc_html( $footer_menu_title ) . '</h4>';
                            }
                            ?>
                        </div>

                        <?php
                        if ( has_nav_menu( 'footer-menu' ) ) {
                            wp_nav_menu([
                                'theme_location' => 'footer-menu',
                                'menu_id'        => 'footer-menu',
                                'container'      => 'ul',
                                'menu_class'     => 'list-unstyled footer-list footer-menu',
                            ]);
                        }
                        ?>
                    </div>

                    <!-- =========================
                         Footer Column 3 (Quick Links)
                    ========================== -->
                    <div class="col-lg-3 col-md-4 mt-0 mt-md-4 mt-lg-0">
                        <div class="d-flex align-items-center footer-collapse mt-2">
                            <?php
                            $quick_link_menu_title = get_theme_mod( 'coinsfera_footer_quick_links_menu' );
                            if ( function_exists( 'icl_t' ) ) {
                                $quick_link_menu_title = icl_t( 'Coinsfera Footer', 'Quick Links Menu Title', $quick_link_menu_title );
                            }
                            if ( $quick_link_menu_title ) {
                                echo '<h4 class="footer-list-title mb-0">' . esc_html( $quick_link_menu_title ) . '</h4>';
                            }
                            ?>
                        </div>

                        <?php
                        if ( has_nav_menu( 'quick-link-menu' ) ) {
                            wp_nav_menu([
                                'theme_location' => 'quick-link-menu',
                                'menu_id'        => 'quick-link-menu',
                                'container'      => 'ul',
                                'menu_class'     => 'list-unstyled footer-menu footer-list quick-link-menu',
                            ]);
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- =========================
                 Footer Bottom (Copyright + Bottom Menu)
            ========================== -->
            <div class="footer-copyright">
                <div class="row">
                    <div class="col-md-6">
                        <?php
                        $copyright_text = get_theme_mod( 'coinsfera_copyright_text' );

                        if ( function_exists( 'icl_t' ) ) {
                            $copyright_text = icl_t( 'Coinsfera Footer', 'Footer Copyright Text', $copyright_text );
                        }

                        if ( $copyright_text ) {
                            echo '<p class="mb-0 text-center text-md-left">' . wp_kses_post( $copyright_text ) . '</p>';
                        }
                        ?>
                    </div>

                    <div class="col-md-6">
                        <?php
                        if ( has_nav_menu( 'footer-bottom-menu' ) ) {
                            wp_nav_menu([
                                'theme_location' => 'footer-bottom-menu',
                                'menu_id'        => 'footer-bottom-menu',
                                'container'      => 'ul',
                                'menu_class'     => 'list-unstyled copyright-list footer-bottom-menu',
                            ]);
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>