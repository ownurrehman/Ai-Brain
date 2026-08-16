<?php
/**
 * Template part for displaying header style 1
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( is_archive() || is_single() ) {
    $innerpage = "inner-topbar";
} else {
    $innerpage = '';
}

/**
 * Detect current language safely
 */
$lang = (defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE) ? ICL_LANGUAGE_CODE : 'en';
$label_lang = ( function_exists( 'coinsfera_staff_session_present' ) && coinsfera_staff_session_present() ) ? 'en' : $lang;

/**
 * Helper function for localized city labels
 */
function coinsfera_city_label($city_title, $lang) {
    $city = trim($city_title);

    if ($lang === 'ru') {
        return 'в ' . $city;
    }

    if ($lang === 'tr') {
        $normalized = mb_strtolower($city, 'UTF-8');
        // Handle Istanbul special case
        if (in_array($normalized, ['istanbul', 'i̇stanbul'], true)) {
            return "İstanbul'da";
        }
        return $city;
    }

    // Default English
    return sprintf('%s %s', __('In', 'coinsfera'), $city);
}
?>

<header class="topbar <?php echo esc_attr($innerpage); ?>">
    <div class="container">
        <!-- Start Header -->
        <nav class="navbar navbar-expand-md navbar-light top-navbar p-0">
            <div class="navbar-header d-flex">
                <?php
                // START OF LOGO SECTION
                if ( get_theme_mod( 'custom_logo' ) ) {
                    $custom_logo_id = get_theme_mod( 'custom_logo' );
                    $custom_logo_url = wp_get_attachment_image_src( $custom_logo_id , 'full' ); ?>
                    <a class="navbar-brand pt-0 mt-n1" href="<?php echo home_url( '/' ); ?>">
                        <img src="<?php echo esc_url( $custom_logo_url[0] ); ?>" class="logo" alt="Coinsfera logo" width="182" height="41">
                    </a>
                <?php } else { ?>
                    <a class="navbar-brand text-primary font-medium" href="<?php echo home_url( '/' ); ?>">
                        <?php echo get_bloginfo( 'name' ); ?>
                    </a>
                <?php } ?>
                <!-- END OF LOGO SECTION -->

                <button class="navbar-toggler border-0" type="button" data-toggle="collapse"
                    data-target="#collapsible-menu" aria-controls="collapsible-menu" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <div class="hamburger">
                        <div class="hamburger-top"></div>
                        <div class="hamburger-bottom"></div>
                    </div>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="collapsible-menu">

                <!-- START OF DESKTOP MENU -->
                <?php
                $menu_items = wp_get_nav_menu_items("coinsfera-menu");
                if( $menu_items ) { ?>
                <div class="d-none d-lg-block">
                    <ul class="navbar-nav custom-main-menu desktop-menu mr-auto">
                        <?php
                        for($i=0;$i<sizeof($menu_items);$i++){
                            $count = 0;
                            $item = $menu_items[$i];
                            $next_item = isset($menu_items[$i+1]) ? $menu_items[$i+1] : null;
                            $menu_name = $item->post_title;

                            if(!$next_item || $next_item->menu_item_parent!=$item->ID){
                                $menu_link = $item->url; ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo esc_url($menu_link); ?>"><?php echo $menu_name; ?></a>
                                </li>
                            <?php } else { ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="javascript:void(0)"><?php echo $menu_name; ?>
                                        <i class="fas fa-angle-down"></i>
                                    </a>
                                    <div class="dropdown-menu">
                                        <div class="d-flex">
                                            <div class="nav custom-tab-menu flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                                <?php
                                                for($j=$i+1;$j<sizeof($menu_items);$j++){
                                                    $prev_item = $menu_items[$i];
                                                    $item = $menu_items[$j];
                                                    $prev_menu_name = $prev_item->post_title;
                                                    $menu_name = $item->post_title;
                                                    if($item->menu_item_parent==$prev_item->ID){
                                                        $menu_link = $item->url; ?>
                                                        <a class="nav-link" id="<?php echo $menu_name; ?>-tab" data-toggle="tab" href="#<?php echo $prev_menu_name.$menu_name; ?>" role="tab" aria-controls="<?php echo $menu_name; ?>" aria-selected="true"><?php echo $menu_name; ?></a>
                                                <?php } } ?>
                                            </div>
                                            <div class="tab-content w-100" id="custom-tabcontent">
                                                <?php
                                                for($k=$i+1;$k<sizeof($menu_items);$k++){
                                                    $prev_item = $menu_items[$i];
                                                    $item = $menu_items[$k];
                                                    $prev_menu_name = $prev_item->post_title;
                                                    $menu_name = $item->post_title;
                                                    if($item->menu_item_parent==$prev_item->ID){ $count++; ?>
                                                        <div class="tab-pane h-100 fade show" id="<?php echo $prev_menu_name.$menu_name; ?>" role="tabpanel" aria-labelledby="<?php echo $menu_name; ?>-tab">
                                                            <div class="row no-gutters h-100">
                                                                <?php
                                                                for($l=$k+1;$l<sizeof($menu_items);$l++){
                                                                    $prev_item = $menu_items[$k];
                                                                    $item = $menu_items[$l];
                                                                    $prev_menu_name = $prev_item->post_title;
                                                                    $menu_name = $item->post_title;
                                                                    if($item->menu_item_parent==$prev_item->ID){
                                                                        $count++;
                                                                        $menu_link = $item->url;
                                                                        $icon = get_field('custom_main_menu_image', $item); ?>
                                                                        <div class="col-md-4 custom-sub-menu-item">
                                                                            <a class="dropdown-item" href="<?php echo $menu_link; ?>">
                                                                                <div class="d-flex align-items-center">
                                                                                    <img alt="<?php echo esc_attr($menu_name); ?>" src="<?php echo esc_url($icon); ?>" />
                                                                                    <div class="sub-menu-name1">
                                                                                        <span class="ml-2 d-block"><?php echo $menu_name; ?></span>
                                                                                        <span class="font-12 ml-2"><?php echo esc_html( coinsfera_city_label($prev_menu_name, $label_lang) ); ?></span>
                                                                                    </div>
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                <?php } } ?>
                                                            </div>
                                                        </div>
                                                <?php } } ?>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php } $i=$i+$count;
                        } ?>
                    </ul>
                </div>
                <?php } ?>
                <!-- END OF DESKTOP MENU -->

                <!-- START OF MOBILE MENU -->
                <div class="d-block d-lg-none">
                    <ul class="navbar-nav mobile-menu custom-main-menu mr-auto">
                        <?php
                        for($i=0;$i<sizeof($menu_items);$i++){
                            $item = $menu_items[$i];
                            $next_item = isset($menu_items[$i+1]) ? $menu_items[$i+1] : null;
                            $menu_name = $item->post_title;

                            if($item->menu_item_parent==0){
                                if(!$next_item || $next_item->menu_item_parent!=$item->ID){
                                    $menu_link = $item->url; ?>
                                    <li class="nav-item"><a class="nav-link" href="<?php echo esc_url($menu_link); ?>"><?php echo $menu_name; ?></a></li>
                                <?php } else { ?>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="javascript:void(0)"><?php echo $menu_name; ?><i class="fas fa-angle-down"></i></a>
                                        <ul class="dropdown-menu">
                                            <?php
                                            for($j=$i+1;$j<sizeof($menu_items);$j++){
                                                $next_item = isset($menu_items[$j]) ? $menu_items[$j] : null;
                                                if(!$next_item || $next_item->menu_item_parent!=$item->ID) break;
                                                $next_menu_name = $next_item->post_title;
                                                $menu_link = $next_item->url;
                                                $has_children = false;
                                                for($k=$j+1;$k<sizeof($menu_items);$k++){
                                                    if($menu_items[$k]->menu_item_parent == $next_item->ID){ $has_children = true; break; }
                                                }
                                                if(!$has_children){ ?>
                                                    <li class="nav-item"><a class="nav-link" href="<?php echo esc_url($menu_link); ?>"><?php echo $next_menu_name; ?></a></li>
                                                <?php } else { ?>
                                                    <li class="nav-item dropdown">
                                                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="javascript:void(0)"><?php echo $next_menu_name; ?><i class="fas fa-angle-down"></i></a>
                                                        <ul class="dropdown-menu dropdown-submenu">
                                                            <?php
                                                            for($k=$j+1;$k<sizeof($menu_items);$k++){
                                                                $last_item = $menu_items[$k];
                                                                if($last_item->menu_item_parent != $next_item->ID) break;
                                                                $last_menu_name = $last_item->post_title;
                                                                $last_menu_link = $last_item->url;
                                                                $icon = get_field('custom_main_menu_image', $last_item); ?>
                                                                <li class="nav-item">
                                                                    <a class="dropdown-item" href="<?php echo $last_menu_link; ?>">
                                                                        <div class="d-flex align-items-center">
                                                                            <img src="<?php echo esc_url($icon); ?>" alt="<?php echo esc_attr($last_menu_name); ?>" />
                                                                            <div class="sub-menu-name1">
                                                                                <span class="ml-2 d-block"><?php echo $last_menu_name; ?></span>
                                                                                <span class="font-12 ml-2"><?php echo esc_html( coinsfera_city_label($next_menu_name, $label_lang) ); ?></span>
                                                                            </div>
                                                                        </div>
                                                                    </a>
                                                                </li>
                                                            <?php } ?>
                                                        </ul>
                                                    </li>
                                                <?php
                                                    while(isset($menu_items[$j+1]) && $menu_items[$j+1]->menu_item_parent == $next_item->ID){ $j++; }
                                                }
                                            } ?>
                                        </ul>
                                    </li>
                                <?php }
                            }
                        } ?>
                    </ul>
                </div>
                <!-- END OF MOBILE MENU -->

                <!-- START OF LANGUAGE SWITCHER AND CONTACT BUTTON -->
                <div class="language-switcher ml-auto">
                    <?php echo do_shortcode('[wpml_language_selector_widget]'); ?>
                </div>

                <?php
                // Language-based Contact Button
                $contact_btn_data = [
                    'en' => ['label' => 'Contact Us', 'url' => home_url('/contact-us/')],
                    'ru' => ['label' => 'Связаться с нами', 'url' => home_url('/ru/contact-us/')],
                    'tr' => ['label' => 'Bize Ulaşın', 'url' => home_url('/tr/contact-us/')],
                ];
                $label = $contact_btn_data[$label_lang]['label'] ?? 'Contact Us';
                $url   = $contact_btn_data[$lang]['url'] ?? home_url('/contact-us/');
                ?>
                <a href="<?= esc_url($url) ?>" class="btn btn-outline-warning font-circular-medium btn-contact">
                    <?= esc_html($label) ?>
                </a>
                <!-- END OF LANGUAGE SWITCHER AND CONTACT BUTTON -->
            </div>
        </nav>
        <!-- End Header -->
    </div>
</header>