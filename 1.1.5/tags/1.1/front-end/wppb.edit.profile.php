<?php
/*
	wp_update_user only attempts to clear and reset cookies if it's updating the password.
	The php function setcookie(), used in both the cookie-clearing and cookie-resetting functions,
	adds to the page headers and therefore must be called within the first php tag on the page, and 
	before the WordPress get_header() function. Since wp_update_user needs this, it must be at the 
	beginning of the page as well.
*/
$changesSaved = 'no';
$changesSavedNoMatchingPass = 'no';
$changesSavedNoPass = 'no';

function wppb_save_the_password(){
	global $changesSaved;
	global $changesSavedNoMatchingPass;
	global $changesSavedNoPass;
	
	/* Load registration file. */
	require_once(ABSPATH . WPINC . '/registration.php');
	/* Get user info. */
	global $current_user;	
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' && wp_verify_nonce($_POST['edit_nonce_field'],'verify_edit_user') ) { 
		/* Update user password. */
		if ( !empty($_POST['pass1'] ) && !empty( $_POST['pass2'] ) ) {
			if ( $_POST['pass1'] == $_POST['pass2'] )
			{
				wp_update_user( array( 'ID' => $current_user->id, 'user_pass' => esc_attr( $_POST['pass1'] ) ) );
				$changesSaved = 'yes';
			} else {
				$changesSavedNoMatchingPass = 'yes'; 
			}
		}elseif (( empty($_POST['pass1'] ) && !empty( $_POST['pass2'] )) || ( !empty($_POST['pass1'] ) && empty( $_POST['pass2'] )) ) {
			$changesSavedNoPass = 'yes';
		}	
	}
}
add_action('init', 'wppb_save_the_password');
		
function wppb_front_end_profile_info() {
	global $changesSaved;
	global $changesSavedNoMatchingPass;
	global $changesSavedNoPass;
	
	ob_start();
	get_currentuserinfo();
	$wppb_defaultOptions = get_option('wppb_default_settings');
	$changesSavedNoEmail = 'no';
	$changesSavedNoEmailExist = 'no';
	$previousError = 'no';
	$pictureUpload = 'no';
	$avatarUpload = 'yes';
	$uploadName = array();
	$uploadSize = array();
	
	
	/* Load registration file. */
		require_once(ABSPATH . WPINC . '/registration.php');
	/* Get user info. */
		global $current_user;
		
	/* delete the attachment if set */
	if (isset($_GET['userID']) && isset($_GET['field'])){
		update_user_meta( $_GET['userID'], $_GET['field'], '');
	}
	/* delete the avatar */
	if (isset($_GET['userID']) && isset($_GET['fieldOriginal']) && isset($_GET['fieldResized'])){
		update_user_meta( $_GET['userID'], $_GET['fieldOriginal'], '');
		update_user_meta( $_GET['userID'], $_GET['fieldResized'], '');
	}
		
	/* If profile was saved, update profile. */
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' && wp_verify_nonce($_POST['edit_nonce_field'],'verify_edit_user') ) { 
	
		if (email_exists( $_POST['email'] ) !=  FALSE)
			$thisEmail = email_exists( $_POST['email'] );
		else $thisEmail = $current_user->id;
		
		if ( !empty( $_POST['email'] ) &&  is_email( $_POST['email'] )){                  				// if the user entered a valid email address
			if (($thisEmail ==  $current_user->id)){            										// if the entered email address is not already registered to some other user
				wp_update_user( array( 'ID' => $current_user->id, 'user_email' => esc_attr( $_POST['email'] )));	
				$changesSaved = 'yes';
			}else{
				$changesSavedNoEmailExist = 'yes';
			}
		}else{
			$changesSavedNoEmail = 'yes';
		}
		

		/* Update user information. */
		if ($wppb_defaultOptions['firstname'] == 'show'){
			wp_update_user( array( 'ID' => $current_user->id, 'first_name' => esc_attr( $_POST['first_name'] )));
			$changesSaved = 'yes';
		}	
		if ($wppb_defaultOptions['lastname'] == 'show'){
			wp_update_user( array( 'ID' => $current_user->id, 'last_name' => esc_attr( $_POST['last_name'] )));
			$changesSaved = 'yes';
		}
			
		if ($wppb_defaultOptions['nickname'] == 'show'){
			wp_update_user( array( 'ID' => $current_user->id, 'nickname' => esc_attr( $_POST['nickname'] )));
			$changesSaved = 'yes';
		}
			
		if ($wppb_defaultOptions['dispname'] == 'show'){
			wp_update_user( array( 'ID' => $current_user->id, 'display_name' => esc_attr( $_POST['display_name'] )));
			$changesSaved = 'yes';
		}
			
		if ($wppb_defaultOptions['website'] == 'show'){
			$wppbPos = strpos($_POST['website'], 'http://');
			if($wppbPos !== FALSE){
				wp_update_user( array( 'ID' => $current_user->id, 'user_url' => esc_attr( $_POST['website'] )));
				$changesSaved = 'yes';
			}else{
				wp_update_user( array( 'ID' => $current_user->id, 'user_url' => 'http://'.esc_attr( $_POST['website'] )));
				$changesSaved = 'yes';
			}
		}
		
		if ($wppb_defaultOptions['aim'] == 'show'){	
			update_user_meta( $current_user->id, 'aim', esc_attr( $_POST['aim'] ) );
			$changesSaved = 'yes';
		}
			
		if ($wppb_defaultOptions['yahoo'] == 'show'){	
			update_user_meta( $current_user->id, 'yim', esc_attr( $_POST['yim'] ) );
			$changesSaved = 'yes';
		}
			
		if ($wppb_defaultOptions['jabber'] == 'show'){	
			update_user_meta( $current_user->id, 'jabber', esc_attr( $_POST['jabber'] ) );
			$changesSaved = 'yes';
		}
			
		if ($wppb_defaultOptions['bio'] == 'show'){	
			update_user_meta( $current_user->id, 'description', esc_attr( $_POST['description'] ) );
			$changesSaved = 'yes';
		}
	
		/* update the extra profile information */
			$wppb_premium = wppb_plugin_dir . '/premium/functions/';
			if (file_exists ( $wppb_premium.'extra.fields.php' )){
				$wppbFetchArray = get_option('wppb_custom_fields');
				foreach ( $wppbFetchArray as $key => $value){
					switch ($value['item_type']) {
						case "input":{
							update_user_meta( $current_user->id, 'custom_field_'.$value['id'], esc_attr( $_POST[$value['item_id'].$value['id']] ) );
							break;
						}
						case "checkbox":{
							$checkboxOption = '';
							$checkboxValue = explode(',', $value['item_options']);
							foreach($checkboxValue as $thisValue){
								if (isset($_POST[$thisValue.$value['id']]))
									$checkboxOption = $checkboxOption.$_POST[$thisValue.$value['id']].',';
							}
							update_user_meta( $current_user->id, 'custom_field_'.$value['id'], esc_attr( $checkboxOption ) );
							break;
						}
						case "radio":{
							update_user_meta( $current_user->id, 'custom_field_'.$value['id'], $_POST[$value['item_id'].$value['id']]  );
							break;
						}
						case "select":{
							update_user_meta( $current_user->id, 'custom_field_'.$value['id'], esc_attr( $_POST[$value['item_id'].$value['id']] ) );
							break;
						}
						case "textarea":{
							update_user_meta( $current_user->id, 'custom_field_'.$value['id'], esc_attr( $_POST[$value['item_id'].$value['id']] ) );
							break;
						}
						case "upload":{
								$uploadedfile = $value['item_type'].$value['id'];
								if (($_FILES[$uploadedfile]['size'] == 0) && (basename( $_FILES[$uploadedfile]['name']) != ''))
									array_push($uploadName, basename( $_FILES[$uploadedfile]['name']));
								$target_path = "wp-content/uploads/profile_builder/attachments/";

								$target_path = $target_path . 'userID_'.$current_user->id.'_attachment_'. basename( $_FILES[$uploadedfile]['name']); 	
								
								if (move_uploaded_file($_FILES[$uploadedfile]['tmp_name'], $target_path)){
									$upFile = get_bloginfo('home').'/'.$target_path;
									update_user_meta( $current_user->id, 'custom_field_'.$value['id'], $upFile);
									$pictureUpload = 'yes';
								}
							break;
						}
						case "avatar":{
								$avatarUpload = 'no';
								$uploadedfile = $value['item_type'].$value['id'];
								$target_path_original = "wp-content/uploads/profile_builder/avatars/";
								$fileName = $_FILES[$uploadedfile]['name'];

								$target_path = $target_path_original . 'userID_'.$current_user->id.'_originalAvatar_'. $fileName; 	
								$target_path_avatar = $target_path_original . 'userID_'.$current_user->id.'_resziedAvatarSize_'.$value['item_options'].'_orignalName_'.$fileName; 

								/* when trying to upload file, be sure it's one of the accepted image file-types */
								if (($_FILES[$uploadedfile]['type'] == 'image/jpeg') || ($_FILES[$uploadedfile]['type'] == 'image/jpg') || ($_FILES[$uploadedfile]['type'] == 'image/png') || ($_FILES[$uploadedfile]['type'] == 'image/bmp') || ($_FILES[$uploadedfile]['type'] == 'image/pjpeg') || ($_FILES[$uploadedfile]['type'] == 'image/x-png')){
									$avatarUpload = 'yes';
									$wp_filetype = wp_check_filetype(basename( $_FILES[$uploadedfile]['name']), null );
									$attachment = array(
										 'post_mime_type' => $wp_filetype['type'],
										 'post_title' => $fileName,
										 'post_content' => '',
										 'post_status' => 'inherit'
										);


									$attach_id = wp_insert_attachment( $attachment, $target_path);
									$attach_id_avatar = wp_insert_attachment( $attachment, $target_path_avatar);
							
									$upFile = image_downsize( $attach_id, 'thumbnail' );
									$upFile_avatar = image_downsize( $attach_id_avatar, 'thumbnail' );
									$upFile = $upFile[0];
									$upFile_avatar = $upFile_avatar[0];
									
									//if file upload succeded			
									if (move_uploaded_file($_FILES[$uploadedfile]['tmp_name'], $target_path)){
										// update the usermeta field with the original file url
										update_user_meta( $current_user->id, 'custom_field_'.$value['id'], $upFile);
										
										// also upload a resized image of it
										(move_uploaded_file($_FILES[$uploadedfile]['tmp_name'], $target_path_avatar));
										
										
										$extension = str_replace ( 'image/' , '' , $_FILES[$uploadedfile]['type']);
										if($extension=="jpg" || $extension=="jpeg" || $extension=="pjpeg" )
											$src = imagecreatefromjpeg($upFile);
										elseif($extension=="png" || $extension=="x-png")
											$src = imagecreatefrompng($upFile);
										else 
											$src = imagecreatefromgif($upFile);
										//echo $scr;
										
										list($width,$height)=getimagesize($upFile);

										$newwidth=$value['item_options']; //160;
										$newheight=$value['item_options']; //($height/$width)*$newwidth;
										$tmp=imagecreatetruecolor($newwidth,$newheight);
							
										imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
										$filename = $target_path_avatar;
										
										imagejpeg($tmp,$filename,100);
										
										imagedestroy($src);
										imagedestroy($tmp);
										
										// update/add a new usermeta field containing the url to the resized image
										update_user_meta( $current_user->id, 'custom_field_resized'.$value['id'], $upFile_avatar);
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
		
	}
	
?>
	<div class="wppb_holder" id="wppb_modify">
	<?php if ( !is_user_logged_in() ) : ?>
 
				<p class="warning">
					<?php _e('You must be logged in to edit your profile.', 'profilebuilder'); ?>
				</p><!-- .warning -->
 
			<?php else : 
					{
					/* messages for the the delete avatar/attachment */
					if (isset($_GET['fileType'])){
						if ($_GET['fileType'] == 'avatar'){
							echo '<p class="changes-saved">';
							_e('The avatar was successfully deleted.', 'profilebuilder'); 
							echo'</p><!-- .changes-saved -->';
							unset($_GET['fileType']);
						}elseif ($_GET['fileType'] == 'attachment'){
							echo '<p class="changes-saved">';
							_e('The attachment "', 'profilebuilder');
							echo $_GET['fileName'];
							_e('" was successfully deleted.', 'profilebuilder'); 
							echo'</p><!-- .changes-saved -->';
							unset($_GET['fileType']);
							unset($_GET['fileName']);
						}
					}
					
					/* all the other messages/errors */
						$nrOfBadUploads = 0;
						$nrOfBadUploads = count($uploadName);
					
					if (($changesSaved == 'yes') && ($changesSavedNoMatchingPass == 'no')  && ($changesSavedNoPass == 'no') && ($changesSavedNoEmail == 'no') && ($changesSavedNoEmailExist == 'no') && ($avatarUpload == 'yes') && ($nrOfBadUploads == 0)){
						echo '<p class="changes-saved">';
						_e('The changes have been successfully saved.', 'profilebuilder'); 
						echo'</p><!-- .changes-saved -->';
					}
					elseif (($changesSaved == 'yes') && ($changesSavedNoEmailExist == 'yes') && ($previousError == 'no')){
						echo '<p class="semi-saved">';
						 _e('The email address you entered is already registered to a different user.<br/>The email address was ', 'profilebuilder');
						echo'<span class="error">'; _e('NOT', 'profilebuilder');echo'</span>';
						 _e(' updated along with the rest of the information.', 'profilebuilder');
						echo '</p>';
						$previousError = 'yes';
					}
					
					if (($changesSaved == 'yes') && ($changesSavedNoEmail == 'yes') && ($previousError == 'no')){
						echo '<p class="error">';
						echo '<p class="semi-saved">';
						 _e('The email address you entered is invalid. <br/> The email address was ', 'profilebuilder');
						echo'<span class="error">'; _e('NOT', 'profilebuilder');echo'</span>';
						 _e(' updated along with the rest of the information.', 'profilebuilder');
						echo '</p>';
						$previousError = 'yes';
					}
					
					if (($changesSaved == 'yes') && ($changesSavedNoMatchingPass == 'yes') && ($previousError == 'no')){
						echo '<p class="error">';
						echo '<p class="semi-saved">';
						 _e('The passwords you entered do not match. <br/> The password was ', 'profilebuilder');
						echo'<span class="error">'; _e('NOT', 'profilebuilder');echo'</span>';
						 _e(' updated along with the rest of the information.', 'profilebuilder');
						echo '</p>';
						$previousError = 'yes';
					}
					if (($changesSaved == 'yes') && ($changesSavedNoPass == 'yes') && ($previousError == 'no')){
						echo '<p class="error">';
						echo '<p class="semi-saved">';
						 _e('You didn\'t complete both password fields. <br/> The password was ', 'profilebuilder');
						echo'<span class="error">'; _e('NOT', 'profilebuilder');echo'</span>';
						 _e(' updated along with the rest of the information.', 'profilebuilder');
						echo '</p>';
						$previousError = 'yes';
					}
						$wppb_premium = wppb_plugin_dir . '/premium/functions/';
						if (file_exists ( $wppb_premium.'extra.fields.php' )){
							if (($changesSaved == 'yes') && ($nrOfBadUploads > 0) && ($previousError == 'no')){
								$lastOne = 0;
								echo '<p class="error">';
								echo '<p class="semi-saved">';
								 _e('There was an error while trying to upload the following attachments:<br/>', 'profilebuilder');
								echo '<span class="error">';
								 foreach ($uploadName as $key => $name){
									$lastOne++;
									echo $name;
									if ($nrOfBadUploads-$lastOne > 0) echo ';<span style="padding-left:10px"></span>';
								}
								echo '</span>';
								_e('<br/>Possible cause: the size was bigger than ', 'profilebuilder');
								echo ServerMaxUploadSizeMega;
								_e('b.<br/>The listed attachements were ', 'profilebuilder');
								echo'<span class="error">'; _e('NOT', 'profilebuilder');echo'</span>';
								 _e(' updated along with the rest of the information.', 'profilebuilder');
								echo '</p>';
								$previousError = 'yes';
							}		if (($changesSaved == 'yes') && ($avatarUpload == 'no') && ($previousError == 'no')){
								echo '<p class="error">';
								echo '<p class="semi-saved">';
								 _e('There was an error while trying to upload your avatar picture.<br/>Possible cause: size/incorrect file-type.<br/>The avatar was ', 'profilebuilder');
								echo'<span class="error">'; _e('NOT', 'profilebuilder');echo'</span>';
								 _e(' updated along with the rest of the information.', 'profilebuilder');
								echo '</p>';
								$previousError = 'yes';
							}
						}
				}
					
				?>
 
				<?php /* use this action hook to add extra content before the edit profile form. */ ?>
				<?php do_action( 'wppb_before_edit_profile_fields' ); ?> 
	
				<form enctype="multipart/form-data" method="post" id="edituser" class="user-forms" action="<?php the_permalink(); ?>">
				<?php echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.ServerMaxUploadSizeByte.'" />'; ?> <!-- set the MAX_FILE_SIZE to the server's current max upload size in bytes -->
				
				<p>
				<strong><?php _e('Name', 'profilebuilder');?></strong>
				</p>
				<?php
				if ($wppb_defaultOptions['username'] == 'show'){ echo'
				<p class="username">
					<label for="user_login">'; _e('Username', 'profilebuilder'); echo'</label>
					<input class="text-input" name="user_login" type="text" id="user_login" value="'; the_author_meta( 'user_login', $current_user->id ); echo'" disabled="disabled"/> <span class="wppb-description-delimiter">'; _e(' Usernames cannot be changed.'); echo'</span>
				</p><!-- .first_name -->';
				}
				?>
				
				<?php 
				if ($wppb_defaultOptions['firstname'] == 'show'){ echo'
				<p class="first_name">
					<label for="first_name">'; _e('First Name', 'profilebuilder'); echo'</label>
					<input class="text-input" name="first_name" type="text" id="first_name" value="'; the_author_meta( 'first_name', $current_user->id ); echo '" />
				</p><!-- .first_name -->';
				}
				?>
				
				<?php 
				if ($wppb_defaultOptions['lastname'] == 'show'){ echo'
				<p class="last_name">
					<label for="last_name">'; _e('Last Name', 'profilebuilder'); echo'</label>
					<input class="text-input" name="last_name" type="text" id="last_name" value="'; the_author_meta( 'last_name', $current_user->id ); echo '" />
				</p><!-- .last_name -->';
				}
				?>
				
				<?php 
				if ($wppb_defaultOptions['nickname'] == 'show'){ echo'
				<p class="nickname">
					<label for="nickname">'; _e('Nickname', 'profilebuilder'); echo '</label>
					<input class="text-input" name="nickname" type="text" id="nickname" value="'; the_author_meta( 'nickname', $current_user->id ); echo'" />
					<span class="wppb-description-delimiter">'; _e('(required)', 'profilebuilder');echo'</span>
				</p><!-- .nickname -->';
				}
				?>
				
				<?php 
				if ($wppb_defaultOptions['dispname'] == 'show'){ echo'
				<p class="display_name">
					<label for="display_name">'; _e('Display name publicly as', 'profilebuilder'); echo'</label>
					<select name="display_name" id="display_name">';
						$public_display = array();
						$public_display['display_username']  = get_the_author_meta('user_login', $current_user->id);
						$thisFirstName = get_the_author_meta('first_name', $current_user->id);
						if ( !empty($thisFirstName))
							$public_display['display_firstname'] = get_the_author_meta('first_name', $current_user->id);
						$thisLastName = get_the_author_meta('last_name', $current_user->id);
						if ( !empty($thisLastName))
							$public_display['display_lastname'] = get_the_author_meta('last_name', $current_user->id);
						$public_display['display_nickname']  = get_the_author_meta('nickname', $current_user->id);
						if ( !empty($thisFirstName) && !empty($thisLastName) ) {
							$public_display['display_firstlast'] = $thisFirstName . ' ' . $thisLastName;
							$public_display['display_lastfirst'] = $thisLastName . ' ' . $thisFirstName;
						}
						$thisDisplayName = get_the_author_meta('display_name', $current_user->id);
						if ( !in_array( $thisDisplayName, $public_display ) )               // Only add this if it isn't duplicated elsewhere
							$public_display = array( 'display_displayname' => $thisDisplayName ) + $public_display;
						$public_display = array_map( 'trim', $public_display );
						foreach ( $public_display as $id => $item ) {
							echo '<option id="'.$id.'" value="'.$item.'"'; selected( $thisDisplayName, $item ); echo'>'.$item.'</option>';
						}
					echo'</select>
				</p><!-- .display_name -->';
				}
				?>

				
				<p>
				<strong><?php _e('Contact Info', 'profilebuilder');?></strong>
				</p>
				
				<?php 
				if ($wppb_defaultOptions['email'] == 'show'){ echo'
				<p class="form-email">
					<label for="email">'; _e('E-mail', 'profilebuilder');echo '</label>
					<input class="text-input" name="email" type="text" id="email" value="'; the_author_meta( 'user_email', $current_user->id ); echo'" />
					<span class="wppb-description-delimiter">'; _e('(required)', 'profilebuilder');echo'</span>
				</p><!-- .form-email -->';
				}
				?>
				
				<?php 
				if ($wppb_defaultOptions['website'] == 'show'){ echo'
				<p class="form-website">
					<label for="website">'; _e('Website', 'profilebuilder'); echo'</label>
					<input class="text-input" name="website" type="text" id="website" value="'; the_author_meta( 'user_url', $current_user->id ); echo'" />
				</p><!-- .form-website -->';
				}
				?>
				
				<?php 
				if ($wppb_defaultOptions['aim'] == 'show'){ echo'
				<p class="form-aim">
					<label for="aim">'; _e('AIM', 'profilebuilder'); echo'</label>
					<input class="text-input" name="aim" type="text" id="aim" value="'; the_author_meta( 'aim', $current_user->id ); echo'" />
				</p><!-- .form-aim -->';
				}
				?>
				
				<?php 
				if ($wppb_defaultOptions['yahoo'] == 'show'){ echo'
				<p class="form-yim">
					<label for="yim">'; _e('Yahoo IM', 'profilebuilder'); echo'</label>
					<input class="text-input" name="yim" type="text" id="yim" value="'; the_author_meta( 'yim', $current_user->id ); echo'" />
				</p><!-- .form-yim -->';
				}
				?>
 
				<?php 
				if ($wppb_defaultOptions['jabber'] == 'show'){ echo'
				<p class="form-jabber">
					<label for="jabber">'; _e('Jabber / Google Talk', 'profilebuilder'); echo'</label>
					<input class="text-input" name="jabber" type="text" id="jabber" value="'; the_author_meta( 'jabber', $current_user->id ); echo'" />
				</p><!-- .form-jabber -->';
				}
				?>
				
				<p>
				<strong><?php _e('About Yourself', 'profilebuilder');?></strong>
				</p>
				
				<?php 
				if ($wppb_defaultOptions['bio'] == 'show'){ echo'
				<p class="form-description">
					<label for="description">'; _e('Biographical Info', 'profilebuilder'); echo'</label>
					<textarea class="text-input" name="description" id="description" rows="5" cols="30">'; the_author_meta( 'description', $current_user->id ); echo'</textarea>
				</p><!-- .form-description -->';
				}
				?>
				
				<?php 
				if ($wppb_defaultOptions['password'] == 'show'){ echo'
				<p class="form-password">
					<label for="pass1">'; _e('New Password', 'profilebuilder'); echo'</label>
					<input class="text-input" name="pass1" type="password" id="pass1" />
				</p><!-- .form-password -->
 
				<p class="form-password">
					<label for="pass2">'; _e('Repeat Password', 'profilebuilder'); echo'</label>
					<input class="text-input" name="pass2" type="password" id="pass2" />
				</p><!-- .form-password -->';
				}
				?>
				
				<?php
					$wppb_premium = wppb_plugin_dir . '/premium/functions/';
					if (file_exists ( $wppb_premium.'extra.fields.php' )){
						require_once($wppb_premium.'extra.fields.php');
						edit_profile_extra_fields($current_user->id);
					}
				?>

				
				<p class="form-submit">
					<input name="updateuser" type="submit" id="updateuser" class="submit button" value="<?php _e('Update', 'profilebuilder'); ?>" />
					<?php// wp_nonce_field( 'update-user' ) ?>
					<input name="action" type="hidden" id="action" value="update-user" />
				</p><!-- .form-submit -->
				<?php wp_nonce_field('verify_edit_user','edit_nonce_field'); ?>
				</form><!-- #edituser -->
				
			<?php endif; ?>
			
			<?php /* use this action hook to add extra content after the edit profile form. */ ?>
			<?php do_action( 'wppb_after_edit_profile_fields' ); ?>
			
	</div>	
	
<?php
	$output = ob_get_contents();
    ob_end_clean();
	
	$output = apply_filters ('wppb_edit_profile', $output);
	
    return $output;
}
?>