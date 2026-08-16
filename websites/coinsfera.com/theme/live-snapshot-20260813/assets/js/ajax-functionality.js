jQuery(document).ready(function() {

    // setInterval(function() {
        var cryptocurrency = [];

        jQuery( '.curr-rate' ).each(function( id ) {
            var test = jQuery(this).data('id');
            cryptocurrency[id] = jQuery(this).data('id');
        });

        jQuery.ajax( {
            type: 'POST',
            dataType: 'json',
            url: ajax_object.ajaxurl,
            data: {

              'action'          : 'getCurrencyData',
              'cryptocurrency'  :  cryptocurrency,

          },success: function( data ){

                jQuery.each(data, function(key,value){
                    jQuery('#PRICE_'+value.currency).text(value.price);
                    jQuery('#CHANGE_'+value.currency).text(value.change+'%');
                    jQuery('#CHANGE_'+value.currency).addClass(value.class);
                });
            }
        } );
        
    // }, 1000); 
});

jQuery(document).ready(function() {

    // setInterval(function() {
        var cryptoCurrency = [];

        jQuery( '.cashpoint-rate' ).each(function( index ) {
            var test = jQuery(this).data('cashid');
            cryptoCurrency[index] = jQuery(this).data('cashid');
        });

        jQuery.ajax( {
            type: 'POST',
            dataType: 'json',
            url: ajax_object.ajaxurl,
            data: {

              'action'          : 'getCurrencyData',
              'cryptocurrency'  :  cryptoCurrency,

          },success: function( data ){
            
                jQuery.each(data, function(key,value){
                    jQuery('#price_'+value.currency).text(value.price);
                    jQuery('#change_'+value.currency).text(value.change +'%');
                    jQuery('#change_'+value.currency).addClass(value.class);
                });
            }
        } );
        
    // }, 1000); 
});
