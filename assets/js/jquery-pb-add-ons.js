
/*
 * Function to download/activate add-ons on button click
 */
jQuery('.wppb-add-on .button').on( 'click', function(e) {
    if( jQuery(this).attr('disabled') ) {
        return false;
    }

    // Download add-on
    if( jQuery(this).hasClass('wppb-add-on-download') ) {
        e.preventDefault();
        wppb_add_on_download_and_install( jQuery(this) );
    }

    // Activate add-on
    if( jQuery(this).hasClass('wppb-add-on-activate') ) {
        e.preventDefault();
        wppb_add_on_activate( jQuery(this) );
    }

    // Deactivate add-on
    if( jQuery(this).hasClass('wppb-add-on-deactivate') ) {
        e.preventDefault();
        wppb_add_on_deactivate( jQuery(this) );
    }
});


/*
 * Make deactivate button from Add-On is Active message button
 */
jQuery('.wppb-add-on').on( 'hover', function() {

    $button = jQuery(this).find('.wppb-add-on-deactivate');

    if( $button.length > 0 ) {
        $button
            .animate({
                opacity: 1
            }, 100);
    }
});

/*
 * Make Add-On is Active message button from deactivate button
 */
jQuery('.wppb-add-on').on( 'mouseleave', function() {

    $button = jQuery(this).find('.wppb-add-on-deactivate');

    if( $button.length > 0 ) {
        $button
            .animate({
                opacity: 0
            }, 100);
    }
});


/*
 * Download Add-On from Cozmoslabs on click
 */
function wppb_add_on_download_and_install( $button ) {
    $download_button = $button;

    var fade_in_out_speed = 300;
    var download_url = $download_button.attr('href');
    var file_name = $download_button.attr('data-add-on-slug') + '.zip';
    var add_on_name = $download_button.attr('data-add-on-name');
    var add_on_index = $download_button.parents('.wppb-add-on').index('.wppb-add-on');
    var nonce = $download_button.data('nonce');

    $download_button
        .attr('disabled', true);

    $spinner = $download_button.siblings('.spinner');

    $spinner.animate({
        opacity: 0.7
    }, 100);

    // Remove the current displayed message
    wppb_add_on_remove_status_message( $download_button, fade_in_out_speed );

    // Set status confirmation message
    wppb_add_on_set_status_message( $download_button, 'dashicons-download', jQuery('#wppb-add-on-downloading-message-text').text(), fade_in_out_speed, fade_in_out_speed );


    jQuery.post( ajaxurl, { action: 'wppb_add_on_download_zip_file', wppb_add_on_download_url: download_url, wppb_add_on_zip_name: file_name, wppb_add_on_name: add_on_name, wppb_add_on_index: add_on_index, nonce: nonce }, function( response ) {

        // Check if we have any errors and display a message to the user
        if( response.indexOf('error') === 0 ) {
            response = response.split('-');
            add_on_index = response[1];

            $download_button = jQuery('.wppb-add-on').eq( add_on_index ).find('.button');
            $error_message = jQuery('.wppb-add-on').eq( add_on_index).find('.wppb-error-manual-install');

            $download_button
                .blur()
                .removeAttr('disabled')
                .text( jQuery('#wppb-add-on-activated-error-button-text').text() );

            $spinner = $download_button.siblings('.spinner');
            $spinner.animate({
                opacity: 0
            }, 0);

            // Remove the current displayed message
            wppb_add_on_remove_status_message( $download_button, fade_in_out_speed);

            // Set status confirmation message
            wppb_add_on_set_status_message( $download_button, 'dashicons-no-alt', $error_message.html(), fade_in_out_speed, fade_in_out_speed, false );

            return false;
        }

        // If everything goes well go ahead and
        var add_on_index = response;
        jQuery.post( ajaxurl, { action: 'wppb_add_on_get_new_plugin_data', wppb_add_on_name: add_on_name }, function( plugin_path ) {

            $download_button = jQuery('.wppb-add-on').eq( add_on_index ).find('.button');

            $download_button
                .blur()
                .removeClass('wppb-add-on-download')
                .addClass('wppb-add-on-activate')
                .attr('href', plugin_path )
                .removeAttr('disabled')
                .text( jQuery('#wppb-add-on-activate-button-text').text() );

            $spinner = $download_button.siblings('.spinner');
            $spinner.animate({
                opacity: 0
            }, 0);

            // Remove the current displayed message
            wppb_add_on_remove_status_message( $download_button, fade_in_out_speed);

            // Set status confirmation message
            wppb_add_on_set_status_message( $download_button, 'dashicons-yes', jQuery('#wppb-add-on-download-finished-message-text').text(), fade_in_out_speed, fade_in_out_speed, true );

            /* For PMS as we are trying to increase active installs activate it automatically */
            if( add_on_name == 'Paid Member Subscriptions' ){
                wppb_add_on_activate( $download_button );
            }
        });

    });
}


/*
 * Function that activates the add-on
 */
function wppb_add_on_activate( $button ) {
    $activate_button = $button;

    var fade_in_out_speed = 300;
    var plugin = $activate_button.attr('href');
    var add_on_index = $activate_button.parents('.wppb-add-on').index('.wppb-add-on');
    var nonce = $activate_button.data('nonce');

    $activate_button
        .attr('disabled', true);

    $spinner = $activate_button.siblings('.spinner');

    $spinner.animate({
        opacity: 0.7
    }, 100);

    // Remove the current displayed message
    wppb_add_on_remove_status_message( $activate_button, fade_in_out_speed);

    jQuery.post( ajaxurl, { action: 'wppb_add_on_activate', wppb_add_on_to_activate: plugin, wppb_add_on_index: add_on_index, nonce: nonce }, function( response ) {

        add_on_index = response;

        $activate_button = jQuery('.wppb-add-on').eq( add_on_index ).find('.button');

            $activate_button
                .blur()
                .removeClass('wppb-add-on-activate')
                .addClass('wppb-add-on-deactivate')
                .removeAttr('disabled')
                .text( jQuery('#wppb-add-on-deactivate-button-text').text() );

            $spinner = $activate_button.siblings('.spinner');

            $spinner.animate({
                opacity: 0
            }, 0);

            // Set status confirmation message
            wppb_add_on_set_status_message( $activate_button, 'dashicons-yes', jQuery('#wppb-add-on-activated-message-text').text(), fade_in_out_speed, 0, true );
            wppb_add_on_remove_status_message( $activate_button, fade_in_out_speed, 2000 );

            // Set is active message
            wppb_add_on_set_status_message( $activate_button, 'dashicons-yes', jQuery('#wppb-add-on-is-active-message-text').html(), fade_in_out_speed, 2000 + fade_in_out_speed );
    });
}



/*
 * Function that deactivates the add-on
 */
function wppb_add_on_deactivate( $button ) {

    var fade_in_out_speed = 300;
    var plugin = $button.attr('href');
    var add_on_index = $button.parents('.wppb-add-on').index('.wppb-add-on');
    var nonce = $button.data('nonce');

    $button
        .removeClass('wppb-add-on-deactivate')
        .attr('disabled', true);

    $spinner = $button.siblings('.spinner');

    $spinner.animate({
        opacity: 0.7
    }, 100);

    // Remove the current displayed message
    wppb_add_on_remove_status_message( $button, fade_in_out_speed );

    jQuery.post( ajaxurl, { action: 'wppb_add_on_deactivate', wppb_add_on_to_deactivate: plugin, wppb_add_on_index: add_on_index, nonce: nonce }, function( response ) {

        add_on_index = response;

        $button = jQuery('.wppb-add-on').eq( add_on_index ).find('.button');

        $button
            .blur()
            .removeClass('wppb-add-on-is-active')
            .addClass('wppb-add-on-activate')
            .attr( 'disabled', false )
            .text( jQuery('#wppb-add-on-activate-button-text').text() );

        $spinner = $button.siblings('.spinner');

        $spinner.animate({
            opacity: 0
        }, 0);

        // Set status confirmation message
        wppb_add_on_set_status_message( $button, 'dashicons-yes', jQuery('#wppb-add-on-deactivated-message-text').text(), fade_in_out_speed, 0, true );
        wppb_add_on_remove_status_message( $button, fade_in_out_speed, 2000 );

        // Set is active message
        wppb_add_on_set_status_message( $button, 'dashicons-no-alt', jQuery('#wppb-add-on-is-not-active-message-text').html(), fade_in_out_speed, 2000 + fade_in_out_speed );

    });
}


/*
 * Function used to remove the status message of an add-on
 *
 * @param object $button            - The jQuery object of the add-on box button that was pressed
 * @param int fade_in_out_speed     - The speed of the fade in and out animations
 * @param int delay                 - Delay removing of the message
 *
 */
function wppb_add_on_remove_status_message( $button, fade_in_out_speed, delay ) {

    if( typeof( delay ) == 'undefined' ) {
        delay = 0;
    }

    setTimeout( function() {

        $button.siblings('.dashicons')
            .animate({
                opacity: 0
            }, fade_in_out_speed );

        $button.siblings('.wppb-add-on-message')
            .animate({
                opacity: 0
            }, fade_in_out_speed );

    }, delay);

}

/*
 * Function used to remove the status message of an add-on
 *
 * @param object $button                - The jQuery object of the add-on box button that was pressed
 * @param string message_icon_class     - The string name of the class we want the icon to have
 * @param string message_text           - The text we want the user to see
 * @param int fade_in_out_speed         - The speed of the fade in and out animations
 * @param bool success                  - If true adds a class to style the message as a success one, if false adds a class to style the message as a failure
 *
 */
function wppb_add_on_set_status_message( $button, message_icon_class, message_text, fade_in_out_speed, delay, success ) {

    if( typeof( delay ) == 'undefined' ) {
        delay = 0;
    }

    setTimeout(function() {

        $button.siblings('.dashicons')
            .css('opacity', 0)
            .attr('class', 'dashicons')
            .addClass( message_icon_class )
            .animate({ opacity: 1}, fade_in_out_speed);

        $button.siblings('.wppb-add-on-message')
            .css('opacity', 0)
            .attr( 'class', 'wppb-add-on-message' )
            .html( message_text )
            .animate({ opacity: 1}, fade_in_out_speed);

        if( typeof( success ) != 'undefined' ) {
            if( success == true ) {
                $button.siblings('.dashicons')
                    .addClass('wppb-confirmation-success');
                $button.siblings('.wppb-add-on-message')
                    .addClass('wppb-confirmation-success');
            } else if( success == false ) {
                $button.siblings('.dashicons')
                    .addClass('wppb-confirmation-error');
                $button.siblings('.wppb-add-on-message')
                    .addClass('wppb-confirmation-error');
            }
        }

    }, delay );

}