<?php
/**
 * Function that creates the "Add-Ons" submenu page
 *
 * @since v.2.1.0
 *
 * @return void
 */
function wppb_register_add_ons_submenu_page() {
    add_submenu_page( 'profile-builder', __( 'Add-Ons', 'profile-builder' ), __( 'Add-Ons', 'profile-builder' ), 'manage_options', 'profile-builder-add-ons', 'wppb_add_ons_content' );
}
add_action( 'admin_menu', 'wppb_register_add_ons_submenu_page', 19 );


/**
 * Function that adds content to the "Add-Ons" submenu page
 *
 * @since v.2.1.0
 *
 * @return string
 */
function wppb_add_ons_content() {

    $version = 'Free';
    $version = ( ( PROFILE_BUILDER == 'Profile Builder Pro' ) ? 'Pro' : $version );
    $version = ( ( PROFILE_BUILDER == 'Profile Builder Hobbyist' ) ? 'Hobbyist' : $version );

    ?>

    <div class="wrap wppb-add-on-wrap">

        <h2><?php _e( 'Add-Ons', 'profile-builder' ); ?></h2>

        <span id="wppb-add-on-activate-button-text" class="wppb-add-on-user-messages"><?php echo __( 'Activate', 'profile-builder' ); ?></span>

        <span id="wppb-add-on-downloading-message-text" class="wppb-add-on-user-messages"><?php echo __( 'Downloading and installing...', 'profile-builder' ); ?></span>
        <span id="wppb-add-on-download-finished-message-text" class="wppb-add-on-user-messages"><?php echo __( 'Installation complete', 'profile-builder' ); ?></span>

        <span id="wppb-add-on-activated-button-text" class="wppb-add-on-user-messages"><?php echo __( 'Add-On is Active', 'profile-builder' ); ?></span>
        <span id="wppb-add-on-activated-message-text" class="wppb-add-on-user-messages"><?php echo __( 'Add-On has been activated', 'profile-builder' ) ?></span>
        <span id="wppb-add-on-activated-error-button-text" class="wppb-add-on-user-messages"><?php echo __( 'Retry Install', 'profile-builder' ) ?></span>

        <span id="wppb-add-on-is-active-message-text" class="wppb-add-on-user-messages"><?php echo __( 'Add-On is <strong>active</strong>', 'profile-builder' ); ?></span>
        <span id="wppb-add-on-is-not-active-message-text" class="wppb-add-on-user-messages"><?php echo __( 'Add-On is <strong>inactive</strong>', 'profile-builder' ); ?></span>

        <span id="wppb-add-on-deactivate-button-text" class="wppb-add-on-user-messages"><?php echo __( 'Deactivate', 'profile-builder' ) ?></span>
        <span id="wppb-add-on-deactivated-message-text" class="wppb-add-on-user-messages"><?php echo __( 'Add-On has been deactivated.', 'profile-builder' ) ?></span>

        <div id="the-list">

        <?php

            $wppb_add_ons = wppb_add_ons_get_remote_content();
            $wppb_get_all_plugins = get_plugins();
            $wppb_get_active_plugins = get_option('active_plugins');

            if( $wppb_add_ons === false ) {

                echo __('Something went wrong, we could not connect to the server. Please try again later.', 'profile-builder');

            } else {

                foreach( $wppb_add_ons as $key => $wppb_add_on ) {

                    $wppb_add_on_exists = 0;
                    $wppb_add_on_is_active = 0;
                    $wppb_add_on_is_network_active = 0;

                    // Check to see if add-on is in the plugins folder
                    foreach ($wppb_get_all_plugins as $wppb_plugin_key => $wppb_plugin) {
                        if (strpos(strtolower($wppb_plugin['Name']), strtolower($wppb_add_on['name'])) !== false && strpos(strtolower($wppb_plugin['AuthorName']), strtolower('Cozmoslabs')) !== false) {
                            $wppb_add_on_exists = 1;

                            if (in_array($wppb_plugin_key, $wppb_get_active_plugins)) {
                                $wppb_add_on_is_active = 1;
                            }

                            // Consider the add-on active if it's network active
                            if (is_plugin_active_for_network($wppb_plugin_key)) {
                                $wppb_add_on_is_network_active = 1;
                                $wppb_add_on_is_active = 1;
                            }

                            $wppb_add_on['plugin_file'] = $wppb_plugin_key;
                        }
                    }

                    echo '<div class="plugin-card wppb-add-on">';
                    echo '<div class="plugin-card-top">';

                    echo '<a target="_blank" href="' . $wppb_add_on['url'] . '?utm_source=wpbackend&utm_medium=clientsite&utm_content=add-on-page&utm_campaign=PB' . $version . '">';
                    echo '<img src="' . $wppb_add_on['thumbnail_url'] . '" />';
                    echo '</a>';

                    echo '<h3 class="wppb-add-on-title">';
                    echo '<a target="_blank" href="' . $wppb_add_on['url'] . '?utm_source=wpbackend&utm_medium=clientsite&utm_content=add-on-page&utm_campaign=PB' . $version . '">';
                    echo $wppb_add_on['name'];
                    echo '</a>';
                    echo '</h3>';

                    echo '<h3 class="wppb-add-on-price">' . $wppb_add_on['price'] . '</h3>';
                    echo '<p class="wppb-add-on-description">' . $wppb_add_on['description'] . '</p>';

                    echo '</div>';

                    $wppb_version_validation = version_compare(PROFILE_BUILDER_VERSION, $wppb_add_on['product_version']);

                    ($wppb_version_validation != -1) ? $wppb_version_validation_class = 'wppb-add-on-compatible' : $wppb_version_validation_class = 'wppb-add-on-not-compatible';

                    echo '<div class="plugin-card-bottom ' . $wppb_version_validation_class . '">';

                    // PB minimum version number is all good
                    if ($wppb_version_validation != -1) {

                        // PB version type does match
                        if (in_array(strtolower($version), $wppb_add_on['product_version_type'])) {

                            $ajax_nonce = wp_create_nonce("wppb-activate-addon");

                            if ($wppb_add_on_exists) {

                                // Display activate/deactivate buttons
                                if (!$wppb_add_on_is_active) {
                                    echo '<a class="wppb-add-on-activate right button button-secondary" href="' . $wppb_add_on['plugin_file'] . '" data-nonce="' . $ajax_nonce . '">' . __('Activate', 'profile-builder') . '</a>';

                                    // If add-on is network activated don't allow deactivation
                                } elseif (!$wppb_add_on_is_network_active) {
                                    echo '<a class="wppb-add-on-deactivate right button button-secondary" href="' . $wppb_add_on['plugin_file'] . '" data-nonce="' . $ajax_nonce . '">' . __('Deactivate', 'profile-builder') . '</a>';
                                }

                                // Display message to the user
                                if (!$wppb_add_on_is_active) {
                                    echo '<span class="dashicons dashicons-no-alt"></span><span class="wppb-add-on-message">' . __('Add-On is <strong>inactive</strong>', 'profile-builder') . '</span>';
                                } else {
                                    echo '<span class="dashicons dashicons-yes"></span><span class="wppb-add-on-message">' . __('Add-On is <strong>active</strong>', 'profile-builder') . '</span>';
                                }

                            } else {

                                // If we're on a multisite don't add the wpp-add-on-download class to the button so we don't fire the js that
                                // handles the in-page download
                                if (is_multisite()) {
                                    ($wppb_add_on['paid']) ? $wppb_paid_link_class = 'button-primary' : $wppb_paid_link_class = 'button-secondary';
                                    ($wppb_add_on['paid']) ? $wppb_paid_link_text = __('Buy Now', 'profile-builder') : $wppb_paid_link_text = __('Download Now', 'profile-builder');
                                } else {
                                    ($wppb_add_on['paid']) ? $wppb_paid_link_class = 'button-primary' : $wppb_paid_link_class = 'button-secondary wppb-add-on-download';
                                    ($wppb_add_on['paid']) ? $wppb_paid_link_text = __('Buy Now', 'profile-builder') : $wppb_paid_link_text = __('Install Now', 'profile-builder');
                                }

                                ($wppb_add_on['paid']) ? $wppb_paid_href_utm_text = '?utm_source=wpbackend&utm_medium=clientsite&utm_content=add-on-page-buy-button&utm_campaign=PB' . $version : $wppb_paid_href_utm_text = '&utm_source=wpbackend&utm_medium=clientsite&utm_content=add-on-page&utm_campaign=PB' . $version;

                                echo '<a target="_blank" class="right button ' . $wppb_paid_link_class . '" href="' . $wppb_add_on['download_url'] . $wppb_paid_href_utm_text . '" data-add-on-slug="profile-builder-' . $wppb_add_on['slug'] . '" data-add-on-name="' . $wppb_add_on['name'] . '" data-nonce="' . $ajax_nonce . '">' . $wppb_paid_link_text . '</a>';
                                echo '<span class="dashicons dashicons-yes"></span><span class="wppb-add-on-message">' . __('Compatible with your version of Profile Builder.', 'profile-builder') . '</span>';

                            }

                            echo '<div class="spinner"></div>';

                            // PB version type does not match
                        } else {

                            echo '<a target="_blank" class="button button-secondary right" href="http://www.cozmoslabs.com/wordpress-profile-builder/?utm_source=wpbackend&utm_medium=clientsite&utm_content=add-on-page-upgrade-button&utm_campaign=PB' . $version . '">' . __('Upgrade Profile Builder', 'profile-builder') . '</a>';
                            echo '<span class="dashicons dashicons-no-alt"></span><span class="wppb-add-on-message">' . __('Not compatible with Profile Builder', 'profile-builder') . ' ' . $version . '</span>';

                        }

                    } else {

                        // If PB version is older than the minimum required version of the add-on
                        echo ' ' . '<a class="button button-secondary right" href="' . admin_url('plugins.php') . '">' . __('Update', 'profile-builder') . '</a>';
                        echo '<span class="wppb-add-on-message">' . __('Not compatible with your version of Profile Builder.', 'profile-builder') . '</span><br />';
                        echo '<span class="wppb-add-on-message">' . __('Minimum required Profile Builder version:', 'profile-builder') . '<strong> ' . $wppb_add_on['product_version'] . '</strong></span>';

                    }

                    // We had to put this error here because we need the url of the add-on
                    echo '<span class="wppb-add-on-user-messages wppb-error-manual-install">' . sprintf(__('Could not install add-on. Retry or <a href="%s" target="_blank">install manually</a>.', 'profile-builder'), esc_url($wppb_add_on['url'])) . '</span>';

                    echo '</div>';
                    echo '</div>';

                } /* end $wppb_add_ons foreach */
            }

        ?>
        </div>

        <div class="clear"></div>
        <div>
            <h2><?php _e( 'Recommended Plugins', 'profile-builder' ) ?></h2>
            <?php
            $pms_add_on_exists = 0;
            $pms_add_on_is_active = 0;
            $pms_add_on_is_network_active = 0;
            // Check to see if add-on is in the plugins folder
            foreach ($wppb_get_all_plugins as $wppb_plugin_key => $wppb_plugin) {
                if( strtolower($wppb_plugin['Name']) == strtolower( 'Paid Member Subscriptions' ) && strpos(strtolower($wppb_plugin['AuthorName']), strtolower('Cozmoslabs')) !== false) {
                    $pms_add_on_exists = 1;
                    if (in_array($wppb_plugin_key, $wppb_get_active_plugins)) {
                        $pms_add_on_is_active = 1;
                    }
                    // Consider the add-on active if it's network active
                    if (is_plugin_active_for_network($wppb_plugin_key)) {
                        $pms_add_on_is_network_active = 1;
                        $pms_add_on_is_active = 1;
                    }
                    $plugin_file = $wppb_plugin_key;
                }
            }
            ?>
            <div class="plugin-card wppb-recommended-plugin wppb-add-on">
                <div class="plugin-card-top">
                    <a target="_blank" href="http://wordpress.org/plugins/paid-member-subscriptions/">
                        <img src="<?php echo plugins_url( '../assets/images/pms_recommended.jpg', __FILE__ ); ?>" width="100%">
                    </a>
                    <h3 class="wppb-add-on-title">
                        <a target="_blank" href="http://wordpress.org/plugins/paid-member-subscriptions/">Paid Member Subscriptions</a>
                    </h3>
                    <h3 class="wppb-add-on-price"><?php  _e( 'Free', 'profile-builder' ) ?></h3>
                    <p class="wppb-add-on-description">
                        <?php _e( 'Accept user payments, create subscription plans and restrict content on your membership site.', 'profile-builder' ) ?>
                        <a href="<?php admin_url();?>plugin-install.php?tab=plugin-information&plugin=paid-member-subscriptions&TB_iframe=true&width=772&height=875" class="thickbox" aria-label="More information about Paid Member Subscriptions - membership & content restriction" data-title="Paid Member Subscriptions - membership & content restriction"><?php _e( 'More Details' ); ?></a>
                    </p>
                </div>
                <div class="plugin-card-bottom wppb-add-on-compatible">
                   <?php
                   if ($pms_add_on_exists) {

                       // Display activate/deactivate buttons
                       if (!$pms_add_on_is_active) {
                           echo '<a class="wppb-add-on-activate right button button-secondary" href="' . $plugin_file . '" data-nonce="' . $ajax_nonce . '">' . __('Activate', 'profile-builder') . '</a>';

                           // If add-on is network activated don't allow deactivation
                       } elseif (!$pms_add_on_is_network_active) {
                           echo '<a class="wppb-add-on-deactivate right button button-secondary" href="' . $plugin_file . '" data-nonce="' . $ajax_nonce . '">' . __('Deactivate', 'profile-builder') . '</a>';
                       }

                       // Display message to the user
                       if( !$pms_add_on_is_active ){
                           echo '<span class="dashicons dashicons-no-alt"></span><span class="wppb-add-on-message">' . __('Plugin is <strong>inactive</strong>', 'profile-builder') . '</span>';
                       } else {
                           echo '<span class="dashicons dashicons-yes"></span><span class="wppb-add-on-message">' . __('Plugin is <strong>active</strong>', 'profile-builder') . '</span>';
                       }

                   } else {

                       // If we're on a multisite don't add the wpp-add-on-download class to the button so we don't fire the js that
                       // handles the in-page download
                       if (is_multisite()) {
                           $wppb_paid_link_class = 'button-secondary';
                           $wppb_paid_link_text = __('Download Now', 'profile-builder' );
                       } else {
                           $wppb_paid_link_class = 'button-secondary wppb-add-on-download';
                           $wppb_paid_link_text = __('Install Now', 'profile-builder');
                       }

                       echo '<a target="_blank" class="right button ' . $wppb_paid_link_class . '" href="https://downloads.wordpress.org/plugin/paid-member-subscriptions.zip" data-add-on-slug="paid-member-subscriptions" data-add-on-name="Paid Member Subscriptions" data-nonce="' . $ajax_nonce . '">' . $wppb_paid_link_text . '</a>';
                       echo '<span class="dashicons dashicons-yes"></span><span class="wppb-add-on-message">' . __('Compatible with your version of Profile Builder.', 'profile-builder') . '</span>';

                   }
                    ?>
                    <div class="spinner"></div>
                    <span class="wppb-add-on-user-messages wppb-error-manual-install"><?php printf(__('Could not install plugin. Retry or <a href="%s" target="_blank">install manually</a>.', 'profile-builder'), esc_url( 'http://www.wordpress.org/plugins/paid-member-subscriptions' )) ?></a>.</span>
                </div>
            </div>
        </div>

    </div>
    <?php
}

/*
 * Function that returns the array of add-ons from cozmoslabs.com if it finds the file
 * If something goes wrong it returns false
 *
 * @since v.2.1.0
 */
function wppb_add_ons_get_remote_content() {

    $response = wp_remote_get('http://www.cozmoslabs.com/wp-content/plugins/cozmoslabs-products-add-ons/profile-builder-add-ons.json');

    if( is_wp_error($response) ) {
        return false;
    } else {
        $json_file_contents = $response['body'];
        $wppb_add_ons = json_decode( $json_file_contents, true );
    }

    if( !is_object( $wppb_add_ons ) && !is_array( $wppb_add_ons ) ) {
        return false;
    }

    return $wppb_add_ons;

}


/*
 * Function that is triggered through Ajax to activate an add-on
 *
 * @since v.2.1.0
 */
function wppb_add_on_activate() {
    check_ajax_referer( 'wppb-activate-addon', 'nonce' );
    if( current_user_can( 'manage_options' ) ){
        // Setup variables from POST
        $wppb_add_on_to_activate = $_POST['wppb_add_on_to_activate'];
        $response = $_POST['wppb_add_on_index'];

        if( !empty( $wppb_add_on_to_activate ) && !is_plugin_active( $wppb_add_on_to_activate )) {
            activate_plugin( $wppb_add_on_to_activate );
        }

        if( !empty( $response ) )
            echo $response;
    }
    wp_die();
}
add_action( 'wp_ajax_wppb_add_on_activate', 'wppb_add_on_activate' );


/*
 * Function that is triggered through Ajax to deactivate an add-on
 *
 * @since v.2.1.0
 */
function wppb_add_on_deactivate() {
    check_ajax_referer( 'wppb-activate-addon', 'nonce' );
    if( current_user_can( 'manage_options' ) ){
        // Setup variables from POST
        $wppb_add_on_to_deactivate = $_POST['wppb_add_on_to_deactivate'];
        $response = $_POST['wppb_add_on_index'];

        if( !empty( $wppb_add_on_to_deactivate ))
            deactivate_plugins( $wppb_add_on_to_deactivate );

        if( !empty( $response ) )
            echo $response;
    }
    wp_die();

}
add_action( 'wp_ajax_wppb_add_on_deactivate', 'wppb_add_on_deactivate' );


/*
 * Function that downloads and unzips the .zip file returned from Cozmoslabs
 *
 * @since v.2.1.0
 */
function wppb_add_on_download_zip_file() {

    check_ajax_referer( 'wppb-activate-addon', 'nonce' );

    // Set the response to success and change it later if needed
    $response = $_POST['wppb_add_on_index'];
    $add_on_index = $response;

    // Setup variables from POST
    $wppb_add_on_download_url = $_POST['wppb_add_on_download_url'];
    $wppb_add_on_zip_name = $_POST['wppb_add_on_zip_name'];

    if( strpos( $wppb_add_on_download_url, 'http://www.cozmoslabs.com/' ) === false && strpos( $wppb_add_on_download_url, 'https://downloads.wordpress.org/' )  === false )
        wp_die();

    // Get .zip file
    $remote_response = wp_remote_get( $wppb_add_on_download_url, array( 'timeout' => 500000) );
    if( is_wp_error( $remote_response ) ) {
        $response = 'error-' . $add_on_index;
    } else {
        $file_contents = $remote_response['body'];
    }


    // Put the file in the plugins directory
    if( isset( $file_contents ) ) {
        if( file_put_contents( WP_PLUGIN_DIR . '/' . $wppb_add_on_zip_name, $file_contents ) === false ) {
            $response = 'error-' . $add_on_index;
        }
    }


    // Unzip the file
    if( $response != 'error' ) {
        WP_Filesystem();
        if( unzip_file( WP_PLUGIN_DIR . '/' . $wppb_add_on_zip_name , WP_PLUGIN_DIR ) ) {
            // Remove the zip file after we are all done
            unlink( WP_PLUGIN_DIR . '/' . $wppb_add_on_zip_name );
        } else {
            $response = 'error-' . $add_on_index;
        }
    }

    echo $response;
    wp_die();
}
add_action( 'wp_ajax_wppb_add_on_download_zip_file', 'wppb_add_on_download_zip_file' );


/*
 * Function that retrieves the data of the newly added plugin
 *
 * @since v.2.1.0
 */
function wppb_add_on_get_new_plugin_data() {
    $wppb_add_on_name = $_POST['wppb_add_on_name'];

    $wppb_get_all_plugins = get_plugins();
    foreach( $wppb_get_all_plugins as $wppb_plugin_key => $wppb_plugin ) {

        if( strpos( $wppb_plugin['Name'], $wppb_add_on_name ) !== false && strpos( $wppb_plugin['AuthorName'], 'Cozmoslabs' ) !== false ) {

            // Deactivate the add-on if it's active
            if( is_plugin_active( $wppb_plugin_key )) {
                deactivate_plugins( $wppb_plugin_key );
            }

            // Return the plugin path
            echo $wppb_plugin_key;
            wp_die();
        }
    }

    wp_die();
}
add_action( 'wp_ajax_wppb_add_on_get_new_plugin_data', 'wppb_add_on_get_new_plugin_data' );