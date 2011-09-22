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
	$editProfileFilterArray = array();
	$extraFieldsErrorHolder = array();  //we will use this array to store the ID's of the extra-fields left uncompleted
	
	ob_start();
	get_currentuserinfo();
	$wppb_defaultOptions = get_option('wppb_default_settings');
	$changesSavedNoEmail = 'no';
	$changesSavedNoEmailExist = 'no';
	$previousError = 'no';
	$pictureUpload = 'no';
	$avatarUpload = 'yes';
	$allRequiredCompleted = 'yes';
	$uploadName = array();
	$uploadSize = array();
	$editFilterArray = array();
	
	
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
	
	//fallback if the file was largen then post_max_size, case in which no errors can be saved in $_FILES[fileName]['error']	
	if (empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
		$editProfileFilterArray['noPost'] = '
		<p class="error">'.
			__('The information size you were trying to submit was larger than', 'profilebuilder') .' '. ServerMaxUploadSizeMega .'b!<br/>'.
			__('This is usually caused by a large file(s) trying to be uploaded.', 'profilebuilder') .'<br/>'.
			__('Since it was also larger than', 'profilebuilder') .' '. ServerMaxPostSizeMega .'b '. __('no additional information is available.', 'profilebuilder') .'<br/>
		</p>';
		$editProfileFilterArray['noPost'] = apply_filters('wppb_edit_profile_no_post_error', $editProfileFilterArray['noPost']);
		echo $editProfileFilterArray['noPost'];
	}
	// a way to catch the user before updating his/her profile without completing a required field
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' && wp_verify_nonce($_POST['edit_nonce_field'],'verify_edit_user') ) {	
		//variable to control whether the user submitted data or not
		
		$allRequiredCompleted = apply_filters('wppb_edit_profile_all_required_completed', $allRequiredCompleted);
	}
		
	/* If profile was saved, update profile. */
	if ( ('POST' == $_SERVER['REQUEST_METHOD']) && (!empty( $_POST['action'] )) && ($_POST['action'] == 'update-user') && (wp_verify_nonce($_POST['edit_nonce_field'],'verify_edit_user')) && ($allRequiredCompleted == 'yes') ) { 
		
		$_POST['email'] =  apply_filters('wppb_edit_profile_posted_email', $_POST['email']);
		if ($wppb_defaultOptions['emailRequired'] == 'yes'){
			if ((trim($_POST['email']) != '') && isset($_POST['email'])){
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
			}
		}else{	
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
		}
		

		/* Update user information. */
		if ($wppb_defaultOptions['firstname'] == 'show'){
			$_POST['first_name'] =  apply_filters('wppb_edit_profile_posted_first_name', $_POST['first_name']);
			if ($wppb_defaultOptions['firstnameRequired'] == 'yes'){
				if ((trim($_POST['first_name']) != '') && isset($_POST['first_name'])){
					wp_update_user( array( 'ID' => $current_user->id, 'first_name' => esc_attr( $_POST['first_name'] )));
					$changesSaved = 'yes';
				}
			}else{
				wp_update_user( array( 'ID' => $current_user->id, 'first_name' => esc_attr( $_POST['first_name'] )));
				$changesSaved = 'yes';
			}
		}	
		
		if ($wppb_defaultOptions['lastname'] == 'show'){
			$_POST['last_name'] =  apply_filters('wppb_edit_profile_posted_last_name', $_POST['last_name']);
			if ($wppb_defaultOptions['lastnameRequired'] == 'yes'){
				if ((trim($_POST['last_name']) != '') && isset($_POST['last_name'])){
					wp_update_user( array( 'ID' => $current_user->id, 'last_name' => esc_attr( $_POST['last_name'] )));
					$changesSaved = 'yes';
				}
			}else{
				wp_update_user( array( 'ID' => $current_user->id, 'last_name' => esc_attr( $_POST['last_name'] )));
				$changesSaved = 'yes';
			}
		}
		
		if ($wppb_defaultOptions['nickname'] == 'show'){
			$_POST['nickname'] =  apply_filters('wppb_edit_profile_posted_nickname', $_POST['nickname']);
			if ($wppb_defaultOptions['nicknameRequired'] == 'yes'){
				if ((trim($_POST['nickname']) != '') && isset($_POST['nickname'])){
					wp_update_user( array( 'ID' => $current_user->id, 'nickname' => esc_attr( $_POST['nickname'] )));
					$changesSaved = 'yes';
				}
			}else{
				wp_update_user( array( 'ID' => $current_user->id, 'nickname' => esc_attr( $_POST['nickname'] )));
				$changesSaved = 'yes';
			}

		}
			
		if ($wppb_defaultOptions['dispname'] == 'show'){
			$_POST['display_name'] =  apply_filters('wppb_edit_profile_posted_display_name', $_POST['display_name']);
			if ($wppb_defaultOptions['dispnameRequired'] == 'yes'){
				if ((trim($_POST['display_name']) != '') && isset($_POST['display_name'])){
					wp_update_user( array( 'ID' => $current_user->id, 'display_name' => esc_attr( $_POST['display_name'] )));
					$changesSaved = 'yes';
				}
			}else{
				wp_update_user( array( 'ID' => $current_user->id, 'display_name' => esc_attr( $_POST['display_name'] )));
				$changesSaved = 'yes';
			}
		}
			
		if ($wppb_defaultOptions['website'] == 'show'){
			$_POST['website'] =  apply_filters('wppb_edit_profile_posted_website', $_POST['website']);
			if ($wppb_defaultOptions['websiteRequired'] == 'yes'){
				if ((trim($_POST['website']) != '') && isset($_POST['website'])){
					$wppbPos = strpos($_POST['website'], 'http://');
					if($wppbPos !== FALSE){
						wp_update_user( array( 'ID' => $current_user->id, 'user_url' => esc_attr( $_POST['website'] )));
						$changesSaved = 'yes';
					}else{
						wp_update_user( array( 'ID' => $current_user->id, 'user_url' => 'http://'.esc_attr( $_POST['website'] )));
						$changesSaved = 'yes';
					}
				}
			}else{
				$wppbPos = strpos($_POST['website'], 'http://');
				$website = esc_attr( $_POST['website'] );
				if($wppbPos !== FALSE){
					if ($website == 'http://')
						$website = '';
					wp_update_user( array( 'ID' => $current_user->id, 'user_url' => $website));
					$changesSaved = 'yes';
				}else{
					if ($website != '')
						$website = 'http://'.$website;
					wp_update_user( array( 'ID' => $current_user->id, 'user_url' => $website));
					$changesSaved = 'yes';
				}
			}
		}
		
		if ($wppb_defaultOptions['aim'] == 'show'){
			$_POST['aim'] =  apply_filters('wppb_edit_profile_posted_aim', $_POST['aim']);
			if ($wppb_defaultOptions['aimRequired'] == 'yes'){
				if ((trim($_POST['aim']) != '') && isset($_POST['aim'])){
					update_user_meta( $current_user->id, 'aim', esc_attr( $_POST['aim'] ) );
					$changesSaved = 'yes';
				}
			}else{
				update_user_meta( $current_user->id, 'aim', esc_attr( $_POST['aim'] ) );
				$changesSaved = 'yes';
			}
		}
			
		if ($wppb_defaultOptions['yahoo'] == 'show'){
			$_POST['yim'] =  apply_filters('wppb_edit_profile_posted_yahoo', $_POST['yim']);
			if ($wppb_defaultOptions['yahooRequired'] == 'yes'){
				if ((trim($_POST['yim']) != '') && isset($_POST['yim'])){
					update_user_meta( $current_user->id, 'yim', esc_attr( $_POST['yim'] ) );
					$changesSaved = 'yes';
				}
			}else{
				update_user_meta( $current_user->id, 'yim', esc_attr( $_POST['yim'] ) );
				$changesSaved = 'yes';
			}
		}
			
		if ($wppb_defaultOptions['jabber'] == 'show'){
			$_POST['jabber'] =  apply_filters('wppb_edit_profile_posted_jabber', $_POST['jabber']);
			if ($wppb_defaultOptions['jabberRequired'] == 'yes'){
				if ((trim($_POST['jabber']) != '') && isset($_POST['jabber'])){
					update_user_meta( $current_user->id, 'jabber', esc_attr( $_POST['jabber'] ) );
					$changesSaved = 'yes';
				}
			}else{
				update_user_meta( $current_user->id, 'jabber', esc_attr( $_POST['jabber'] ) );
				$changesSaved = 'yes';
			}
		}
			
		if ($wppb_defaultOptions['bio'] == 'show'){
			$_POST['description'] =  apply_filters('wppb_edit_profile_posted_bio', $_POST['description']);
			if ($wppb_defaultOptions['bioRequired'] == 'yes'){
				if ((trim($_POST['description']) != '') && isset($_POST['description'])){
					update_user_meta( $current_user->id, 'description', esc_attr( $_POST['description'] ) );
					$changesSaved = 'yes';
				}
			}else{
				update_user_meta( $current_user->id, 'description', esc_attr( $_POST['description'] ) );
				$changesSaved = 'yes';
			}
		}
	
		/* update the extra profile information */
			$wppb_premium = wppb_plugin_dir . '/premium/functions/';
			if (file_exists ( $wppb_premium.'extra.fields.php' )){
				$wppbFetchArray = get_option('wppb_custom_fields');
				foreach ( $wppbFetchArray as $key => $value){
					switch ($value['item_type']) {
						case "input":{
							$_POST[$value['item_id'].$value['id']] = apply_filters('wppb_edit_profile_input_custom_field_'.$value['id'], $_POST[$value['item_id'].$value['id']]);
							if ($value['item_required'] != null){
								if ($value['item_required'] == 'yes'){
									if (trim($_POST[$value['item_id'].$value['id']]) != '')
										update_user_meta( $current_user->id, 'custom_field_'.$value['id'], esc_attr( $_POST[$value['item_id'].$value['id']] ) );
									else 
										array_push($extraFieldsErrorHolder, $value['id']);
								}
							}else
								update_user_meta( $current_user->id, 'custom_field_'.$value['id'], esc_attr( $_POST[$value['item_id'].$value['id']] ) );
								
							break;
						}						
						case "hiddenInput":{
							update_user_meta( $current_user->id, 'custom_field_'.$value['id'], esc_attr( $_POST[$value['item_id'].$value['id']] ) );
							
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
							
							if ($value['item_required'] != null){
								if ($value['item_required'] == 'yes'){
									if (trim($checkboxOption) != '')
										update_user_meta( $current_user->id, 'custom_field_'.$value['id'], esc_attr( $checkboxOption ) );
									else 
										array_push($extraFieldsErrorHolder, $value['id']);
								}
							}else
								update_user_meta( $current_user->id, 'custom_field_'.$value['id'], esc_attr( $checkboxOption ) );
								
							break;
						}
						case "radio":{
							if ($value['item_required'] != null){
								if ($value['item_required'] == 'yes'){
									if (trim($_POST[$value['item_id'].$value['id']]) != '')
										update_user_meta( $current_user->id, 'custom_field_'.$value['id'], esc_attr( $_POST[$value['item_id'].$value['id']] ) );
									else 
										array_push($extraFieldsErrorHolder, $value['id']);
								}
							}
							break;
						}
						case "select":{
							if ($value['item_required'] != null){
								if ($value['item_required'] == 'yes'){
									if (trim($_POST[$value['item_id'].$value['id']]) != '')
										update_user_meta( $current_user->id, 'custom_field_'.$value['id'], esc_attr( $_POST[$value['item_id'].$value['id']] ) );
									else 
										array_push($extraFieldsErrorHolder, $value['id']);
								}
							}
							break;
						}
						case "countrySelect":{
							if ($value['item_required'] != null){
								if ($value['item_required'] == 'yes'){
									if (trim($_POST[$value['item_id'].$value['id']]) != '')
										update_user_meta( $current_user->id, 'custom_field_'.$value['id'], esc_attr( $_POST[$value['item_id'].$value['id']] ) );
									else 
										array_push($extraFieldsErrorHolder, $value['id']);
								}
							}else
								update_user_meta( $current_user->id, 'custom_field_'.$value['id'], esc_attr( $_POST[$value['item_id'].$value['id']] ) );
							
							break;
						}
						case "timeZone":{
							if ($value['item_required'] != null){
								if ($value['item_required'] == 'yes'){
									if (trim($_POST[$value['item_id'].$value['id']]) != '')
										update_user_meta( $current_user->id, 'custom_field_'.$value['id'], esc_attr( $_POST[$value['item_id'].$value['id']] ) );
									else 
										array_push($extraFieldsErrorHolder, $value['id']);
								}
							}else
								update_user_meta( $current_user->id, 'custom_field_'.$value['id'], esc_attr( $_POST[$value['item_id'].$value['id']] ) );
							
							break;
						}
						case "datepicker":{
							if ($value['item_required'] != null){
								if ($value['item_required'] == 'yes'){
									if (trim($_POST[$value['item_id'].$value['id']]) != '')
										update_user_meta( $current_user->id, 'custom_field_'.$value['id'], esc_attr( $_POST[$value['item_id'].$value['id']] ) );
									else 
										array_push($extraFieldsErrorHolder, $value['id']);
								}
							}else
								update_user_meta( $current_user->id, 'custom_field_'.$value['id'], esc_attr( $_POST[$value['item_id'].$value['id']] ) );
							
							break;
						}
						case "textarea":{
							if ($value['item_required'] != null){
								if ($value['item_required'] == 'yes'){
									if (trim($_POST[$value['item_id'].$value['id']]) != '')
										update_user_meta( $current_user->id, 'custom_field_'.$value['id'], esc_attr( $_POST[$value['item_id'].$value['id']] ) );
									else 
										array_push($extraFieldsErrorHolder, $value['id']);
								}
							}else
								update_user_meta( $current_user->id, 'custom_field_'.$value['id'], esc_attr( $_POST[$value['item_id'].$value['id']] ) );
							
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
										$target_path = $target_path . 'userID_'.$current_user->id.'_attachment_'. $finalFileName; 

										if (move_uploaded_file($_FILES[$uploadedfile]['tmp_name'], $target_path)){
											$upFile = get_bloginfo('home').'/'.$target_path;
											update_user_meta( $current_user->id, 'custom_field_'.$value['id'], $upFile);
											$pictureUpload = 'yes';
										}else{
											//insert the name of the file in an array so that in case an error comes up, we know which files we just uploaded
											array_push($uploadName, basename( $_FILES[$uploadedfile]['name']));
										}
									}else{
										//insert the name of the file in an array so that in case an error comes up, we know which files we just uploaded
										array_push($uploadName, basename( $_FILES[$uploadedfile]['name']));
									}
								}
							break;
						}
						case "avatar":{
								$avatarUpload = 'no';
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

								$target_path = $target_path_original . 'userID_'.$current_user->id.'_originalAvatar_'. $fileName; 	
								$target_path_avatar = $target_path_original . 'userID_'.$current_user->id.'_resziedAvatarSize_'.$value['item_options'].'_orignalName_'.$fileName; 

								/* when trying to upload file, be sure it's one of the accepted image file-types */
								if ( (($_FILES[$uploadedfile]['type'] == 'image/jpeg') || ($_FILES[$uploadedfile]['type'] == 'image/jpg') || ($_FILES[$uploadedfile]['type'] == 'image/png') || ($_FILES[$uploadedfile]['type'] == 'image/bmp') || ($_FILES[$uploadedfile]['type'] == 'image/pjpeg') || ($_FILES[$uploadedfile]['type'] == 'image/x-png')) && (($_FILES[$uploadedfile]['size'] < ServerMaxUploadSizeByte) && ($_FILES[$uploadedfile]['size'] !=0)) ){
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
									
									//calculate memory needed for file creation and allocate dynamically, in case of large files
									$memoryNeeded = round(($upFile[0] * $upFile[1] * $upFile['bits'] * $upFile['channels'] / 8 + Pow(2, 16)) * 1.8);
									$memoryHave = memory_get_usage();
									$memoryHave = memoryHave * Pow(1024,2);
									$memoryLimitMB = ini_get('memory_limit');
									$memoryLimit = substr ( $memoryLimitMB, 0, strlen($memoryLimitMB)-1 );
									$memoryLimit = $memoryLimit * Pow(1024,2);
									
									$newLimit = ceil( (($memoryHave + $memoryNeeded + $memoryLimit) / Pow(1024,2)) * 1.8 );
									ini_set( 'memory_limit', $newLimit . 'M' );
															
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
										
										list($width,$height)=getimagesize($upFile);

										$newwidth=$value['item_options'];
										$newheight=$value['item_options'];
										$tmp=imagecreatetruecolor($newwidth,$newheight);
							
										imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
										$filename = $target_path_avatar;
										
										imagejpeg($tmp,$filename,100);
										
										imagedestroy($src);
										imagedestroy($tmp);
										
										//restore the old value of the memory after imageprocessing is done
										ini_restore ( 'memory_limit' );
										
										// update/add a new usermeta field containing the url to the resized image
										update_user_meta( $current_user->id, 'custom_field_resized'.$value['id'], $upFile_avatar);
									}else $avatarUpload = 'no'; 
								}elseif ( (($_FILES[$uploadedfile]['size'] > ServerMaxUploadSizeByte) || ($_FILES[$uploadedfile]['size'] == 0)) && ($fileName != '') )
									$avatarUpload = 'no';
								elseif ($fileName == '')
									$avatarUpload = 'yes';
							break;
						}
					}
				}
			}
		
	}
	
?>
	<div class="wppb_holder" id="wppb_modify">
<?php 
		if ( !is_user_logged_in() ) :
			$editProfileFilterArray['notLoggedIn'] = '
				<p class="warning">'. __('You must be logged in to edit your profile.', 'profilebuilder') .'</p><!-- .warning -->';
			$editProfileFilterArray['notLoggedIn'] = apply_filters('wppb_edit_profile_user_not_logged_in', $editProfileFilterArray['notLoggedIn']);
			echo $editProfileFilterArray['notLoggedIn'];
 
		else : 
			{
			/* messages for the the delete avatar/attachment */
			if (isset($_GET['fileType'])){
				if ($_GET['fileType'] == 'avatar'){
					$editProfileFilterArray['avatarChangesSaved'] = '
						<p class="changes-saved">'. __('The avatar was successfully deleted.', 'profilebuilder') .'</p><!-- .changes-saved -->';
					$editProfileFilterArray['avatarChangesSaved'] = apply_filters('wppb_edit_profile_avatar_changes_saved', $editProfileFilterArray['avatarChangesSaved']);
					echo $editProfileFilterArray['avatarChangesSaved'];
					unset($_GET['fileType']);
				}elseif ($_GET['fileType'] == 'attachment'){
					$editProfileFilterArray['attachmentChangesSaved'] = '
						<p class="changes-saved">'.
							__('The attachment', 'profilebuilder') .' "'. $_GET['fileName'] .'" '. __('was successfully deleted.', 'profilebuilder') .'
						</p><!-- .changes-saved -->';
					$editProfileFilterArray['attachmentChangesSaved'] = apply_filters('wppb_edit_profile_attachment_changes_saved', $editProfileFilterArray['attachmentChangesSaved']);
					echo $editProfileFilterArray['attachmentChangesSaved'];
					unset($_GET['fileType']);
					unset($_GET['fileName']);
				}
			}
			
			/* all the other messages/errors */
				$nrOfBadUploads = 0;
				$nrOfBadUploads = count($uploadName);
			
			if (($changesSaved == 'yes') && ($changesSavedNoMatchingPass == 'no')  && ($changesSavedNoPass == 'no') && ($changesSavedNoEmail == 'no') && ($changesSavedNoEmailExist == 'no') && ($avatarUpload == 'yes') && ($nrOfBadUploads == 0)){
				$editProfileFilterArray['allChangesSaved'] = '
					<p class="changes-saved">'. __('The changes have been successfully saved.', 'profilebuilder') .'</p><!-- .changes-saved -->';
				$editProfileFilterArray['allChangesSaved'] = apply_filters('wppb_edit_profile_all_changes_saved', $editProfileFilterArray['allChangesSaved']);
				echo $editProfileFilterArray['allChangesSaved'];
			}
			elseif (($changesSaved == 'yes') && ($changesSavedNoEmailExist == 'yes') && ($previousError == 'no')){
				$editProfileFilterArray['allChangesSavedExceptExistingEmail'] = '
					<p class="semi-saved"> '.
						__('The email address you entered is already registered to a different user.', 'profilebuilder') .'<br/>'. __('The email address was', 'profilebuilder') .' <span class="error">'. __('NOT', 'profilebuilder') .'</span> '. __('updated along with the rest of the information.', 'profilebuilder') .'
					</p>';
				$editProfileFilterArray['allChangesSavedExceptExistingEmail'] = apply_filters('wppb_edit_profile_all_changes_saved_except_existing_email', $editProfileFilterArray['allChangesSavedExceptExistingEmail']);
				echo $editProfileFilterArray['allChangesSavedExceptExistingEmail'];
				$previousError = 'yes';
			}
			
			if (($changesSaved == 'yes') && ($changesSavedNoEmail == 'yes') && ($previousError == 'no')){
				$editProfileFilterArray['allChangesSavedExceptInvalidEmail'] = '
					<p class="semi-saved"> '.
						__('The email address you entered is invalid.', 'profilebuilder') .'<br/>'. __('The email address was', 'profilebuilder') .' <span class="error">'. __('NOT', 'profilebuilder') .'</span> '. __('updated along with the rest of the information.', 'profilebuilder') .'
					</p>';
				$editProfileFilterArray['allChangesSavedExceptInvalidEmail'] = apply_filters('wppb_edit_profile_all_changes_saved_except_invalid_email', $editProfileFilterArray['allChangesSavedExceptInvalidEmail']);
				echo $editProfileFilterArray['allChangesSavedExceptInvalidEmail'];
				$previousError = 'yes';
			}
			
			if (($changesSaved == 'yes') && ($changesSavedNoMatchingPass == 'yes') && ($previousError == 'no')){
				$editProfileFilterArray['allChangesSavedMismatchedPass'] = '
					<p class="semi-saved">';
						__('The passwords you entered do not match.', 'profilebuilder') .'<br/>'. __('The password was', 'profilebuilder') .' <span class="error">'. __('NOT', 'profilebuilder') .'</span> '. __('updated along with the rest of the information.', 'profilebuilder') .'
					</p>';
				$editProfileFilterArray['allChangesSavedMismatchedPass'] = apply_filters('wppb_edit_profile_all_changes_saved_except_mismatch_password', $editProfileFilterArray['allChangesSavedMismatchedPass']);
				echo $editProfileFilterArray['allChangesSavedMismatchedPass'];
				$previousError = 'yes';
			}
			if (($changesSaved == 'yes') && ($changesSavedNoPass == 'yes') && ($previousError == 'no')){
				$editProfileFilterArray['allChangesSavedUncompletedPass'] = '
					<p class="semi-saved">'.
						__('You didn\'t complete both password fields.', 'profilebuilder') .'<br/>'. __('The password was', 'profilebuilder') .' <span class="error">'. __('NOT', 'profilebuilder') .'</span> '. __('updated along with the rest of the information.', 'profilebuilder') .'
					</p>';
				$editProfileFilterArray['allChangesSavedUncompletedPass'] = apply_filters('wppb_edit_profile_all_changes_saved_except_uncompleted_password', $editProfileFilterArray['allChangesSavedUncompletedPass']);
				echo $editProfileFilterArray['allChangesSavedUncompletedPass'];
				$previousError = 'yes';
			}
			if ($allRequiredCompleted == 'no'){
				$editProfileFilterArray['errorSavingChanges'] = '<p class="error">'.$errorMessage.'<br/>'. __('Your profile was NOT updated!', 'profilebuilder').'</p><!-- .error -->';
				$editProfileFilterArray['errorSavingChanges'] = apply_filters('wppb_edit_profile_error_saving_changes', $editProfileFilterArray['errorSavingChanges']);
				echo $editProfileFilterArray['errorSavingChanges'];
			}
				$wppb_premium = wppb_plugin_dir . '/premium/functions/';
				if (file_exists ( $wppb_premium.'extra.fields.php' )){
					if (($changesSaved == 'yes') && ($nrOfBadUploads > 0) && ($previousError == 'no')){
						$lastOne = 0;
						$editProfileFilterArray['errorUploadingAttachments'] = '
							<p class="semi-saved">'. __('There was an error while trying to upload the following attachments:', 'profilebuilder') .'<br/>
								<span class="error">';
									 foreach ($uploadName as $key => $name){
										$lastOne++;
										$editProfileFilterArray['errorUploadingAttachments'] .= $name;
										if ($nrOfBadUploads-$lastOne > 0) 
											$editProfileFilterArray['errorUploadingAttachments'] .= ';<span style="padding-left:10px"></span>';
									}
						$editProfileFilterArray['errorUploadingAttachments'] .= '
								</span><br/>'.
								__('Possible cause: the size was bigger than', 'profilebuilder') .' '.ServerMaxUploadSizeMega.'b.<br/>'. __('The listed attachements were', 'profilebuilder') .' <span class="error">'. __('NOT', 'profilebuilder') .'</span> '. __('updated along with the rest of the information.', 'profilebuilder') .'
							</p>';
						$editProfileFilterArray['errorUploadingAttachments'] = apply_filters('wppb_edit_profile_error_uploading_attachments', $editProfileFilterArray['errorUploadingAttachments']);
						echo $editProfileFilterArray['errorUploadingAttachments'];
						$previousError = 'yes';
					}if (($changesSaved == 'yes') && ($avatarUpload == 'no') && ($previousError == 'no')){
						$editProfileFilterArray['errorUploadingAvatar'] = '
							<p class="semi-saved">'.
								__('There was an error while trying to upload your avatar picture.', 'profilebuilder') .'<br/>'. __('Possible cause: size/incorrect file-type.', 'profilebuilder') .'<br/>'. __('The avatar was', 'profilebuilder') .' <span class="error">'. __('NOT', 'profilebuilder') .'</span> '. __('updated along with the rest of the information.', 'profilebuilder') .'
							</p>';
						$editProfileFilterArray['errorUploadingAvatar'] = apply_filters('wppb_edit_profile_error_uploading_avatar', $editProfileFilterArray['errorUploadingAvatar']);
						echo $editProfileFilterArray['errorUploadingAvatar'];
						$previousError = 'yes';
					}
				}
		}
 
		/* use this action hook to add extra content before the edit profile form. */
		do_action( 'wppb_before_edit_profile_fields' );
?>
	
		<form enctype="multipart/form-data" method="post" id="edituser" class="user-forms" action="<?php the_permalink(); ?>">
<?php 
			echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.ServerMaxUploadSizeByte.'" /><!-- set the MAX_FILE_SIZE to the server\'s current max upload size in bytes -->';			
			
			$editProfileFilterArray['contentName1'] = '<p class="nameHeader"><strong>'. __('Name', 'profilebuilder') .'</strong></p>';
			$editProfileFilterArray['contentName1'] = apply_filters('wppb_edit_profile_content_name1', $editProfileFilterArray['contentName1']);
			echo $editProfileFilterArray['contentName1'];		
		
			if ($wppb_defaultOptions['username'] == 'show'){
				$editProfileFilterArray['contentName2'] = '
					<p class="username">
						<label for="user_login">'. __('Username', 'profilebuilder') .'</label>
						<input class="text-input" name="user_login" type="text" id="user_login" value="'. get_the_author_meta( 'user_login', $current_user->id ) .'" disabled="disabled"/> <span class="wppb-description-delimiter"> '. __('Usernames cannot be changed.', 'profilebuilder') .'</span>
					</p><!-- .first_name -->';
				$editProfileFilterArray['contentName2'] = apply_filters('wppb_edit_profile_content_name2', $editProfileFilterArray['contentName2']);
				echo $editProfileFilterArray['contentName2'];	
			}

			if ($wppb_defaultOptions['firstname'] == 'show'){
				$errorVar = '';
				$errorMark = '';
				if ($wppb_defaultOptions['firstnameRequired'] == 'yes'){
					$errorMark = '<font color="red" title="This field is marked as required by the administrator.">*</font>';
					if ((trim($_POST['first_name']) == '') && isset($_POST['first_name'])){
						$errorMark = '<img src="'.wppb_plugin_url . '/assets/images/pencil_delete.png" title="This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator)."/>';
						$errorVar = ' errorHolder';
					}
				}
				$editProfileFilterArray['contentName3'] = '
					<p class="first_name'.$errorVar.'">
						<label for="first_name">'. __('First Name', 'profilebuilder') .$errorMark.'</label>
						<input class="text-input" name="first_name" type="text" id="first_name" value="'. get_the_author_meta( 'first_name', $current_user->id ) .'" />
					</p><!-- .first_name -->';
				$editProfileFilterArray['contentName3'] = apply_filters('wppb_edit_profile_content_name3', $editProfileFilterArray['contentName3']);
				echo $editProfileFilterArray['contentName3'];	
			}
				
			if ($wppb_defaultOptions['lastname'] == 'show'){
				$errorVar = '';
				$errorMark = '';
				if ($wppb_defaultOptions['lastnameRequired'] == 'yes'){
					$errorMark = '<font color="red" title="This field is marked as required by the administrator.">*</font>';
					if ((trim($_POST['last_name']) == '') && isset($_POST['last_name'])){
						$errorMark = '<img src="'.wppb_plugin_url . '/assets/images/pencil_delete.png" title="This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator)."/>';
						$errorVar = ' errorHolder';
					}
				}					
				$editProfileFilterArray['contentName4'] = '
					<p class="last_name'.$errorVar.'">
						<label for="last_name">'. __('Last Name', 'profilebuilder') .$errorMark.'</label>
						<input class="text-input" name="last_name" type="text" id="last_name" value="'. get_the_author_meta( 'last_name', $current_user->id ) .'" />
					</p><!-- .last_name -->';
				$editProfileFilterArray['contentName4'] = apply_filters('wppb_edit_profile_content_name4', $editProfileFilterArray['contentName4']);
				echo $editProfileFilterArray['contentName4'];	
			}
				
			if ($wppb_defaultOptions['nickname'] == 'show'){
				$errorVar = '';
				$errorMark = '';
				if ($wppb_defaultOptions['nicknameRequired'] == 'yes'){
					$errorMark = '<font color="red" title="This field is marked as required by the administrator.">*</font>';
					if ((trim($_POST['nickname']) == '') && isset($_POST['nickname'])){
						$errorMark = '<img src="'.wppb_plugin_url . '/assets/images/pencil_delete.png" title="This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator)."/>';
						$errorVar = ' errorHolder';
					}
				}					
				$editProfileFilterArray['contentName5'] = '
					<p class="nickname'.$errorVar.'">
						<label for="nickname">'. __('Nickname', 'profilebuilder') .$errorMark.'</label>
						<input class="text-input" name="nickname" type="text" id="nickname" value="'. get_the_author_meta( 'nickname', $current_user->id ) .'" />
						<span class="wppb-description-delimiter">'. __('(required)', 'profilebuilder') .'</span>
					</p><!-- .nickname -->';
				$editProfileFilterArray['contentName5'] = apply_filters('wppb_edit_profile_content_name5', $editProfileFilterArray['contentName5']);
				echo $editProfileFilterArray['contentName5'];	
			}
				
			if ($wppb_defaultOptions['dispname'] == 'show'){
				$errorVar = '';
				$errorMark = '';
				if ($wppb_defaultOptions['dispnameRequired'] == 'yes'){
					$errorMark = '<font color="red" title="This field is marked as required by the administrator.">*</font>';
					if ((trim($_POST['display_name']) == '') && isset($_POST['display_name'])){
						$errorMark = '<img src="'.wppb_plugin_url . '/assets/images/pencil_delete.png" title="This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator)."/>';
						$errorVar = ' errorHolder';
					}
				}					
				echo '
					<p class="display_name'.$errorVar.'">
						<label for="display_name">'. __('Display name publicly as', 'profilebuilder') .$errorMark.'</label>
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
					echo '
						</select>
					</p><!-- .display_name -->';
			}

			$editProfileFilterArray['contentInfo1'] = '<p class="contactInfoHeader"><strong>'. __('Contact Info', 'profilebuilder') .'</strong></p>';
			$editProfileFilterArray['contentInfo1'] = apply_filters('wppb_edit_profile_content_info1', $editProfileFilterArray['contentInfo1']);
			echo $editProfileFilterArray['contentInfo1'];				
				
			if ($wppb_defaultOptions['email'] == 'show'){
				$errorVar = '';
				$errorMark = '';
				if ($wppb_defaultOptions['emailRequired'] == 'yes'){
					$errorMark = '<font color="red" title="This field is marked as required by the administrator.">*</font>';
					if ((trim($_POST['email']) == '') && isset($_POST['email'])){
						$errorMark = '<img src="'.wppb_plugin_url . '/assets/images/pencil_delete.png" title="This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator)."/>';
						$errorVar = ' errorHolder';
					}
				}					
				$editProfileFilterArray['contentInfo2'] = '
					<p class="form-email'.$errorVar.'">
						<label for="email">'. __('E-mail', 'profilebuilder') .$errorMark.'</label>
						<input class="text-input" name="email" type="text" id="email" value="'. get_the_author_meta( 'user_email', $current_user->id ) .'" />
						<span class="wppb-description-delimiter">'. __('(required)', 'profilebuilder') .'</span>
					</p><!-- .form-email -->';
				$editProfileFilterArray['contentInfo2'] = apply_filters('wppb_edit_profile_content_info2', $editProfileFilterArray['contentInfo2']);
				echo $editProfileFilterArray['contentInfo2'];
			}
				
			if ($wppb_defaultOptions['website'] == 'show'){
				$errorVar = '';
				$errorMark = '';
				if ($wppb_defaultOptions['websiteRequired'] == 'yes'){
					$errorMark = '<font color="red" title="This field is marked as required by the administrator.">*</font>';
					if ((trim($_POST['website']) == '') && isset($_POST['website'])){
						$errorMark = '<img src="'.wppb_plugin_url . '/assets/images/pencil_delete.png" title="This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator)."/>';
						$errorVar = ' errorHolder';
					}
				}					
				$editProfileFilterArray['contentInfo3'] = '
					<p class="form-website'.$errorVar.'">
						<label for="website">'. __('Website', 'profilebuilder') .$errorMark.'</label>
						<input class="text-input" name="website" type="text" id="website" value="'. get_the_author_meta( 'user_url', $current_user->id ) .'" />
					</p><!-- .form-website -->';
				$editProfileFilterArray['contentInfo3'] = apply_filters('wppb_edit_profile_content_info3', $editProfileFilterArray['contentInfo3']);
				echo $editProfileFilterArray['contentInfo3'];
			}

			if ($wppb_defaultOptions['aim'] == 'show'){
				$errorVar = '';
				$errorMark = '';
				if ($wppb_defaultOptions['aimRequired'] == 'yes'){
					$errorMark = '<font color="red" title="This field is marked as required by the administrator.">*</font>';
					if ((trim($_POST['aim']) == '') && isset($_POST['aim'])){
						$errorMark = '<img src="'.wppb_plugin_url . '/assets/images/pencil_delete.png" title="This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator)."/>';
						$errorVar = ' errorHolder';
					}
				}					
				$editProfileFilterArray['contentInfo4'] = '
					<p class="form-aim'.$errorVar.'">
						<label for="aim">'. __('AIM', 'profilebuilder') .'</label>
						<input class="text-input" name="aim" type="text" id="aim" value="'. get_the_author_meta( 'aim', $current_user->id ) .'" />
					</p><!-- .form-aim -->';
				$editProfileFilterArray['contentInfo4'] = apply_filters('wppb_edit_profile_content_info4', $editProfileFilterArray['contentInfo4']);
				echo $editProfileFilterArray['contentInfo4'];
			}
				
			if ($wppb_defaultOptions['yahoo'] == 'show'){
				$errorVar = '';
				$errorMark = '';
				if ($wppb_defaultOptions['yahooRequired'] == 'yes'){
					$errorMark = '<font color="red" title="This field is marked as required by the administrator.">*</font>';
					if ((trim($_POST['yim']) == '') && isset($_POST['yim'])){
						$errorMark = '<img src="'.wppb_plugin_url . '/assets/images/pencil_delete.png" title="This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator)."/>';
						$errorVar = ' errorHolder';
					}
				}					
				$editProfileFilterArray['contentInfo5'] = '
					<p class="form-yim'.$errorVar.'">
						<label for="yim">'. __('Yahoo IM', 'profilebuilder') .$errorMark.'</label>
						<input class="text-input" name="yim" type="text" id="yim" value="'. get_the_author_meta( 'yim', $current_user->id ) .'" />
					</p><!-- .form-yim -->';
				$editProfileFilterArray['contentInfo5'] = apply_filters('wppb_edit_profile_content_info5', $editProfileFilterArray['contentInfo5']);
				echo $editProfileFilterArray['contentInfo5'];
			}
 
			if ($wppb_defaultOptions['jabber'] == 'show'){
				$errorVar = '';
				$errorMark = '';
				if ($wppb_defaultOptions['jabberRequired'] == 'yes'){
					$errorMark = '<font color="red" title="This field is marked as required by the administrator.">*</font>';
					if ((trim($_POST['jabber']) == '') && isset($_POST['jabber'])){
						$errorMark = '<img src="'.wppb_plugin_url . '/assets/images/pencil_delete.png" title="This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator)."/>';
						$errorVar = ' errorHolder';
					}
				}					
				$editProfileFilterArray['contentInfo6'] = '
					<p class="form-jabber'.$errorVar.'">
						<label for="jabber">'. __('Jabber / Google Talk', 'profilebuilder') .$errorMark.'</label>
						<input class="text-input" name="jabber" type="text" id="jabber" value="'. get_the_author_meta( 'jabber', $current_user->id ) .'" />
					</p><!-- .form-jabber -->';
				$editProfileFilterArray['contentInfo6'] = apply_filters('wppb_edit_profile_content_info6', $editProfileFilterArray['contentInfo6']);
				echo $editProfileFilterArray['contentInfo6'];
			}
				
			$editProfileFilterArray['aboutYourself1'] = '<p class="aboutYourselfHeader"><strong>'. __('About Yourself', 'profilebuilder') .'</strong></p>';
			$editProfileFilterArray['aboutYourself1'] = apply_filters('wppb_edit_profile_content_about_yourself1', $editProfileFilterArray['aboutYourself1']);
			echo $editProfileFilterArray['aboutYourself1'];
				
			if ($wppb_defaultOptions['bio'] == 'show'){
				$errorVar = '';
				$errorMark = '';
				if ($wppb_defaultOptions['bioRequired'] == 'yes'){
					$errorMark = '<font color="red" title="This field is marked as required by the administrator.">*</font>';
					if ((trim($_POST['description']) == '') && isset($_POST['description'])){
						$errorMark = '<img src="'.wppb_plugin_url . '/assets/images/pencil_delete.png" title="This field wasn\'t updated because you entered and empty string (It was marked as required by the administrator)."/>';
						$errorVar = ' errorHolder';
					}
				}					
				$editProfileFilterArray['aboutYourself2'] = '
					<p class="form-description'.$errorVar.'">
						<label for="description">'. __('Biographical Info', 'profilebuilder') .$errorMark.'</label>
						<textarea class="text-input" name="description" id="description" rows="5" cols="30">'. get_the_author_meta( 'description', $current_user->id ) .'</textarea>
					</p><!-- .form-description -->';
				$editProfileFilterArray['aboutYourself2'] = apply_filters('wppb_edit_profile_content_about_yourself2', $editProfileFilterArray['aboutYourself2']);
				echo $editProfileFilterArray['aboutYourself2'];
			}
				
			if ($wppb_defaultOptions['password'] == 'show'){
			
				$editProfileFilterArray['aboutYourself3'] = '
					<p class="form-password">
						<label for="pass1">'. __('New Password', 'profilebuilder') .'</label>
						<input class="text-input" name="pass1" type="password" id="pass1" />
					</p><!-- .form-password -->

					<p class="form-password'.$errorVar.'">
						<label for="pass2">'. __('Repeat Password', 'profilebuilder') .$errorMark.'</label>
						<input class="text-input" name="pass2" type="password" id="pass2" />
					</p><!-- .form-password -->';
				$editProfileFilterArray['aboutYourself3'] = apply_filters('wppb_edit_profile_content_about_yourself3', $editProfileFilterArray['aboutYourself3']);
				echo $editProfileFilterArray['aboutYourself3'];
			}
				

			$wppb_premium = wppb_plugin_dir . '/premium/functions/';
			if (file_exists ( $wppb_premium.'extra.fields.php' )){
				require_once($wppb_premium.'extra.fields.php');
				edit_profile_extra_fields($current_user->id, $extraFieldsErrorHolder);
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
	
	$editProfileFilterArray = apply_filters('wppb_edit_profile', $editProfileFilterArray);
	
    return $output;
}
?>