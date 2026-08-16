/**
 * Coinsfera Global AJAX Crypto Engine
 * Handles live card rates AND the inner banner calculator using a single centralized API key and caching layer.
 */

if ( ! defined( 'COINSFERA_CRYPTOCOMPARE_API_KEY' ) ) {
    define( 'COINSFERA_CRYPTOCOMPARE_API_KEY', 'b4d8981c35783fe6eb105168f3cee8998d47e82385f136924b4b549162f9687a' );
}

// Register endpoints
add_action( 'wp_ajax_getCurrencyData', 'coinsfera_core_ajax_market_feed' );
add_action( 'wp_ajax_nopriv_getCurrencyData', 'coinsfera_core_ajax_market_feed' );

add_action( 'wp_ajax_getSingleCoinPrice', 'coinsfera_calculator_ajax_feed' );
add_action( 'wp_ajax_nopriv_getSingleCoinPrice', 'coinsfera_calculator_ajax_feed' );

// Expose admin-ajax URL to the front-end securely
add_action( 'wp_head', function() {
    echo '<script type="text/javascript">var coinsfera_ajax_url = "' . esc_url( admin_url( 'admin-ajax.php' ) ) . '";</script>';
});

// 1. CARDS / WIDGETS ENDPOINT (Cached for 1 minute)
function coinsfera_core_ajax_market_feed() {
    $cryptocurrency = isset( $_REQUEST['cryptocurrency'] ) && is_array( $_REQUEST['cryptocurrency'] ) ? $_REQUEST['cryptocurrency'] : [];

    if ( empty( $cryptocurrency ) ) {
        wp_send_json_error( 'Invalid or missing cryptocurrency data.' );
    }

    $fsyms = implode( ',', array_map( 'sanitize_text_field', $cryptocurrency ) );
    $transient_key = 'coinsfera_market_feed_' . md5( $fsyms );
    $result = get_transient( $transient_key );

    if ( false === $result ) {
        $api_url = 'https://min-api.cryptocompare.com/data/pricemultifull?fsyms=' . $fsyms . '&tsyms=USD&api_key=' . COINSFERA_CRYPTOCOMPARE_API_KEY;
        $response = wp_remote_get( $api_url );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( 'External API unreachable.' );
        }

        $json = json_decode( wp_remote_retrieve_body( $response ) );
        $result = [];

        if ( ! empty( $json->RAW ) ) {
            foreach ( $cryptocurrency as $val ) {
                $val = sanitize_text_field( $val );

                if ( isset( $json->RAW->$val->USD->PRICE ) && isset( $json->RAW->$val->USD->CHANGEPCT24HOUR ) ) {
                    $price_raw      = $json->RAW->$val->USD->PRICE;
                    $change_pct_raw = $json->RAW->$val->USD->CHANGEPCT24HOUR;

                    $result[ $val ] = array(
                        'currency' => $val,
                        'price'    => number_format( $price_raw, 2, '.', '' ),
                        'change'   => $change_pct_raw < 0 ? number_format( $change_pct_raw, 2, '.', '' ) : '+' . number_format( $change_pct_raw, 2, '.', '' ),
                        'class'    => $change_pct_raw < 0 ? 'low-rate' : 'high-rate',
                    );
                }
            }
            set_transient( $transient_key, $result, 60 ); // Cache for 60 seconds
        }
    }

    wp_send_json( $result );
}

// 2. CALCULATOR ENDPOINT (Cached for 30 seconds)
function coinsfera_calculator_ajax_feed() {
    $fsym  = isset( $_REQUEST['fsym'] ) ? sanitize_text_field( $_REQUEST['fsym'] ) : '';
    $tsyms = isset( $_REQUEST['tsyms'] ) ? sanitize_text_field( $_REQUEST['tsyms'] ) : 'USD';

    if ( empty( $fsym ) ) {
        wp_send_json( [ $tsyms => 0 ] );
    }

    $transient_key = 'coinsfera_calc_' . strtolower( $fsym ) . '_' . strtolower( $tsyms );
    $data = get_transient( $transient_key );

    if ( false === $data ) {
        $api_url = 'https://min-api.cryptocompare.com/data/price?fsym=' . $fsym . '&tsyms=' . $tsyms . '&api_key=' . COINSFERA_CRYPTOCOMPARE_API_KEY;
        $response = wp_remote_get( $api_url );

        if ( ! is_wp_error( $response ) ) {
            $data = json_decode( wp_remote_retrieve_body( $response ), true );
            set_transient( $transient_key, $data, 30 ); // Cache for 30 seconds to keep inputs highly reactive
        } else {
            $data = [ $tsyms => 0 ];
        }
    }

    wp_send_json( $data );
}
