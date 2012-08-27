<?php

function wppb_front_end_register($atts){
	ob_start();
	$wppb_defaultOptions = get_option('wppb_default_settings');
	global $current_user;
	global $wp_roles;
	global $wpdb;
	global $error;
	$agreed = true;
	$new_user = 'no';
	$registerFilterArray = array();
	$extraFieldsErrorHolder = array();  //we will use this array to store the ID's of the extra-fields left uncompleted
	get_currentuserinfo();

	/* variables used to verify if all required fields were submitted*/
	$firstnameComplete = 'yes';
	$lastnameComplete = 'yes';
	$nicknameComplete = 'yes';
	$websiteComplete = 'yes';
	$aimComplete = 'yes';
	$yahooComplete = 'yes';
	$jabberComplete = 'yes';
	$bioComplete = 'yes';
	/* END variables used to verify if all required fields were submitted*/
	
	
	/* Load registration file if the version number is less then 3.1. */
	$versionNo = get_bloginfo( 'version' );
	if ($versionNo <= '3.1')
		require_once(ABSPATH . WPINC . '/registration.php');

	/* Check if users can register. */
	$registration = get_option( 'users_can_register' );
	
	
	//fallback if the file was largen then post_max_size, case in which no errors can be saved in $_FILES[fileName]['error']	
	if (empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
		$registerFilterArray['noPostError'] = '
		<p class="error">'.
		 __('The information size you were trying to submit was larger than', 'profilebuilder') .' '. ServerMaxUploadSizeMega .'b!<br/>'.
		 __('This is usually caused by a large file(s) trying to be uploaded.', 'profilebuilder') .'<br/>'.
		 __('Since it was also larger than', 'profilebuilder') .' '. ServerMaxPostSizeMega .'b, '. __('no additional information is available.', 'profilebuilder'). '<br/>'.
		 __('The user was NOT created!', 'profilebuilder') .
		'</p>';
		$registerFilterArray['noPostError'] = apply_filters('wppb_register_no_post_error_message', $registerFilterArray['noPostError']);
		echo $registerFilterArray['noPostError'];
	}
	
	/* If user registered, input info. */
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'adduser' && wp_verify_nonce($_POST['register_nonce_field'],'verify_true_registration') ) {
		//global $wp_roles;
		
		//get value sent in the shortcode as parameter, default to "subscriber" if not set
		extract(shortcode_atts(array('role' => 'subscriber'), $atts));

		//check if the specified role exists in the database, else fall back to the "safe-zone"
		$found = get_role($role);
		
		if ($found != null)
			$aprovedRole = $role;
		else $aprovedRole = get_option( 'default_role' );
		
		/* preset the values in case some are not submitted */
		$user_pass = '';
		if (isset($_POST['passw1']))
			$user_pass = esc_attr( $_POST['passw1'] );
		$user_name = '';
		if (isset($_POST['user_name']))
			$user_name = trim ($_POST['user_name']);
		$first_name = '';
		if (isset($_POST['first_name']))
			$first_name = trim ($_POST['first_name']);
		$last_name = '';
		if (isset($_POST['last_name']))
			$last_name = trim ($_POST['last_name']);
		$nickname = '';
		if (isset($_POST['nickname']))
			$nickname = trim ($_POST['nickname']);
		$email = '';
		if (isset($_POST['email']))
			$email = trim ($_POST['email']);
		$website = '';
		if (isset($_POST['website']))
			$website = trim ($_POST['website']);
		$aim = '';
		if (isset($_POST['aim']))
			$aim = trim ($_POST['aim']);
		$yim = '';
		if (isset($_POST['yim']))
			$yim = trim ($_POST['yim']);
		$jabber = '';
		if (isset($_POST['jabber']))
			$jabber = trim ($_POST['jabber']);
		$description = '';
		if (isset($_POST['description']))
			$description = trim ($_POST['description']);
		
		/* use filters to modify (if needed) the posted data before creating the user-data */
		$user_pass = apply_filters('wppb_register_posted_password', $user_pass);
		$user_name = apply_filters('wppb_register_posted_email', $user_name);
		$first_name = apply_filters('wppb_register_posted_first_name', $first_name);
		$last_name = apply_filters('wppb_register_posted_last_name', $last_name);
		$nickname = apply_filters('wppb_register_posted_nickname', $nickname);
		$email = apply_filters('wppb_register_posted_email', $email);
		$website = apply_filters('wppb_register_posted_website', $website);
		$aim = apply_filters('wppb_register_posted_aim', $aim);
		$yim = apply_filters('wppb_register_posted_yahoo', $yim);
		$jabber = apply_filters('wppb_register_posted_jabber', $jabber);
		$description = apply_filters('wppb_register_posted_bio', $description);
		/* END use filters to modify (if needed) the posted data before creating the user-data */
		
		$userdata = array(
			'user_pass' => $user_pass,
			'user_login' => esc_attr( $user_name ),
			'first_name' => esc_attr( $first_name ),
			'last_name' => esc_attr( $last_name ),
			'nickname' => esc_attr( $nickname ),
			'user_email' => esc_attr( $email ),
			'user_url' => esc_attr( $website ),
			'aim' => esc_attr( $aim ),
			'yim' => esc_attr( $yim ),
			'jabber' => esc_attr( $jabber ),
			'description' => esc_attr( $description ),
			'role' => $aprovedRole);
		
		//get required and shown fields
		$wppb_defaultOptions = get_option('wppb_default_settings');
		
		//check if the user agreed to the terms and conditions (if it was set)
		$wppb_premium = wppb_plugin_dir . '/premium/functions/';
			if (file_exists ( $wppb_premium.'extra.fields.php' )){
				$wppbFetchArray = get_option('wppb_custom_fields');
				foreach ( $wppbFetchArray as $key => $value){
					switch ($value['item_type']) {
						case "agreeToTerms":{
							$agreed = false;
							if ( (isset($_POST[$value['item_id'].$value['id']] )) && ($_POST[$value['item_id'].$value['id']] == 'agree'))
								$agreed = true;
							break;
						}
					}
				}
			}
		
		$registerFilterArray['extraError'] = ''; //this is for creating extra error message and bypassing registration
		$registerFilterArray['extraError'] = apply_filters('wppb_register_extra_error', $registerFilterArray['extraError']);
		
		/* check if all the required fields were completed */
		if($wppb_defaultOptions['firstname'] == 'show'){
			if (($wppb_defaultOptions['firstnameRequired'] == 'yes') && (trim($_POST['first_name']) == ''))
				$firstnameComplete = 'no';
		}elseif($wppb_defaultOptions['lastname'] == 'show'){
			if (($wppb_defaultOptions['lastnameRequired'] == 'yes') && (trim($_POST['last_name']) == ''))
				$lastnameComplete = 'no';
		}elseif($wppb_defaultOptions['nickname'] == 'show'){
			if (($wppb_defaultOptions['nicknameRequired'] == 'yes') && (trim($_POST['nickname']) == ''))
				$nicknameComplete = 'no';
		}elseif($wppb_defaultOptions['website'] == 'show'){
			if (($wppb_defaultOptions['websiteRequired'] == 'yes') && (trim($_POST['website']) == ''))
				$websiteComplete = 'no';
		}elseif($wppb_defaultOptions['aim'] == 'show'){
			if (($wppb_defaultOptions['aimRequired'] == 'yes') && (trim($_POST['aim']) == ''))
				$aimComplete = 'no';
		}elseif($wppb_defaultOptions['yahoo'] == 'show'){
			if (($wppb_defaultOptions['yahooRequired'] == 'yes') && (trim($_POST['yahoo']) == ''))
				$yahooComplete = 'no';
		}elseif($wppb_defaultOptions['jabber'] == 'show'){
			if (($wppb_defaultOptions['jabberRequired'] == 'yes') && (trim($_POST['jabber']) == ''))
				$jabberComplete = 'no';
		}elseif($wppb_defaultOptions['bio'] == 'show'){
			if (($wppb_defaultOptions['bioRequired'] == 'yes') && (trim($_POST['description']) == ''))
				$bioComplete = 'no';
		}
		
		// check the extra fields also
		$wppb_premium = wppb_plugin_dir . '/premium/functions/';
		if (file_exists ( $wppb_premium.'extra.fields.php' )){
			$wppbFetchArray = get_option('wppb_custom_fields');
			foreach ( $wppbFetchArray as $key => $value){
				switch ($value['item_type']) {
					case "input":{
						$_POST[$value['item_id'].$value['id']] = apply_filters('wppb_register_input_custom_field_'.$value['id'], $_POST[$value['item_id'].$value['id']]);
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_id'].$value['id']]) == '')
									array_push($extraFieldsErrorHolder, $value['id']);
							}
						}
						break;					
					}
					case "checkbox":{
						$checkboxOption = '';
						$checkboxValue = explode(',', $value['item_options']);
						foreach($checkboxValue as $thisValue){
							$thisValue = str_replace(' ', '#@space@#', $thisValue); //we need to escape the space-codification we sent earlier in the post
							if (isset($_POST[$thisValue.$value['id']])){
								$localValue = str_replace('#@space@#', ' ', $_POST[$thisValue.$value['id']]);
								$checkboxOption = $checkboxOption.$localValue.',';
							}
						}
						
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($checkboxOption) == '')
									array_push($extraFieldsErrorHolder, $value['id']);
							}
						}
						break;
					}
					case "radio":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_id'].$value['id']]) == '')
									array_push($extraFieldsErrorHolder, $value['id']);
							}
						}
						break;
					}
					case "select":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_id'].$value['id']]) == '')
									array_push($extraFieldsErrorHolder, $value['id']);
							}
						}
						break;
					}
					case "countrySelect":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_id'].$value['id']]) == '')
									array_push($extraFieldsErrorHolder, $value['id']);
							}
						}
						break;
					}
					case "timeZone":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_id'].$value['id']]) == '')
									array_push($extraFieldsErrorHolder, $value['id']);
							}
						}
						break;
					}
					case "datepicker":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_id'].$value['id']]) == '')
									array_push($extraFieldsErrorHolder, $value['id']);
							}
						}
						break;
					}
					case "textarea":{
						if (isset($value['item_required'])){
							if ($value['item_required'] == 'yes'){
								if (trim($_POST[$value['item_id'].$value['id']]) == '')
									array_push($extraFieldsErrorHolder, $value['id']);
							}
						}
						break;
					}
					case "upload":{
						$uploadedfile = $value['item_type'].$value['id'];
						if ( (basename( $_FILES[$uploadedfile]['name']) == '')){
							if (isset($value['item_required'])){
								if ($value['item_required'] == 'yes')
										array_push($extraFieldsErrorHolder, $value['id']);
							}
						}
						break;
					}
					case "avatar":{

						$uploadedfile = $value['item_type'].$value['id'];

						if ( (basename( $_FILES[$uploadedfile]['name']) == '')){
							if (($_FILES[$uploadedfile]['type'] != 'image/jpeg') || ($_FILES[$uploadedfile]['type'] != 'image/jpg') || ($_FILES[$uploadedfile]['type'] != 'image/png') || ($_FILES[$uploadedfile]['type'] != 'image/bmp') || ($_FILES[$uploadedfile]['type'] != 'image/pjpeg') || ($_FILES[$uploadedfile]['type'] != 'image/x-png'))
								if (isset($value['item_required'])){
									if ($value['item_required'] == 'yes')
											array_push($extraFieldsErrorHolder, $value['id']);
								}
						}
						break;
					}
				}
			}
		}
		
		/* END check if all the required fields were completed */
		
		if ($registerFilterArray['extraError'] != '')
			$error = $registerFilterArray['extraError'];
		elseif ( !$userdata['user_login'] )
			$error = __('A username is required for registration.', 'profilebuilder');
		elseif ( username_exists($userdata['user_login']) )
			$error = __('Sorry, that username already exists!', 'profilebuilder');
		elseif ( !is_email($userdata['user_email'], true) )
			$error = __('You must enter a valid email address.', 'profilebuilder');
		elseif ( email_exists($userdata['user_email']) )
			$error = __('Sorry, that email address is already used!', 'profilebuilder');
		elseif (( empty($_POST['passw1'] ) || empty( $_POST['passw2'] )) || ( $_POST['passw1'] != $_POST['passw2'] )){
			if ( empty($_POST['passw1'] ) || empty( $_POST['passw2'] ))                                                    //verify if the user has completed both password fields
				$error = __('You didn\'t complete one of the password-fields!', 'profilebuilder');
			elseif ( $_POST['passw1'] != $_POST['passw2'] )																   //verify if the the password and the retyped password are a match
				$error = __('The entered passwords don\'t match!', 'profilebuilder');
		}
		elseif ( $agreed == false )
			$error = __('You must agree to the terms and conditions before registering!', 'profilebuilder');
		elseif(($firstnameComplete == 'no' || $lastnameComplete == 'no' ||	$nicknameComplete == 'no' || $websiteComplete == 'no' || $aimComplete == 'no' || $yahooComplete == 'no' ||	$jabberComplete == 'no' ||	$bioComplete == 'no' ) || !empty($extraFieldsErrorHolder))
			$error = __('The account was NOT created!', 'profilebuilder') .'<br/>'. __('(Several required fields were left uncompleted)', 'profilebuilder');
		else{
			$registered_name = $_POST['user_name'];
			$new_user = wp_insert_user( $userdata );
			
			/* add the extra profile information */
			$wppb_premium = wppb_plugin_dir . '/premium/functions/';
			if (file_exists ( $wppb_premium.'extra.fields.php' )){
				$wppbFetchArray = get_option('wppb_custom_fields');
				foreach ( $wppbFetchArray as $key => $value){
					switch ($value['item_type']) {
						case "input":{
							add_user_meta( $new_user, 'custom_field_'.$value['id'], esc_attr($_POST[$value['item_id'].$value['id']]) );
							break;
						}						
						case "hiddenInput":{
							add_user_meta( $new_user, 'custom_field_'.$value['id'], esc_attr($_POST[$value['item_id'].$value['id']]) );
							break;
						}
						case "checkbox":{
							$checkboxOption = '';
							$checkboxValue = explode(',', $value['item_options']);
							foreach($checkboxValue as $thisValue){
								$thisValue = str_replace(' ', '#@space@#', $thisValue); //we need to escape the space-codification we sent earlier in the post
								if (isset($_POST[$thisValue.$value['id']])){
									$localValue = str_replace('#@space@#', ' ', $_POST[$thisValue.$value['id']]);
									$checkboxOption = $checkboxOption.$localValue.',';
								}
							}							
							
							add_user_meta( $new_user, 'custom_field_'.$value['id'], $checkboxOption );
							break;
						}
						case "radio":{
							add_user_meta( $new_user, 'custom_field_'.$value['id'], $_POST[$value['item_id'].$value['id']] );
							break;
						}
						case "select":{
							add_user_meta( $new_user, 'custom_field_'.$value['id'], $_POST[$value['item_id'].$value['id']] );
							break;
						}
						case "countrySelect":{
							update_user_meta( $new_user, 'custom_field_'.$value['id'], $_POST[$value['item_id'].$value['id']] );
							break;
						}
						case "timeZone":{
							update_user_meta( $new_user, 'custom_field_'.$value['id'], $_POST[$value['item_id'].$value['id']] );
							break;
						}
						case "datepicker":{
							update_user_meta( $new_user, 'custom_field_'.$value['id'], $_POST[$value['item_id'].$value['id']] );
							break;
						}
						case "textarea":{
							add_user_meta( $new_user, 'custom_field_'.$value['id'], esc_attr($_POST[$value['item_id'].$value['id']]) );
							break;
						}
						case "upload":{
						
							$uploadedfile = $value['item_type'].$value['id'];
								
							//first we need to verify if we don't try to upload a 0b or 0 length file
							if ( (basename( $_FILES[$uploadedfile]['name']) != '')){
								
								//second we need to verify if the uploaded file size is less then the set file size in php.ini
								if (($_FILES[$uploadedfile]['size'] < ServerMaxUploadSizeByte) && ($_FILES[$uploadedfile]['size'] !=0)){
									//we need to prepare the basename of the file, so that ' becomes ` as ' gives an error
									$fileName = basename( $_FILES[$uploadedfile]['name']);
									$finalFileName = '';
									
									for ($i=0; $i < strlen($fileName); $i++){
										if ($fileName[$i] == "'")
											$finalFileName .= '`';
										else $finalFileName .= $fileName[$i];
									}
										
									//create the target path for uploading	
									$wpUploadPath = wp_upload_dir(); // Array of key => value pairs
									$target_path = $wpUploadPath['basedir']."/profile_builder/attachments/";
									$target_path = $target_path . 'userID_'.$new_user.'_attachment_'. $finalFileName;

									if (move_uploaded_file($_FILES[$uploadedfile]['tmp_name'], $target_path)){
										//$upFile = get_bloginfo('home').'/'.$target_path;
										$upFile = $wpUploadPath['baseurl'].'/profile_builder/attachments/userID_'.$new_user.'_attachment_'. $finalFileName;
										add_user_meta( $new_user, 'custom_field_'.$value['id'], $upFile );
										$pictureUpload = 'yes';
									}
								}
							}
							break;
						}
						case "avatar":{

							$uploadedfile = $value['item_type'].$value['id'];
							$wpUploadPath = wp_upload_dir(); // Array of key => value pairs
							$target_path_original = $wpUploadPath['basedir']."/profile_builder/avatars/";
							$fileName = $_FILES[$uploadedfile]['name'];
							$finalFileName = '';
									
							for ($i=0; $i < strlen($fileName); $i++){
								if ($fileName[$i] == "'")
									$finalFileName .= '`';
								elseif ($fileName[$i] == ' ')
									$finalFileName .= '_';
								else $finalFileName .= $fileName[$i];
							}
							
							$fileName = $finalFileName;

							$target_path = $target_path_original . 'userID_'.$new_user.'_originalAvatar_'. $fileName; 	
							
							/* when trying to upload file, be sure it's one of the accepted image file-types */
							if ( (($_FILES[$uploadedfile]['type'] == 'image/jpeg') || ($_FILES[$uploadedfile]['type'] == 'image/jpg') || ($_FILES[$uploadedfile]['type'] == 'image/png') || ($_FILES[$uploadedfile]['type'] == 'image/bmp') || ($_FILES[$uploadedfile]['type'] == 'image/pjpeg') || ($_FILES[$uploadedfile]['type'] == 'image/x-png')) && (($_FILES[$uploadedfile]['size'] < ServerMaxUploadSizeByte) && ($_FILES[$uploadedfile]['size'] !=0)) ){
								$wp_filetype = wp_check_filetype(basename( $_FILES[$uploadedfile]['name']), null );
								$attachment = array('post_mime_type' => $wp_filetype['type'],
													'post_title' => $fileName, //preg_replace('/\.[^.]+$/', '', basename($_FILES[$uploadedfile]['name'])),
													'post_content' => '',
													'post_status' => 'inherit'
													);


								$attach_id = wp_insert_attachment( $attachment, $target_path);
						
								$upFile = image_downsize( $attach_id, 'thumbnail' );
								$upFile = $upFile[0];
								
								//if file upload succeded			
								if (move_uploaded_file($_FILES[$uploadedfile]['tmp_name'], $target_path)){
									add_user_meta( $new_user, 'custom_field_'.$value['id'], $upFile );
									$avatarUpload = 'yes';
								}
								else $avatarUpload = 'no'; 
							}
							if (($_FILES[$uploadedfile]['type'] == ''))
								$avatarUpload = 'yes';
						
							break;
						}
					}
				}
			}
			
			
			//send an email to the admin regarding each and every new subscriber
			$bloginfo = get_bloginfo( 'name' );
			$registerFilterArray['adminMessageOnRegistration']  = ''; 
			$registerFilterArray['adminMessageOnRegistration']  = __('New subscriber on', 'profilebuilder') .' '.$bloginfo . "\r\n\r\n";
			$registerFilterArray['adminMessageOnRegistration'] .= __('Username', 'profilebuilder') .': '. esc_attr($_POST['user_name']) . "\r\n";
			$registerFilterArray['adminMessageOnRegistration'] .= __('E-mail', 'profilebuilder') .': '. esc_attr($_POST['email']) . "\r\n";
			$registerFilterArray['adminMessageOnRegistration'] = apply_filters('wppb_register_admin_message_content', $registerFilterArray['adminMessageOnRegistration']);
			
			$registerFilterArray['adminMessageOnRegistrationTitle'] = '['. $bloginfo .']'. __('A new subscriber has (been) registered!');
			$registerFilterArray['adminMessageOnRegistrationTitle'] = apply_filters ('wppb_register_admin_message_title', $registerFilterArray['adminMessageOnRegistrationTitle']);

			if (trim($registerFilterArray['adminMessageOnRegistration']) != '')
				wp_mail(get_option('admin_email'), $registerFilterArray['adminMessageOnRegistrationTitle'], $registerFilterArray['adminMessageOnRegistration']);

			
			//send an email to the newly registered user, if this option was selected
			if (isset($_POST['send_credentials_via_email']) && ($_POST['send_credentials_via_email'] == 'sending')){
				//change these variables to modify sent email message, destination and source.
				$email = $_POST['email'];
				$mailPassword = $_POST['passw1'];
				$mailUsername = $_POST['user_name'];				
				
				$registerFilterArray['userMessageFrom'] = get_bloginfo('name');
				$registerFilterArray['userMessageFrom'] = apply_filters('wppb_register_from_email_content', $registerFilterArray['userMessageFrom']);

				$registerFilterArray['userMessageSubject'] = 'A new account has been created for you.';
				$registerFilterArray['userMessageSubject'] = apply_filters('wppb_register_subject_email_content', $registerFilterArray['userMessageSubject']);
				
				$registerFilterArray['userMessageContent'] = 'Welcome to blog '.$registerFilterArray['userMessageFrom'].'. Your username is:'.$mailUsername.' and password:'.$mailPassword;
				$registerFilterArray['userMessageContent'] = apply_filters('wppb_register_email_content', $registerFilterArray['userMessageContent']);
				
				$messageSent = wp_mail( $email, $registerFilterArray['userMessageSubject'], $registerFilterArray['userMessageContent']);
				if( $messageSent == TRUE)
					$sentEmailStatus = 2; 
				else
					$sentEmailStatus = 1;
			}
			
		}
	}

?>
	<div class="wppb_holder" id="wppb_register">
<?php 	
		if ( is_user_logged_in() && !current_user_can( 'create_users' ) ) :

		global $user_ID; 
		$login = get_userdata( $user_ID );
		if($login->display_name == ''){ 
			$login->display_name = $login->user_login;
		}
			$registerFilterArray['loginLogoutError'] = '
				<p class="log-in-out alert">'. __('You are logged in as', 'profilebuilder') .' <a href="'.get_author_posts_url( $login->ID ).'" title="'.$login->display_name.'">'.$login->display_name.'</a>. '. __('You don\'t need another account.', 'profilebuilder') .' <a href="'.wp_logout_url(get_permalink()).'" title="'. __('Log out of this account.', 'profilebuilder') .'">'. __('Logout', 'profilebuilder') .'  &raquo;</a></p><!-- .log-in-out .alert -->';
			$registerFilterArray['loginLogoutError'] = apply_filters('wppb_register_have_account_alert', $registerFilterArray['loginLogoutError']);
			echo $registerFilterArray['loginLogoutError'];
			
		elseif ( $new_user != 'no' ) :
					if ( current_user_can( 'create_users' ) ){
						$registerFilterArray['registrationMessage1'] = '
							<p class="success">'. __('A user account has been created for', 'profilebuilder') .' '. $registered_name. '.</p><!-- .success -->';
						$registerFilterArray['registrationMessage1'] = apply_filters('wppb_register_account_created1', $registerFilterArray['registrationMessage1']);
						echo $registerFilterArray['registrationMessage1'];
						
						$wppb_addons = wppb_plugin_dir . '/premium/addon/';
						if (file_exists ( $wppb_addons.'addon.php' )){
							//check to see if the redirecting addon is present and activated
							$wppb_premium_addon_settings = get_option('wppb_premium_addon_settings');
							if ($wppb_premium_addon_settings['customRedirect'] == 'show'){
								//check to see if the redirect location is not an empty string and is activated
								$customRedirectSettings = get_option('customRedirectSettings');
								if ((trim($customRedirectSettings['afterRegisterTarget']) != '') && ($customRedirectSettings['afterRegister'] == 'yes')){
									$redirectLink = trim($customRedirectSettings['afterRegisterTarget']);
									$findHttp = strpos($redirectLink, 'http');
									if ($findHttp === false)
										$redirectLink = 'http://'. $redirectLink;
								}
							}
						}
						$registerFilterArray['redirectMessage1'] = '<font color="black">You will soon be redirected automatically. If you see this page for more than 3 second, please click <a href="'.$redirectLink.'">here</a>.<meta http-equiv="Refresh" content="3;url='.$redirectLink.'" /></font><br/><br/>';	
						$registerFilterArray['redirectMessage1'] = apply_filters('wppb_register_redirect_after_creation1', $registerFilterArray['redirectMessage1']);
						echo $registerFilterArray['redirectMessage1'];			
						
					}else{
						$registerFilterArray['registrationMessage2'] = '
							<p class="success">'. __('Thank you for registering', 'profilebuilder') .' '. $registered_name .'.</p><!-- .success -->';
						$registerFilterArray['registrationMessage2'] = apply_filters('wppb_register_account_created2', $registerFilterArray['registrationMessage2']);
						echo $registerFilterArray['registrationMessage2'];
						
						$wppb_addons = wppb_plugin_dir . '/premium/addon/';
						if (file_exists ( $wppb_addons.'addon.php' )){
							//check to see if the redirecting addon is present and activated
							$wppb_premium_addon_settings = get_option('wppb_premium_addon_settings');
							if ($wppb_premium_addon_settings['customRedirect'] == 'show'){
								//check to see if the redirect location is not an empty string and is activated
								$customRedirectSettings = get_option('customRedirectSettings');
								if ((trim($customRedirectSettings['afterRegisterTarget']) != '') && ($customRedirectSettings['afterRegister'] == 'yes')){
									$redirectLink = trim($customRedirectSettings['afterRegisterTarget']);
									$findHttp = strpos($redirectLink, 'http');
									if ($findHttp === false)
										$redirectLink = 'http://'. $redirectLink;
								}
							}
						}
						$registerFilterArray['redirectMessage2'] = '<font color="black">You will soon be redirected automatically. If you see this page for more than 3 second, please click <a href="'.$redirectLink.'">here</a>.<meta http-equiv="Refresh" content="3;url='.$redirectLink.'" /></font><br/><br/>';	
						$registerFilterArray['redirectMessage2'] = apply_filters('wppb_register_redirect_after_creation2', $registerFilterArray['redirectMessage2']);
						echo $registerFilterArray['redirectMessage2'];
					}

			
				if(isset($_POST['send_credentials_via_email'])){
					if ($sentEmailStatus == 1){
						$registerFilterArray['emailMessage1'] = '<p class="error">'. __('An error occured while trying to send the notification email.', 'profilebuilder') .'</p><!-- .error -->';
						$registerFilterArray['emailMessage1'] = apply_filters('wppb_register_send_notification_email_fail', $registerFilterArray['emailMessage1']);
						echo $registerFilterArray['emailMessage1'];
					}elseif ($sentEmailStatus == 2){
						$registerFilterArray['emailMessage2'] = '<p class="success">'. __('An email containing the username and password was successfully sent.', 'profilebuilder') .'</p><!-- .success -->';
						$registerFilterArray['emailMessage2'] = apply_filters('wppb_register_send_notification_email_success', $registerFilterArray['emailMessage2']);
						echo $registerFilterArray['emailMessage2'];
					}
				}
?>
<?php			
			else :
				if ( $error ) : 
					$registerFilterArray['errorMessage'] = '<p class="error">'. $error .'</p><!-- .error -->';
					$registerFilterArray['errorMessage'] = apply_filters('wppb_register_error_messaging', $registerFilterArray['errorMessage']);
					echo $registerFilterArray['errorMessage'];
				endif;
			
				if ( current_user_can( 'create_users' ) && $registration ) :
					$registerFilterArray['alertMessage1'] = '<p class="alert">'. __('Users can register themselves or you can manually create users here.', 'profilebuilder') .'</p><!-- .alert -->';
					$registerFilterArray['alertMessage1'] = apply_filters('wppb_register_alert_messaging1', $registerFilterArray['alertMessage1']);
					echo $registerFilterArray['alertMessage1'];					
					
				elseif ( current_user_can( 'create_users' ) ) :
					$registerFilterArray['alertMessage2'] = '<p class="alert">'. __('Users cannot currently register themselves, but you can manually create users here.', 'profilebuilder') .'</p><!-- .alert -->';
					$registerFilterArray['alertMessage2'] = apply_filters('wppb_register_alert_messaging2', $registerFilterArray['alertMessage2']);
					echo $registerFilterArray['alertMessage2'];
					
				elseif ( !current_user_can( 'create_users' ) && !$registration) :
					$registerFilterArray['alertMessage3'] = '<p class="alert">'. __('Only an administrator can add new users.', 'profilebuilder') .'</p><!-- .alert -->';
					$registerFilterArray['alertMessage3'] = apply_filters('wppb_register_alert_messaging3', $registerFilterArray['alertMessage3']);
					echo $registerFilterArray['alertMessage3'];				
				endif;

				if ( $registration || current_user_can( 'create_users' ) ) :
					/* use this action hook to add extra content before the register form. */
					do_action( 'wppb_before_register_fields' );
?>
					<form enctype="multipart/form-data" method="post" id="adduser" class="user-forms" action="http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
<?php 					
						echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.ServerMaxUploadSizeByte.'" /><!-- set the MAX_FILE_SIZE to the server\'s current max upload size in bytes -->'; 

						$registerFilterArray['name1'] = '<p class="registerNameHeading"><strong>'. __('Name', 'profilebuilder') .'</strong></p>';
						$registerFilterArray['name1'] = apply_filters('wppb_register_content_name1', $registerFilterArray['name1']);
						echo $registerFilterArray['name1'];	
						
						if ($wppb_defaultOptions['username'] == 'show'){
							$errorMark = '';
							if ($wppb_defaultOptions['usernameRequired'] == 'yes'){
								$errorMark = '<font color="red" title="This field is required for registration.">*</font>';
							}
						
							$localVar = '';
							if (isset($_POST['user_name']))
								$localVar = $_POST['user_name'];
							$registerFilterArray['name2'] = '
								<p class="form-username">
									<label for="user_name">'. __('Username', 'profilebuilder') .$errorMark.'</label>
									<input class="text-input" name="user_name" type="text" id="user_name" value="'.trim($localVar).'" />
									<span class="wppb-description-delimiter">'. __('(required)', 'profilebuilder') .'</span>
								</p><!-- .form-username -->';
							$registerFilterArray['name2'] = apply_filters('wppb_register_content_name2', $registerFilterArray['name2']);
							echo $registerFilterArray['name2'];
						}
						
						if ($wppb_defaultOptions['firstname'] == 'show'){
								$errorVar = '';
								$errorMark = '';
								if ($wppb_defaultOptions['firstnameRequired'] == 'yes'){
									$errorMark = '<font color="red" title="This field is marked as required by the administrator.">*</font>';
									if (isset($_POST['first_name'])){
										if (trim($_POST['first_name']) == ''){
											$errorMark = '<img src="'.wppb_plugin_url . '/assets/images/pencil_delete.png" title="This field must be filled out before registering (It was marked as required by the administrator)."/>';
											$errorVar = ' errorHolder';
										}
									}
								}
								
							$localVar = '';
							if (isset($_POST['first_name']))
								$localVar = $_POST['first_name'];
							$registerFilterArray['name3'] = '
								<p class="first_name'.$errorVar.'">
									<label for="first_name">'. __('First Name', 'profilebuilder') .$errorMark.'</label>
									<input class="text-input" name="first_name" type="text" id="first_name" value="'.trim($localVar).'" />
								</p><!-- .first_name -->';
							$registerFilterArray['name3'] = apply_filters('wppb_register_content_name3', $registerFilterArray['name3']);
							echo $registerFilterArray['name3'];
						}

						if ($wppb_defaultOptions['lastname'] == 'show'){ 
							$errorVar = '';
							$errorMark = '';
							if ($wppb_defaultOptions['lastnameRequired'] == 'yes'){
								$errorMark = '<font color="red" title="This field is marked as required by the administrator.">*</font>';
								if (isset($_POST['last_name'])){
									if (trim($_POST['last_name']) == ''){
										$errorMark = '<img src="'.wppb_plugin_url . '/assets/images/pencil_delete.png" title="This field must be filled out before registering (It was marked as required by the administrator)."/>';
										$errorVar = ' errorHolder';
									}
								}
							}
							
							$localVar = '';
							if (isset($_POST['last_name']))
								$localVar = $_POST['last_name'];
							$registerFilterArray['name4'] = '
								<p class="last_name'.$errorVar.'">
									<label for="last_name">'. __('Last Name', 'profilebuilder') .$errorMark.'</label>
									<input class="text-input" name="last_name" type="text" id="last_name" value="'.trim($localVar).'" />
								</p><!-- .last_name -->';
							$registerFilterArray['name4'] = apply_filters('wppb_register_content_name4', $registerFilterArray['name4']);
							echo $registerFilterArray['name4'];
						}

						if ($wppb_defaultOptions['nickname'] == 'show'){
							$errorVar = '';
							$errorMark = '';
							if ($wppb_defaultOptions['nicknameRequired'] == 'yes'){
								$errorMark = '<font color="red" title="This field is marked as required by the administrator.">*</font>';
								if (isset($_POST['nickname'])){
									if (trim($_POST['nickname']) == ''){
										$errorMark = '<img src="'.wppb_plugin_url . '/assets/images/pencil_delete.png" title="This field must be filled out before registering (It was marked as required by the administrator)."/>';
										$errorVar = ' errorHolder';
									}
								}
							}
							
							$localVar = '';
							if (isset($_POST['nickname']))
								$localVar = $_POST['nickname'];
							$registerFilterArray['name5'] = '
								<p class="nickname'.$errorVar.'">
									<label for="nickname">'. __('Nickname', 'profilebuilder') .$errorMark.'</label>
									<input class="text-input" name="nickname" type="text" id="nickname" value="'.trim($localVar).'" />
								</p><!-- .nickname -->';
							$registerFilterArray['name5'] = apply_filters('wppb_register_content_name5', $registerFilterArray['name5']);
							echo $registerFilterArray['name5'];
						}

						$registerFilterArray['info1'] = '<p class="registerContactInfoHeading"><strong>'. __('Contact Info', 'profilebuilder') .'</strong></p>';
						$registerFilterArray['info1'] = apply_filters('wppb_register_content_info1', $registerFilterArray['info1']);
						echo $registerFilterArray['info1'];			

						if ($wppb_defaultOptions['email'] == 'show'){
							$errorVar = '';
							$errorMark = '';
							if ($wppb_defaultOptions['emailRequired'] == 'yes'){
								$errorMark = '<font color="red" title="This field is marked as required by the administrator.">*</font>';
								if (isset($_POST['email']))
									if (trim($_POST['email']) == '')
										$errorMark = '<img src="'.wppb_plugin_url . '/assets/images/pencil_delete.png" title="This field is required for registration."/>';
							}
							
							$localVar = '';
							if (isset($_POST['email']))
								$localVar = $_POST['email'];
							$registerFilterArray['info2'] = '
								<p class="form-email">
									<label for="email">'. __('E-mail', 'profilebuilder') .$errorMark.'</label>
									<input class="text-input" name="email" type="text" id="email" value="'.trim($localVar).'" />
									<span class="wppb-description-delimiter">'. __('(required)', 'profilebuilder') .'</span>
								</p><!-- .form-email -->';
							$registerFilterArray['info2'] = apply_filters('wppb_register_content_info2', $registerFilterArray['info2']);
							echo $registerFilterArray['info2'];	
						}

						if ($wppb_defaultOptions['website'] == 'show'){
							$errorVar = '';
							$errorMark = '';
							if ($wppb_defaultOptions['websiteRequired'] == 'yes'){
								$errorMark = '<font color="red" title="This field is marked as required by the administrator.">*</font>';
								if (isset($_POST['website'])){
									if (trim($_POST['website']) == ''){
										$errorMark = '<img src="'.wppb_plugin_url . '/assets/images/pencil_delete.png" title="This field must be filled out before registering (It was marked as required by the administrator)."/>';
										$errorVar = ' errorHolder';
									}
								}
							}
						
							$localVar = '';
							if (isset($_POST['website']))
								$localVar = $_POST['website'];
							$registerFilterArray['info3'] = '
								<p class="form-website'.$errorVar.'">
									<label for="website">'. __('Website', 'profilebuilder') .$errorMark.'</label>
									<input class="text-input" name="website" type="text" id="website" value="'.trim($localVar).'" />
								</p><!-- .form-website -->';
							$registerFilterArray['info3'] = apply_filters('wppb_register_content_info3', $registerFilterArray['info3']);
							echo $registerFilterArray['info3'];	
						}

						if ($wppb_defaultOptions['aim'] == 'show'){
							$errorVar = '';
								$errorMark = '';
								if ($wppb_defaultOptions['aimRequired'] == 'yes'){
									$errorMark = '<font color="red" title="This field is marked as required by the administrator.">*</font>';
									if (isset($_POST['aim'])){
										if (trim($_POST['aim']) == ''){
											$errorMark = '<img src="'.wppb_plugin_url . '/assets/images/pencil_delete.png" title="This field must be filled out before registering (It was marked as required by the administrator)."/>';
											$errorVar = ' errorHolder';
										}
									}
								}					

							$localVar = '';
							if (isset($_POST['aim']))
								$localVar = $_POST['aim'];
							$registerFilterArray['info4'] = '
								<p class="form-aim'.$errorVar.'">
									<label for="aim">'. __('AIM', 'profilebuilder') .$errorMark.'</label>
									<input class="text-input" name="aim" type="text" id="aim" value="'.trim($localVar).'" />
								</p><!-- .form-aim -->';
							$registerFilterArray['info4'] = apply_filters('wppb_register_content_info4', $registerFilterArray['info4']);
							echo $registerFilterArray['info4'];	
						}

						if ($wppb_defaultOptions['yahoo'] == 'show'){
							$errorVar = '';
							$errorMark = '';
							if ($wppb_defaultOptions['yahooRequired'] == 'yes'){
								$errorMark = '<font color="red" title="This field is marked as required by the administrator.">*</font>';
								if (isset($_POST['yim'])){
									if (trim($_POST['yim']) == ''){
										$errorMark = '<img src="'.wppb_plugin_url . '/assets/images/pencil_delete.png" title="This field must be filled out before registering (It was marked as required by the administrator)."/>';
										$errorVar = ' errorHolder';
									}
								}
							}
							
							$localVar = '';
							if (isset($_POST['yim']))
								$localVar = $_POST['yim'];
							$registerFilterArray['info5'] = '
								<p class="form-yim'.$errorVar.'">
									<label for="yim">'. __('Yahoo IM', 'profilebuilder') .$errorMark.'</label>
									<input class="text-input" name="yim" type="text" id="yim" value="'.trim($localVar).'" />
								</p><!-- .form-yim -->';
							$registerFilterArray['info5'] = apply_filters('wppb_register_content_info5', $registerFilterArray['info5']);
							echo $registerFilterArray['info5'];	
						}

						if ($wppb_defaultOptions['jabber'] == 'show'){
							$errorVar = '';
							$errorMark = '';
							if ($wppb_defaultOptions['jabberRequired'] == 'yes'){
								$errorMark = '<font color="red" title="This field is marked as required by the administrator.">*</font>';
								if (isset($_POST['jabber'])){
									if (trim($_POST['jabber']) == ''){
										$errorMark = '<img src="'.wppb_plugin_url . '/assets/images/pencil_delete.png" title="This field must be filled out before registering (It was marked as required by the administrator)."/>';
										$errorVar = ' errorHolder';
									}
								}
							}						
						
							$localVar = '';
							if (isset($_POST['jabber']))
								$localVar = $_POST['jabber'];
							$registerFilterArray['info6'] = '
								<p class="form-jabber'.$errorVar.'">
									<label for="jabber">'. __('Jabber / Google Talk', 'profilebuilder') .$errorMark.'</label>
									<input class="text-input" name="jabber" type="text" id="jabber" value="'.trim($localVar).'" />
								</p><!-- .form-jabber -->';
							$registerFilterArray['info6'] = apply_filters('wppb_register_content_info6', $registerFilterArray['info6']);
							echo $registerFilterArray['info6'];	
						}
						
						$registerFilterArray['ay1'] = '<p class="registerAboutYourselfHeader"><strong>'. __('About Yourself', 'profilebuilder') .'</strong></p>';
						$registerFilterArray['ay1'] = apply_filters('wppb_register_content_about_yourself1', $registerFilterArray['ay1']);
						echo $registerFilterArray['ay1'];	
						
						if ($wppb_defaultOptions['bio'] == 'show'){
							$errorVar = '';
							$errorMark = '';
							if ($wppb_defaultOptions['bioRequired'] == 'yes'){
								$errorMark = '<font color="red" title="This field is marked as required by the administrator.">*</font>';
								if (isset($_POST['description'])){
									if (trim($_POST['description']) == ''){
										$errorMark = '<img src="'.wppb_plugin_url . '/assets/images/pencil_delete.png" title="This field must be filled out before registering (It was marked as required by the administrator)."/>';
										$errorVar = ' errorHolder';
									}
								}
							}
							
							$localVar = '';
							if (isset($_POST['description']))
								$localVar = $_POST['description'];
							$registerFilterArray['ay2'] = '
								<p class="form-description'.$errorVar.'">
									<label for="description">'. __('Biographical Info', 'profilebuilder') .$errorMark.'</label>
									<textarea class="text-input" name="description" id="description" rows="5" cols="30">'.trim($localVar).'</textarea>
								</p><!-- .form-description -->';
							$registerFilterArray['ay2'] = apply_filters('wppb_register_content_about_yourself2', $registerFilterArray['ay2']);
							echo $registerFilterArray['ay2'];	
						}

						if ($wppb_defaultOptions['password'] == 'show'){
							$errorMark = '';
							if ($wppb_defaultOptions['passwordRequired'] == 'yes'){
								$errorMark = '<font color="red" title="This field is required for registration.">*</font>';
							}
							
							$localVar1 = '';
							if (isset($_POST['passw1']))
								$localVar1 = $_POST['passw1'];
							$localVar2 = '';
							if (isset($_POST['passw2']))
								$localVar2 = $_POST['passw2'];
							$registerFilterArray['ay3'] = '
								<p class="form-password">
									<label for="pass1">'. __('Password', 'profilebuilder') .$errorMark.'</label>
									<input class="text-input" name="passw1" type="password" id="pass1" value="'.trim($localVar).'" />
								</p><!-- .form-password -->
				 
								<p class="form-password">
									<label for="pass2">'. __('Repeat Password', 'profilebuilder') .$errorMark.'</label>
									<input class="text-input" name="passw2" type="password" id="pass2" value="'.trim($localVar2).'" />
								</p><!-- .form-password -->';
							$registerFilterArray['ay3'] = apply_filters('wppb_register_content_about_yourself3', $registerFilterArray['ay3']);
							echo $registerFilterArray['ay3'];	
						}

							$wppb_premium = wppb_plugin_dir . '/premium/functions/';
							if (file_exists ( $wppb_premium.'extra.fields.php' )){
								require_once($wppb_premium.'extra.fields.php');
								register_user_extra_fields($error, $_POST, $extraFieldsErrorHolder);
							}

							if (isset($_POST['send_credentials_via_email'])) 
								$checkedVar = ' checked';
							else $checkedVar = '';
							$registerFilterArray['confirmationEmailForm'] = '
								<p class="send-confirmation-email">
									<label for="send-confirmation-email"> 
										<input id="send_credentials_via_email" type="checkbox" name="send_credentials_via_email" value="sending"'. $checkedVar .'/>
										<span class="wppb-description-delimiter"> '. __('Send these credentials via email.', 'profilebuilder') .'</span>
									</label>
								</p><!-- .send-confirmation-email -->';
							$registerFilterArray['confirmationEmailForm'] = apply_filters('wppb_register_confirmation_email_form', $registerFilterArray['confirmationEmailForm']);
							echo $registerFilterArray['confirmationEmailForm'];	
?>
							
						<p class="form-submit">
							<input name="adduser" type="submit" id="addusersub" class="submit button" value="<?php if ( current_user_can( 'create_users' ) ) _e('Add User', 'profilebuilder'); else _e('Register', 'profilebuilder'); ?>" />
							<input name="action" type="hidden" id="action" value="adduser" />
						</p><!-- .form-submit -->
<?php 
						wp_nonce_field('verify_true_registration','register_nonce_field'); 
?>
					</form><!-- #adduser -->

<?php	
				endif;
			endif;
		
		/* use this action hook to add extra content after the register form. */
		do_action( 'wppb_after_register_fields' );
?>
	
	</div>
<?php
	$output = ob_get_contents();
    ob_end_clean();
	
	$registerFilterArray = apply_filters('wppb_register', $registerFilterArray);
	
    return $output;
}
?>