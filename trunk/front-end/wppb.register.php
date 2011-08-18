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
	get_currentuserinfo(); 
	
	/* Load registration file. */
	require_once( ABSPATH . WPINC . '/registration.php' );

	/* Check if users can register. */
	$registration = get_option( 'users_can_register' );
	
	
	//fallback if the file was largen then post_max_size, case in which no errors can be saved in $_FILES[fileName]['error']	
	if (empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {  
		echo '<p class="error">';
		 _e('The information size you were trying to submit was larger then '. ServerMaxUploadSizeMega .'b!<br/>', 'profilebuilder');
		 _e('This is usually caused by a large file(s) trying to be uploaded.<br/>', 'profilebuilder');
		 _e('Since it was also larger than '. ServerMaxPostSizeMega .'b no additional information is available.<br/>', 'profilebuilder');
		 _e('The user was NOT created!', 'profilebuilder');
		echo '</p>';
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
	
		$user_pass = esc_attr( $_POST['passw1'] );
		$userdata = array(
			'user_pass' => $user_pass,
			'user_login' => esc_attr( $_POST['user_name'] ),
			'first_name' => esc_attr( $_POST['first_name'] ),
			'last_name' => esc_attr( $_POST['last_name'] ),
			'nickname' => esc_attr( $_POST['nickname'] ),
			'user_email' => esc_attr( $_POST['email'] ),
			'user_url' => esc_attr( $_POST['website'] ),
			'aim' => esc_attr( $_POST['aim'] ),
			'yim' => esc_attr( $_POST['yim'] ),
			'jabber' => esc_attr( $_POST['jabber'] ),
			'description' => esc_attr( $_POST['description'] ),
			'role' => $aprovedRole);
			
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
		
		if ( !$userdata['user_login'] )
			$error = __('A username is required for registration.', 'profilebuilder');
		elseif ( username_exists($userdata['user_login']) )
			$error = __('Sorry, that username already exists!', 'profilebuilder');
		
		elseif ( !is_email($userdata['user_email'], true) )
			$error = __('You must enter a valid email address.', 'profilebuilder');
		elseif ( email_exists($userdata['user_email']) )
			$error = __('Sorry, that email address is already used!', 'profilebuilder');
		elseif (( empty($_POST['passw1'] ) || empty( $_POST['passw2'] )) || ( $_POST['pass1'] != $_POST['pass2'] )){
				if ( empty($_POST['passw1'] ) || empty( $_POST['passw2'] ))                                                    //verify if the user has completed both password fields
					$error = __('You didn\'t complete one of the password-fields!', 'profilebuilder');
				elseif ( $_POST['pass1'] != $_POST['pass2'] )																   //verify if the the password and the retyped password are a match
					$error = __('The entered passwords don\'t match!', 'profilebuilder');
			}
		elseif ( $agreed == false )
			$error = __('You must agree to the terms and conditions before registering!', 'profilebuilder');
		
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
									$target_path = "wp-content/uploads/profile_builder/attachments/";
									$target_path = $target_path . 'userID_'.$new_user.'_attachment_'. $finalFileName;

									if (move_uploaded_file($_FILES[$uploadedfile]['tmp_name'], $target_path)){
										$upFile = get_bloginfo('home').'/'.$target_path;
										add_user_meta( $new_user, 'custom_field_'.$value['id'], $upFile );
										$pictureUpload = 'yes';
									}
								}
							}
							break;
						}
						case "avatar":{

							$uploadedfile = $value['item_type'].$value['id'];
							$target_path_original = "wp-content/uploads/profile_builder/avatars/";
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
			$mailMessage  = ''; 
			$mailMessage  = sprintf(__('New subscriber on %s:'), $bloginfo) . "\r\n\r\n";
			$mailMessage .= sprintf(__('Username: %s'), esc_attr( $_POST['user_name'] )) . "\r\n";
			$mailMessage .= sprintf(__('E-mail: %s'), esc_attr( $_POST['email'] )) . "\r\n";

			wp_mail(get_option('admin_email'), sprintf(__('[%s] A new subscriber has (been) registered!'), $bloginfo), $mailMessage);

			
			//send an email to the newly registered user, if this option was selected
			if (isset($_POST['send_credentials_via_email']) && ($_POST['send_credentials_via_email'] == 'sending')){
				$email = $_POST['email'];                                 //change these variables to modify sent email message, destination and source.
				$fromemail = get_bloginfo('name');
				$mailPassword = $_POST['passw1'];
				$mailUsername = $_POST['user_name'];
				$subject = 'A new account has been created for you.';
				$msg = 'Welcome to blog '.$fromemail.'. Your username is:'.$mailUsername.' and password:'.$mailPassword;
				$messageSent = wp_mail( $email, $subject, $msg);
				if( $messageSent == TRUE)
					$sentEmailStatus = 2;
				else
					$sentEmailStatus = 1;
			}
			
		}
	}

?>
	<div class="wppb_holder" id="wppb_register">
	<?php if ( is_user_logged_in() && !current_user_can( 'create_users' ) ) : ?>
		<?php
		global $user_ID; 
		$login = get_userdata( $user_ID );
		if($login->display_name == ''){ 
			$login->display_name = $login->user_login;
		}
		?>
			<p class="log-in-out alert">
			<?php printf( __('You are logged in as <a href="%1$s" title="%2$s">%2$s</a>.  You don\'t need another account.', 'profilebuilder'), get_author_posts_url( $login->ID ), $login->display_name ); ?> <a href="<?php echo wp_logout_url( get_permalink() ); ?>" title="<?php _e('Log out of this account', 'profilebuilder'); ?>"><?php _e('Logout &raquo;', 'profilebuilder'); ?></a>
			</p><!-- .log-in-out .alert -->

		<?php elseif ( $new_user != 'no' ) : ?>
			
			
			<?php
					
					if ( current_user_can( 'create_users' ) ){
						echo'<p class="success">';
						printf( __('A user account for %1$s has been created.', 'profilebuilder'), $registered_name );
						echo'</p><!-- .success -->';
						
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
						echo '<font color="black">You will soon be redirected automatically. If you see this page for more than 3 second, please click <a href="'.$redirectLink.'">here</a>.<meta http-equiv="Refresh" content="3;url='.$redirectLink.'" /></font>';
						echo '<br/><br/>';						
						
					}else{
						echo'<p class="success">';
						printf( __('Thank you for registering, %1$s.', 'profilebuilder'), $registered_name );
						echo'</p><!-- .success -->';
						
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
						echo '<font color="black">You will soon be redirected automatically. If you see this page for more than 3 second, please click <a href="'.$redirectLink.'">here</a>.<meta http-equiv="Refresh" content="3;url='.$redirectLink.'" /></font>';
						echo '<br/><br/>';
					}
					
			?>
			
			<?php
				if(isset($_POST['send_credentials_via_email'])){
					if ($sentEmailStatus == 1){
						echo '<p class="error">';
						echo 'An error occured while trying to send the notification email.';
						echo '</p><!-- .error -->';
					}elseif ($sentEmailStatus == 2){
						echo '<p class="success">';
						echo 'An email containing the username and password was successfully sent.';
						echo '</p><!-- .success -->';
					}
				}	
			?>
		<?php else : ?>

			<?php if ( $error ) : ?>
				<p class="error">
					<?php echo $error; ?>
				</p><!-- .error -->
			<?php endif; ?>	
			
			<?php if ( current_user_can( 'create_users' ) && $registration ) : ?>
				<p class="alert">
					<?php _e('Users can register themselves or you can manually create users here.', 'profilebuilder'); ?>
				</p><!-- .alert -->
			<?php elseif ( current_user_can( 'create_users' ) ) : ?>
				<p class="alert">
					<?php _e('Users cannot currently register themselves, but you can manually create users here.', 'profilebuilder'); ?>
				</p><!-- .alert -->
			<?php elseif ( !current_user_can( 'create_users' ) && !$registration) : ?>
				<p class="alert">
					<?php _e('Only an administrator can add new users.', 'profilebuilder'); ?>
				</p><!-- .alert -->
				
			<?php endif; ?>

			<?php if ( $registration || current_user_can( 'create_users' ) ) : ?>

			<?php /* use this action hook to add extra content before the register form. */ ?>
			<?php do_action( 'wppb_before_register_fields' ); ?> 
			
			<form enctype="multipart/form-data" method="post" id="adduser" class="user-forms" action="http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
				<?php echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.ServerMaxUploadSizeByte.'" />'; ?> <!-- set the MAX_FILE_SIZE to the server's current max upload size in bytes -->
			
				<p>
				<strong><?php _e('Name', 'profilebuilder');?></strong>
				</p>
				
				<?php
				if ($wppb_defaultOptions['username'] == 'show'){ 
					if (isset($_POST['user_name']))
						$localUserName = esc_html($_POST['user_name']);
					else $localUserName = '';
				
					echo'
					<p class="form-username">
						<label for="user_name">'; _e('Username', 'profilebuilder');  echo '</label>
						<input class="text-input" name="user_name" type="text" id="user_name" value="'.$localUserName.'" />
						<span class="wppb-description-delimiter">'; _e('(required)', 'profilebuilder');echo'</span>
					</p><!-- .form-username -->';
				}
				?>
				
				<?php
				if ($wppb_defaultOptions['firstname'] == 'show'){ 
					if (isset($_POST['first_name']))
						$localFirstName = esc_html($_POST['first_name']);
					else $localFirstName = '';
					
					echo'
					<p class="first_name">
						<label for="first_name">'; _e('First Name', 'profilebuilder'); echo'</label>
						<input class="text-input" name="first_name" type="text" id="first_name" value="'.$localFirstName.'" />
					</p><!-- .first_name -->';
				}
				?>
				
				<?php 
				if ($wppb_defaultOptions['lastname'] == 'show'){ 
					if (isset($_POST['last_name']))
						$localLastName = esc_html($_POST['last_name']);
					else $localLastName = '';
					
					echo'
					<p class="last_name">
						<label for="last_name">'; _e('Last Name', 'profilebuilder'); echo'</label>
						<input class="text-input" name="last_name" type="text" id="last_name" value="'.$localLastName.'" />
					</p><!-- .last_name -->';
				}
				?>
				
				<?php 
				if ($wppb_defaultOptions['nickname'] == 'show'){ 
					if (isset($_POST['nickname']))
						$localNickName = esc_html($_POST['nickname']);
					else $localNickName = '';
					
					echo'
					<p class="nickname">
						<label for="nickname">'; _e('Nickname', 'profilebuilder'); echo'</label>
						<input class="text-input" name="nickname" type="text" id="nickname" value="'.$localNickName.'" />
					</p><!-- .nickname -->';
				}
				?>
				
				<p>
				<strong><?php _e('Contact Info', 'profilebuilder');?></strong>
				</p>
				
				<?php 
				if ($wppb_defaultOptions['email'] == 'show'){ 
					if (isset($_POST['email']))
						$localEmail = esc_html($_POST['email']);
					else $localEmail = '';
								
					echo'
					<p class="form-email">
						<label for="email">'; _e('E-mail', 'profilebuilder'); echo '</label>
						<input class="text-input" name="email" type="text" id="email" value="'.$localEmail.'" />
						<span class="wppb-description-delimiter">'; _e('(required)', 'profilebuilder');echo'</span>
					</p><!-- .form-email -->';
				}
				?>
				
				<?php 
				if ($wppb_defaultOptions['website'] == 'show'){ 
					if (isset($_POST['website']))
						$localWebsite = esc_html($_POST['website']);
					else $localWebsite = '';
				
					echo'
					<p class="form-website">
						<label for="website">'; _e('Website', 'profilebuilder'); echo'</label>
						<input class="text-input" name="website" type="text" id="website" value="'.$localWebsite.'" />
					</p><!-- .form-website -->';
				}
				?>
				
				<?php 
				if ($wppb_defaultOptions['aim'] == 'show'){ 
					if (isset($_POST['aim']))
						$localAim = esc_html($_POST['aim']);
					else $localAim = '';
			
					echo'
					<p class="form-aim">
						<label for="aim">'; _e('AIM', 'profilebuilder'); echo'</label>
						<input class="text-input" name="aim" type="text" id="aim" value="'.$localAim.'" />
					</p><!-- .form-aim -->';
				}
				?>
				
				<?php 
				if ($wppb_defaultOptions['yahoo'] == 'show'){ 
					if (isset($_POST['yim']))
						$localYim = esc_html($_POST['yim']);
					else $localYim = '';
					
					echo'
					<p class="form-yim">
						<label for="yim">'; _e('Yahoo IM', 'profilebuilder'); echo'</label>
						<input class="text-input" name="yim" type="text" id="yim" value="'.$localYim.'" />
					</p><!-- .form-yim -->';
				}
				?>
				
				<?php 
				if ($wppb_defaultOptions['jabber'] == 'show'){ 
					if (isset($_POST['jabber']))
						$localJabber = esc_html($_POST['jabber']);
					else $localJabber = '';
					
					echo'
					<p class="form-jabber">
						<label for="jabber">'; _e('Jabber / Google Talk', 'profilebuilder'); echo'</label>
						<input class="text-input" name="jabber" type="text" id="jabber" value="'.$localJabber.'" />
					</p><!-- .form-jabber -->';
				}
				?>
				
				<p>
				<strong><?php _e('About Yourself', 'profilebuilder');?></strong>
				</p>
				
				<?php 
				if ($wppb_defaultOptions['bio'] == 'show'){ 
					if (isset($_POST['description']))
						$localDescription = esc_html($_POST['description']);
					else $localDescription = '';
					
					echo'
					<p class="form-description">
						<label for="description">'; _e('Biographical Info', 'profilebuilder'); echo'</label>
						<textarea class="text-input" name="description" id="description" rows="5" cols="30">'.$localDescription.'</textarea>
					</p><!-- .form-description -->';
				}
				?>
				
				<?php 
				if ($wppb_defaultOptions['password'] == 'show'){
					if (isset($_POST['pass1']))
						$localPass1 = $_POST['pass1'];
					else $localPass1 = '';
					
					if (isset($_POST['pass2']))
						$localPass2 = $_POST['pass2'];
					else $localPass2 = '';
					
					echo'
					<p class="form-password">
						<label for="pass1">'; _e('Password', 'profilebuilder'); echo'</label>
						<input class="text-input" name="passw1" type="password" id="pass1" value="'.$localPass1.'" />
					</p><!-- .form-password -->
	 
					<p class="form-password">
						<label for="pass2">'; _e('Repeat Password', 'profilebuilder'); echo'</label>
						<input class="text-input" name="passw2" type="password" id="pass2" value="'.$localPass2.'" />
					</p><!-- .form-password -->';
				}
				?>
				
				<?php
					$wppb_premium = wppb_plugin_dir . '/premium/functions/';
					if (file_exists ( $wppb_premium.'extra.fields.php' )){
						require_once($wppb_premium.'extra.fields.php');
						register_user_extra_fields($error, $_POST);
					}
				?>
				
				<?php
						echo '
						<p class="send-confirmation-email">
						<label for="send-confirmation-email">'; 
						echo'<input id="send_credentials_via_email" type="checkbox" name="send_credentials_via_email" value="sending"';if (isset($_POST['send_credentials_via_email'])) echo 'checked';echo'/>
						<span class="wppb-description-delimiter">'; _e(' Send these credentials via email.', 'profilebuilder');echo'</span></label>
						</p><!-- .send-confirmation-email -->';
				?>
					
				<p class="form-submit">
					<?php //echo $referer; ?>
					<input name="adduser" type="submit" id="addusersub" class="submit button" value="<?php if ( current_user_can( 'create_users' ) ) _e('Add User', 'profilebuilder'); else _e('Register', 'profilebuilder'); ?>" />
					<input name="action" type="hidden" id="action" value="adduser" />
				</p><!-- .form-submit -->
				<?php wp_nonce_field('verify_true_registration','register_nonce_field'); ?>
			</form><!-- #adduser -->

			<?php endif; ?>

		<?php endif; ?>
		
		<?php /* use this action hook to add extra content after the register form. */ ?>
		<?php do_action( 'wppb_after_register_fields' ); ?> 
	
	</div>
<?php
	$output = ob_get_contents();
    ob_end_clean();
		
	$output = apply_filters ('wppb_register', $output);
	
    return $output;
}
?>