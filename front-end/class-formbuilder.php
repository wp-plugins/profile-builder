<?php 
class Profile_Builder_Form_Creator{
	private $defaults = array(
							'form_type' 			=> '',					
							'form_fields' 			=> array(),
							'form_name' 			=> '',
							'role' 					=> '', //used only for the register-form settings
                            'redirect_url'          => '',
						);
	private $args;	
	
	
	// Constructor method for the class
	function __construct( $args ) {
		// Merge the input arguments and the defaults
		$this->args = wp_parse_args( $args, $this->defaults );

        global $wppb_shortcode_on_front;
        $wppb_shortcode_on_front = true;

		if( empty( $this->args['form_fields'] ) )
			$this->args['form_fields'] = apply_filters( 'wppb_change_form_fields', get_option( 'wppb_manage_fields' ), $this->args );
		
		if ( file_exists ( WPPB_PLUGIN_DIR.'/front-end/default-fields/default-fields.php' ) )
			require_once( WPPB_PLUGIN_DIR.'/front-end/default-fields/default-fields.php' );
			
		if ( file_exists ( WPPB_PLUGIN_DIR.'/front-end/extra-fields/extra-fields.php' ) )
			require_once( WPPB_PLUGIN_DIR.'/front-end/extra-fields/extra-fields.php' );
		
		$this->wppb_retrieve_custom_settings();

		add_action( 'wp_footer', array( &$this, 'wppb_print_script' ) ); //print scripts
        if( current_user_can( 'manage_options' ) )
            add_action( 'wppb_before_edit_profile_fields', array( &$this, 'wppb_edit_profile_select_user_to_edit' ) );
	}
	
	function wppb_retrieve_custom_settings(){
		$this->args['login_after_register'] = apply_filters( 'wppb_automatically_login_after_register', 'No' ); //used only for the register-form settings
		$this->args['redirect_activated'] = apply_filters( 'wppb_redirect_default_setting', '' );
		$this->args['redirect_url'] = apply_filters( 'wppb_redirect_default_location', ($this->args['redirect_url'] != '') ? $this->args['redirect_url'] : wppb_curpageurl() );
        /* for register forms check to see if we have a custom redirect "Redirect After Register" */
        if( PROFILE_BUILDER == 'Profile Builder Pro' ) {
            if ($this->args['form_type'] == 'register') {
                $wppb_module_settings = get_option('wppb_module_settings');
                if (isset($wppb_module_settings['wppb_customRedirect']) && ($wppb_module_settings['wppb_customRedirect'] == 'show')) {
                    $custom_redirect = get_option('customRedirectSettings');
                    //Make sure Custom Redirects are set and there was no "redirect_url" parameter set in the shortcode
                    if (isset($custom_redirect['afterRegister']) && ($custom_redirect['afterRegister'] == 'yes') && (trim($custom_redirect['afterRegisterTarget']) != '') && ($this->args['redirect_url'] == wppb_curpageurl())) {
                        $this->args['redirect_url'] = $this->args['custom_redirect_after_register_url'] = apply_filters('wppb_redirect_default_location', $custom_redirect['afterRegisterTarget']);
                    }
                }
            }
        }
		$this->args['redirect_delay'] = apply_filters( 'wppb_redirect_default_duration', 3 );
	
		if ( $this->args['form_name'] != 'unspecified' ){
			$post_type = ( ( $this->args['form_type'] == 'register' ) ? 'wppb-rf-cpt' : 'wppb-epf-cpt' );
			$meta_name = ( ( $this->args['form_type'] == 'register' ) ? 'wppb_rf_page_settings' : 'wppb_epf_page_settings' );
			
			$ep_r_posts = get_posts( array( 'posts_per_page' => -1, 'post_status' => apply_filters ( 'wppb_get_ep_r_posts_on_front_end', array( 'publish' ) ), 'post_type' => $post_type, 'orderby' => 'post_date', 'order' => 'ASC' ) );
			foreach ( $ep_r_posts as $key => $value ){
				if ( trim( Wordpress_Creation_Kit_PB::wck_generate_slug( $value->post_title ) ) == $this->args['form_name'] ){
					$page_settings = get_post_meta( $value->ID, $meta_name, true );

                    if( !empty( $page_settings[0]['set-role'] ) ){
                        if( $page_settings[0]['set-role'] == 'default role' ){
                            $selected_role = trim( get_option( 'default_role' ) );
                        }
                        else
                            $selected_role = $page_settings[0]['set-role'];
                    }
					
					$this->args['role'] = ( isset( $selected_role ) ? $selected_role : $this->args['role'] );
					$this->args['login_after_register'] = ( isset( $page_settings[0]['automatically-log-in'] ) ? $page_settings[0]['automatically-log-in'] : $this->args['login_after_register'] );
					$this->args['redirect_activated'] = ( isset( $page_settings[0]['redirect'] ) ? $page_settings[0]['redirect'] : $this->args['redirect_activated'] );
					$this->args['redirect_url'] = ( isset( $page_settings[0]['url'] ) ? $page_settings[0]['url'] : $this->args['redirect_url'] );
					$this->args['redirect_delay'] = ( isset( $page_settings[0]['display-messages'] ) ? $page_settings[0]['display-messages'] : $this->args['redirect_delay'] );
				}
			}
		}
	}

    function wppb_form_logic() {
        if( $this->args['form_type'] == 'register' ){
            $registration = apply_filters ( 'wppb_register_setting_override', get_option( 'users_can_register' ) );

            if ( !is_user_logged_in() ){
                if ( !$registration )
                    echo apply_filters( 'wppb_register_pre_form_message', '<p class="alert" id="wppb_register_pre_form_message">'.__( 'Only an administrator can add new users.', 'profilebuilder').'</p>' );

                elseif ( $registration ){
                    $this->wppb_form_content( apply_filters( 'wppb_register_pre_form_message', '' ) );
                }

            }else{
                $current_user_capability = apply_filters ( 'wppb_registration_user_capability', 'create_users' );

                if ( current_user_can( $current_user_capability ) && $registration )
                    $this->wppb_form_content( apply_filters( 'wppb_register_pre_form_message', '<p class="alert" id="wppb_register_pre_form_message">'.__( 'Users can register themselves or you can manually create users here.', 'profilebuilder'). '<img src="'.WPPB_PLUGIN_URL.'assets/images/pencil_delete.png" title="'.__( 'This message is only visible by administrators', 'profilebuilder' ).'"/>' . '</p>' ) );

                elseif ( current_user_can( $current_user_capability ) && !$registration )
                    $this->wppb_form_content( apply_filters( 'wppb_register_pre_form_message', '<p class="alert" id="wppb_register_pre_form_message">'.__( 'Users cannot currently register themselves, but you can manually create users here.', 'profilebuilder'). '<img src="'.WPPB_PLUGIN_URL.'assets/images/pencil_delete.png" title="'.__( 'This message is only visible by administrators', 'profilebuilder' ).'"/>' . '</p>' ) );

                elseif ( !current_user_can( $current_user_capability ) ){
                    global $user_ID;

                    $userdata = get_userdata( $user_ID );
                    $display_name = ( ( $userdata->data->display_name == '' ) ? $userdata->data->user_login : $userdata->data->display_name );

                    $wppb_general_settings = get_option( 'wppb_general_settings' );
                    if ( isset( $wppb_general_settings['loginWith'] ) && ( $wppb_general_settings['loginWith'] == 'email' ) )
                        $display_name = $userdata->data->user_email;

                    echo apply_filters( 'wppb_register_pre_form_message', '<p class="alert" id="wppb_register_pre_form_message">'.sprintf( __( "You are currently logged in as %1s. You don't need another account. %2s", 'profilebuilder' ), '<a href="'.get_author_posts_url( $user_ID ).'" title="'.$display_name.'">'.$display_name.'</a>', '<a href="'.wp_logout_url( get_permalink() ).'" title="'.__( 'Log out of this account.', 'profilebuilder' ).'">'.__( 'Logout', 'profilebuilder' ).'  &raquo;</a>' ).'</p>', $user_ID );
                }
            }

        }elseif ( $this->args['form_type'] == 'edit_profile' ){
            if ( !is_user_logged_in() )
                echo apply_filters( 'wppb_edit_profile_user_not_logged_in_message', '<p class="warning" id="wppb_edit_profile_user_not_logged_in_message">'.__( 'You must be logged in to edit your profile.', 'profilebuilder' ) .'</p>' );

            elseif ( is_user_logged_in() )
                $this->wppb_form_content( apply_filters( 'wppb_edit_profile_logged_in_user_message', '' ) );

        }
    }
	
	function wppb_get_redirect(){
        if ( $this->args['login_after_register'] == 'Yes' )
			return $this->wppb_log_in_user();
		if ( $this->args['redirect_activated'] == 'No' || ( $this->args['form_type'] == 'edit_profile' && $this->args['form_name'] == 'unspecified' && wppb_curpageurl() == $this->args['redirect_url'] ) || ( $this->args['form_type'] == 'register' && $this->args['form_name'] == 'unspecified' && wppb_curpageurl() == $this->args['redirect_url'] ) )
			return '';
        /* if we don't have a preference on the form for redirect then if we have a custom redirect "after register redirect" option redirect to that if not don't do anything  */
        if ( $this->args['redirect_activated'] == '-' ){
            if( !empty( $this->args['custom_redirect_after_register_url'] ) )
                $this->args['redirect_url'] = $this->args['custom_redirect_after_register_url'];
            else return '';
        }

		$redirect_location = ( wppb_check_missing_http( $this->args['redirect_url'] ) ? 'http://'.$this->args['redirect_url'] : $this->args['redirect_url'] );
		
		$redirect_url = apply_filters( 'wppb_redirect_url', '<a href="'.$redirect_location.'">'.__( 'here', 'profilebuilder' ).'</a>' );
		
		return apply_filters ( 'wppb_redirect_message_before_returning', '<p class="redirect_message">'.sprintf( __( 'You will soon be redirected automatically. If you see this page for more than %1$d seconds, please click %2$s.%3$s', 'profilebuilder' ), $this->args['redirect_delay'], $redirect_url, '<meta http-equiv="Refresh" content="'.$this->args['redirect_delay'].';url='.$redirect_location.'" />' ).'</p>', $this->args );
	}
	
	
	function wppb_log_in_user(){
        if ( is_user_logged_in() )
            return;

        $wppb_general_settings = get_option( 'wppb_general_settings' );
        if ( isset( $wppb_general_settings['emailConfirmation'] ) && ( $wppb_general_settings['emailConfirmation'] == 'yes' ) )
            return;

        if ( isset( $wppb_general_settings['adminApproval'] ) && ( $wppb_general_settings['adminApproval'] == 'yes' ) )
            return;

        if( !empty( $_POST['username'] ) )
            $username = trim( $_POST['username'] );
        $password = trim( $_POST['passw1'] );

        if( isset( $wppb_general_settings['loginWith'] ) && ( $wppb_general_settings['loginWith'] == 'email' ) ){
            $username = apply_filters( 'wppb_generated_random_username', Wordpress_Creation_Kit_PB::wck_generate_slug( trim( $_POST['email'] ) ), $_POST['email'] );
        }

        /* get user id */
        $user = get_user_by( 'login', $username );
        $nonce = wp_create_nonce( 'autologin-'.$user->ID.'-'.(int)( time() / 60 ) );

        /* define redirect location */
        if ( $this->args['redirect_activated'] == 'No' ){
            $location = home_url();
        }else if ( $this->args['redirect_activated'] == '-' ){
            if( !empty( $this->args['custom_redirect_after_register_url'] ) )
                $location = $this->args['custom_redirect_after_register_url'];
            else
                $location = home_url();
        }
        else{
            $location = ( wppb_check_missing_http( $this->args['redirect_url'] ) ? 'http://'.$this->args['redirect_url'] : $this->args['redirect_url'] );
        }

        $location .= "/?autologin=true&uid=$user->ID&_wpnonce=$nonce";

        return "<script> window.location.replace('$location'); </script>";
	}
	
	function wppb_form_content( $message ){
		$field_check_errors = array();

		if( isset( $_REQUEST['action'] ) ){
			$field_check_errors = $this->wppb_test_required_form_values( $_REQUEST );			
			if( empty( $field_check_errors ) ){
				// we only have a $user_id on default registration (no email confirmation, no multisite)
				$user_id = $this->wppb_save_form_values( $_REQUEST );
				
				if ( ( 'POST' == $_SERVER['REQUEST_METHOD'] ) && ( $_POST['action'] == $this->args['form_type'] ) ){

                    $form_message_tpl_start = apply_filters( 'wppb_form_message_tpl_start', '<p class="alert" id="wppb_form_success_message">');
                    $form_message_tpl_end = apply_filters( 'wppb_form_message_tpl_end', '</p>');

                    if( $this->args['form_type'] == 'register' ){
                        // ec = email confirmation setting
                        // aa = admin approval setting
                        $wppb_general_settings = get_option( 'wppb_general_settings', 'false' );
                        if ( $wppb_general_settings ){
                            if( !empty( $wppb_general_settings['emailConfirmation'] ) )
                                $wppb_email_confirmation = $wppb_general_settings['emailConfirmation'];
                            else
                                $wppb_email_confirmation = 'no';
                            if( !empty( $wppb_general_settings['adminApproval'] ) )
                                $wppb_admin_approval = $wppb_general_settings['adminApproval'];
                            else
                                $wppb_admin_approval = 'no';
                            $account_management_settings = 'ec-' . $wppb_email_confirmation . '_' . 'aa-' . $wppb_admin_approval;
                        } else {
                            $account_management_settings = 'ec-no_aa-no';
                        }

                        if ( isset( $_POST['username'] ) && ( trim( $_POST['username'] ) != '' ) ){
                            $account_name = trim( $_POST['username'] );
                        } elseif( isset( $_POST['email'] ) && ( trim( $_POST['email'] ) != '' ) ) {
                            $account_name = trim( $_POST['email'] );
                        }

                        switch ( $account_management_settings ){
                            case 'ec-no_aa-no':
                                $wppb_register_success_message = apply_filters( 'wppb_register_success_message', sprintf( __( "The account %1s has been successfully created!", 'profilebuilder' ), $account_name ), $account_name );
                                break;
                            case 'ec-yes_aa-no':
                                $wppb_register_success_message = apply_filters( 'wppb_register_success_message', sprintf( __( "Before you can access your account %1s, you need to confirm your email address. Please check your inbox and click the activation link.", 'profilebuilder' ), $account_name ), $account_name );
                                break;
                            case 'ec-no_aa-yes':
                                $wppb_register_success_message = apply_filters( 'wppb_register_success_message', sprintf( __( "Before you can access your account %1s, an administrator has to approve it. You will be notified via email.", 'profilebuilder' ), $account_name ), $account_name );
                                break;
                            case 'ec-yes_aa-yes':
                                $wppb_register_success_message = apply_filters( 'wppb_register_success_message', sprintf( __( "Before you can access your account %1s, you need to confirm your email address. Please check your inbox and click the activation link.", 'profilebuilder' ), $account_name ), $account_name );
                                break;
                        }
                        $redirect = apply_filters( 'wppb_register_redirect', $this->wppb_get_redirect() );
                        echo $form_message_tpl_start . $wppb_register_success_message  . $form_message_tpl_end . $redirect;
						//action hook after registration success
	                    do_action('wppb_register_success', $_REQUEST, $this->args['form_name'], $user_id);
                        return;
                    } elseif ( $this->args['form_type'] == 'edit_profile' ){
						$redirect = apply_filters( 'wppb_edit_profile_redirect', $this->wppb_get_redirect() );
						echo $form_message_tpl_start  . apply_filters( 'wppb_edit_profile_success_message', __( 'Your profile has been successfully updated!', 'profilebuilder' ) ) . $form_message_tpl_end . $redirect;
						//action hook after edit profile success
	                    do_action('wppb_edit_profile_success', $_REQUEST, $this->args['form_name'], $user_id);
                        if ( apply_filters( 'wppb_no_form_after_profile_update', false ) )
	                        return;
					}
				
				}
			
			}else
				echo $message.apply_filters( 'wppb_general_top_error_message', '<p id="wppb_general_top_error_message">'.__( 'There was an error in the submitted form', 'profilebuilder' ).'</p>' );
		
		}else
			echo $message;
		
		// use this action hook to add extra content before the register form
		do_action( 'wppb_before_'.$this->args['form_type'].'_fields' );
        ?>
        <form enctype="multipart/form-data" method="post" id="<?php if( $this->args['form_type'] == 'register' ) echo 'wppb-register-user';  else if( $this->args['form_type'] == 'edit_profile' ) echo 'wppb-edit-user'; if( isset($this->args['form_name']) && $this->args['form_name'] != "unspecified" ) echo '-' . $this->args['form_name']; ?>" class="wppb-user-forms<?php if( $this->args['form_type'] == 'register' ) echo ' wppb-register-user';  else if( $this->args['form_type'] == 'edit_profile' ) echo ' wppb-edit-user';?>" action="<?php echo apply_filters( 'wppb_form_action', '' ); ?>">
			<?php
			echo apply_filters( 'wppb_before_form_fields', '<ul>' );
			$this->wppb_output_form_fields( $_REQUEST, $field_check_errors );
			echo apply_filters( 'wppb_after_form_fields', '</ul>' );
			
			echo apply_filters( 'wppb_before_send_credentials_checkbox', '<ul>' );
			$this->wppb_add_send_credentials_checkbox( $_REQUEST, $this->args['form_type'] );
			echo apply_filters( 'wppb_after_send_credentials_checkbox', '</ul>' );
			?>
			<p class="form-submit">
				<?php 
				if( $this->args['form_type'] == 'register' )
					$button_name = ( current_user_can( 'create_user' ) ? __( 'Add User', 'profilebuilder' ) : __( 'Register', 'profilebuilder' ) );
					
				elseif( $this->args['form_type'] == 'edit_profile' )
					$button_name = __( 'Update', 'profilebuilder' );
				?>			
				<input name="<?php echo $this->args['form_type']; ?>" type="submit" id="<?php echo $this->args['form_type']; ?>" class="submit button" value="<?php echo apply_filters( 'wppb_'. $this->args['form_type'] .'_button_name', $button_name ); ?>" />
				<input name="action" type="hidden" id="action" value="<?php echo $this->args['form_type']; ?>" />
				<input name="form_name" type="hidden" id="form_name" value="<?php echo $this->args['form_name']; ?>" />
			</p><!-- .form-submit -->
			<?php wp_nonce_field( 'verify_form_submission', $this->args['form_type'].'_nonce_field' ); ?>
		</form>
		<?php
		// use this action hook to add extra content after the register form
		do_action( 'wppb_after_'. $this->args['form_type'] .'_fields' );
		
	}
	
	function wppb_output_form_fields( $global_request, $field_check_errors ){

		$output_fields = '';
		
		if( !empty( $this->args['form_fields'] ) ){
			foreach( $this->args['form_fields'] as $field ){
				$error_var = ( ( array_key_exists( $field['id'], $field_check_errors ) ) ? ' wppb-field-error' : '' );
				$specific_message = ( ( array_key_exists( $field['id'], $field_check_errors ) ) ? $field_check_errors[$field['id']] : '' );

                $display_field = apply_filters( 'wppb_output_display_form_field', true, $field, $this->args['form_type'], $this->args['role'], $this->wppb_get_desired_user_id() );

                if( $display_field == false )
                    continue;

				$output_fields .= apply_filters( 'wppb_output_before_form_field', '<li class="wppb-form-field wppb-'. Wordpress_Creation_Kit_PB::wck_generate_slug( $field['field'] ) .$error_var.'" id="wppb-form-element-'. $field['id'] .'">', $field, $error_var );
				$output_fields .= apply_filters( 'wppb_output_form_field_'.Wordpress_Creation_Kit_PB::wck_generate_slug( $field['field'] ), '', $this->args['form_type'], $field, $this->wppb_get_desired_user_id(), $field_check_errors, $global_request, $this->args['role'] );
				$output_fields .= apply_filters( 'wppb_output_specific_error_message', $specific_message );
				$output_fields .= apply_filters( 'wppb_output_after_form_field', '</li>', $field );
			}
		}
		
		echo apply_filters( 'wppb_output_fields_filter', $output_fields );
	}
		
	function wppb_print_script(){
		if( $this->args['form_type'] == 'edit_profile' ){
			wp_register_script( 'wppb-edit-profile-scripts', WPPB_PLUGIN_URL . 'assets/js/jquery-edit-profile.js', array( 'jquery' ), PROFILE_BUILDER_VERSION );
			
			$translation_array = array( 'avatar' => __( 'The avatar was successfully deleted!', 'profilebuilder' ), 'attachment' => __( 'The following attachment was successfully deleted:', 'profilebuilder' ) );
			wp_localize_script( 'wppb-edit-profile-scripts', 'ep', $translation_array );
			
			wp_print_scripts( 'wppb-edit-profile-scripts' );
		}
	}
	
	function wppb_add_send_credentials_checkbox ( $request_data, $form ){
		if ( $form == 'edit_profile' )
			echo '';
		
		else{
			$checkbox = apply_filters( 'wppb_send_credentials_checkbox_logic', '<li class="wppb-form-field wppb-send-credentials-checkbox"><label for="send_credentials_via_email"><input id="send_credentials_via_email" type="checkbox" name="send_credentials_via_email" value="sending"'.( ( isset( $request_data['send_credentials_via_email'] ) && ( $request_data['send_credentials_via_email'] == 'sending' ) ) ? ' checked' : '' ).'/>'.__( 'Send these credentials via email.', 'profilebuilder').'</label></li>', $request_data, $form );
		
			if ( !is_multisite() ){
				$wppb_general_settings = get_option( 'wppb_general_settings' );
				
				echo ( isset( $wppb_general_settings['emailConfirmation'] ) && ( $wppb_general_settings['emailConfirmation'] == 'yes' ) ? '' : $checkbox );
				
			}else
				echo '';
		}
	}
	
	
	function wppb_test_required_form_values( $global_request ){
		$output_field_errors = array();
		
		if( !empty( $this->args['form_fields'] ) ){
			foreach( $this->args['form_fields'] as $field ){
				$error_for_field = apply_filters( 'wppb_check_form_field_'.Wordpress_Creation_Kit_PB::wck_generate_slug( $field['field'] ), '', $field, $global_request, $this->args['form_type'] );
				
				if( !empty( $error_for_field ) )
					$output_field_errors[$field['id']] = '<span class="wppb-form-error">' . $error_for_field  . '</span>';
			}
		}
		
		return apply_filters( 'wppb_output_field_errors_filter', $output_field_errors );
	}
	
	function wppb_save_form_values( $global_request ){
		$user_id = $this->wppb_get_desired_user_id();
		$userdata = apply_filters( 'wppb_build_userdata', array(), $global_request );
		$new_user_signup = false;

        $wppb_general_settings = get_option( 'wppb_general_settings' );
        if( isset( $wppb_general_settings['loginWith'] ) && ( $wppb_general_settings['loginWith'] == 'email' ) ){
            $userdata['user_login'] = apply_filters( 'wppb_generated_random_username', Wordpress_Creation_Kit_PB::wck_generate_slug( trim( $userdata['user_email'] ) ), $userdata['user_email'] );
        }

		if( $this->args['form_type'] == 'register' ){
			if ( !is_multisite() ){
				if ( isset( $wppb_general_settings['emailConfirmation'] ) && ( $wppb_general_settings['emailConfirmation'] == 'yes' ) ){
					$new_user_signup = true;
					$multisite_message = true;					
					$userdata = $this->wppb_add_custom_field_values( $global_request, $userdata, $this->args['form_fields'] );

                    if( !isset( $userdata['role'] ) )
                        $userdata['role'] = $this->args['role'];

                    $userdata['user_pass'] = wp_hash_password( $userdata['user_pass'] );
					wppb_signup_user( $userdata['user_login'], $userdata['user_email'], $userdata );
				
				}else{
                    if( !isset( $userdata['role'] ) )
					    $userdata['role'] = $this->args['role'];

                    $userdata = wp_unslash( $userdata );
					$user_id = wp_insert_user( $userdata );
				}
			
			}else{
				$new_user_signup = true;
				$multisite_message = true;			
				$userdata = $this->wppb_add_custom_field_values( $global_request, $userdata, $this->args['form_fields'] );

                if( !isset( $userdata['role'] ) )
				    $userdata['role'] = $this->args['role'];

                $userdata['user_pass'] = wp_hash_password( $userdata['user_pass'] );
                /* since version 2.0.7 add this meta so we know on what blog the user registered */
                $userdata['registered_for_blog_id'] = get_current_blog_id();
                $userdata = wp_unslash( $userdata );
				wppb_signup_user( $userdata['user_login'], $userdata['user_email'], $userdata );
			}
		
		}elseif( $this->args['form_type'] == 'edit_profile' ){
			$userdata['ID'] = $this->wppb_get_desired_user_id();
            $userdata = wp_unslash( $userdata );
            /* if the user changes his password then we can't send it to the wp_update_user() function or
            the user will be logged out and won't be logged in again because we call wp_update_user() after
            the headers were sent( in the content as a shortcode ) */
            if( isset( $userdata['user_pass'] ) && !empty( $userdata['user_pass'] ) ){
                unset($userdata['user_pass']);
            }
			wp_update_user( $userdata );
		}
		
		if( !empty( $this->args['form_fields'] ) && !$new_user_signup ){
			foreach( $this->args['form_fields'] as $field ){
				do_action( 'wppb_save_form_field', $field, $user_id, $global_request, $this->args['form_type'] );
			}
			
			if ( $this->args['form_type'] == 'register' ){
				if ( !is_wp_error( $user_id ) ){
					$wppb_general_settings = get_option( 'wppb_general_settings' );
                    if( isset( $global_request['send_credentials_via_email'] ) && ( $global_request['send_credentials_via_email'] == 'sending' ) )
                        $send_credentials_via_email = 'sending';
                    else
                        $send_credentials_via_email = '';
					wppb_notify_user_registration_email( get_bloginfo( 'name' ), ( isset( $userdata['user_login'] ) ? trim( $userdata['user_login'] ) : trim( $userdata['user_email'] ) ), trim( $userdata['user_email'] ), $send_credentials_via_email, trim( $userdata['user_pass'] ), ( isset( $wppb_general_settings['adminApproval'] ) ? $wppb_general_settings['adminApproval'] : 'no' ) );
				}
            }
		}
		return $user_id;
	}
	
	function wppb_add_custom_field_values( $global_request, $meta, $form_properties ){	
		if( !empty( $this->args['form_fields'] ) ){
            foreach( $this->args['form_fields'] as $field ){
                if( !empty( $field['meta-name'] ) ){
                    $posted_value = ( !empty( $global_request[$field['meta-name']] )  ? $global_request[$field['meta-name']] : '' );
                    $meta[$field['meta-name']] = apply_filters( 'wppb_add_to_user_signup_form_field_'.Wordpress_Creation_Kit_PB::wck_generate_slug( $field['field'] ), $posted_value, $field, $global_request );
                }
            }
        }
		
		return $meta;
	}

    /**
     * Function that returns the id for the current logged in user or for edit profile forms for administrator it can return the id of a selected user
     */
    function wppb_get_desired_user_id(){
        if( $this->args['form_type'] == 'edit_profile' ){
            //only admins
            if ( current_user_can( 'manage_options' ) ) {
                if( isset( $_GET['edit_user'] ) && ! empty( $_GET['edit_user'] ) ){
                    return $_GET['edit_user'];
                }
            }
        }

        return get_current_user_id();
    }

    function wppb_edit_profile_select_user_to_edit(){

        /* add a hard cap: if we have more than 5000 users don't display the dropdown for performance considerations */
        $user_count = count_users();
        if( $user_count['total_users'] > apply_filters( 'wppb_edit_other_users_count_limit', 5000 ) )
            return;

        if( isset( $_GET['edit_user'] ) && ! empty( $_GET['edit_user'] ) )
            $selected = $_GET['edit_user'];
        else
            $selected = false;
        ?>
        <form method="GET" action="" id="select_user_to_edit_form">
            <p class="wppb-form-field">
                <label for="edit_user"><?php _e( 'User to edit:', 'profilebuilder' ) ?></label><?php wp_dropdown_users( array( 'name' => 'edit_user', 'id' => 'wppb-edit-user', 'selected' => $selected ) ); ?>
            </p>
            <script type="text/javascript">jQuery( '#wppb-edit-user' ).change( function(){ window.location.href = "<?php echo add_query_arg( array( 'edit_user' => '=' ) ) ?>"+jQuery(this).val(); });</script>
        </form>
        <?php
    }

    /**
     * Handle toString method
     *
     * @since 2.0
     *
     * @return string $html html for the form.
     */
    public function __toString() {
        ob_start();
        $this->wppb_form_logic();
        $html = ob_get_clean();
        return "{$html}";
    }
}

/* set action for automatic login after registration */
add_action( 'init', 'wppb_autologin_after_registration' );
function wppb_autologin_after_registration(){
    if( isset( $_GET['autologin'] ) && isset( $_GET['uid'] ) ){
        $uid = $_GET['uid'];
        $nonce  = $_REQUEST['_wpnonce'];

        $arr_params = array( 'autologin', 'uid', '_wpnonce' );
        $current_page_url = remove_query_arg( $arr_params, wppb_curpageurl() );

        if ( ! ( wp_verify_nonce( $nonce , 'autologin-'.$uid.'-'.(int)( time() / 60 ) ) || wp_verify_nonce( $nonce , 'autologin-'.$uid.'-'.(int)( time() / 60 ) - 1 ) ) ){
            wp_redirect( $current_page_url );
            exit;
        } else {
            wp_set_auth_cookie( $uid );
            wp_redirect( $current_page_url );
            exit;
        }
    }
}