<?php
/**
 * Template Name: Coinsfera - API Test
 * The full width page template file
 *
 * @package Coinsfera_WordPress_Theme
 */

get_header();
?>

<div id="primary" class="content-area page-template-elementor-default col-12">
	<?php

		
$cryptocurrency = ['BTC','ETH','BNB'];

	$fsyms = '';

	for( $j = 0; $j < sizeof($cryptocurrency); $j++ ) {

		if( $j == 0 ) {

			$fsyms = $fsyms.$cryptocurrency[$j];
		}
		else
		{
			$fsyms = $fsyms.','.$cryptocurrency[$j];
		}
		
	}

	$api_url='https://min-api.cryptocompare.com/data/pricemultifull?fsyms=BTC&tsyms=USD,EUR&api_key=ce8c00387b9cf2dc84d59b60d9eb91ea219d0b3c125cf69b56149b94cba8b67b';

	$json=json_decode( file_get_contents( $api_url ) );
    
	$result = array();
if(!empty($json)){
	foreach( $json as $obj ){

		for( $j = 0; $j < sizeof($cryptocurrency); $j++ ) {

			$val = $cryptocurrency[$j];
			$chage_pct = $obj->$val->USD->CHANGEPCT24HOUR;

			if($chage_pct < 0) {
				$class = 'low-rate';
				$change = $obj->$val->USD->CHANGEPCT24HOUR;
			}
			else{
				$class = 'high-rate';
				$change = '+'.$obj->$val->USD->CHANGEPCT24HOUR;
			}

			$result[$val] = array(
				'currency' =>	$cryptocurrency[$j],	
				'price'    =>	$obj->$val->USD->PRICE,
				'change'   =>	$change,
				'class'    =>	$class,			
			);
		}
		
	}
}
	
	print_r($result);
?>
</div>

<?php
get_footer();