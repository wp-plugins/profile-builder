<?php
	/* this is for the backwards compatibility from v.1.1.12 to v.1.1.13 */
	$update = false;
	$arraySettingsPresent = get_option('wppb_custom_fields','not_found');
		if ($arraySettingsPresent != 'not_found'){
			foreach ($arraySettingsPresent as $key => $value){
				if ($value['item_metaName'] == null){
					$arraySettingsPresent[$key]['item_metaName'] = 'custom_field_'.$value['id'];
					$update = true;
				}
				if ($value['item_LastMetaName'] == null){
					$arraySettingsPresent[$key]['item_LastMetaName'] = 'custom_field_'.$value['id'];
					$update = true;
				}
			}
			// only update if it is needed
			if ($update == true)
				update_option( 'wppb_custom_fields', $arraySettingsPresent);
		}
	/* END backwards compatibility */

	function wppb_basic_info(){
?>

		<h2><?php _e('Profile Builder', 'profilebuilder');?></h2>
        <h3><?php _e('Welcome to Profile Builder!', 'profilebuilder');?></h3>
		<p>
		<strong><?php _e('Profile Builder', 'profilebuilder');?></strong><?php _e(' lets you customize your website by adding a front-end menu for all your users, giving them a more flexible way to modify their user-information or to register new users.', 'profilebuilder');?><br/><br/>
		<?php _e('Also, grants users with administrator rights to customize basic fields or to add new ones.', 'profilebuilder');?><br/><br/>
		<?php _e('To achieve this, just create a new page, and give it an intuitive name(e.g. Edit Profile).', 'profilebuilder');?><br/>
		<?php _e('Now all you need to do is add the following shortcode(for the previous example): ', 'profilebuilder');?>[wppb-edit-profile].<br/>
		<?php _e('Publish your page and you are ready to go!', 'profilebuilder');?><br/><br/>
		<?php _e('You can use the following shortcodes:', 'profilebuilder');?><br/>
		&rarr; <strong>[wppb-login]</strong> - <?php _e('for a log-in form.', 'profilebuilder');?><br/>
		&rarr; <strong>[wppb-register]</strong> - <?php _e('to add a registration form.', 'profilebuilder');?><br/>
		&rarr; <strong>[wppb-edit-profile]</strong> - <?php _e('to grant users a front-end acces to their personal information(requires user to be logged in).', 'profilebuilder');?><br/>
		&rarr; <strong>[wppb-recover-password]</strong> - <?php _e('to add a password recovery form.', 'profilebuilder');?><br/><br/>
		
		<?php _e('Users with administrator rights have access to the following features:', 'profilebuilder');?><br/>
		&rarr; <?php _e('add a custom stylesheet/inherit values from the current theme or use one of the following built into this plugin: default, white or black.', 'profilebuilder');?><br/>
		&rarr; <?php _e('select whether to display or not the admin bar in the front end for a specific user-group registered to the site.', 'profilebuilder');?><br/>
		&rarr; <?php _e('select which information-field can users see/modify. The hidden fields\' values remain unmodified.', 'profilebuilder');?><br/>
		&rarr; <?php _e('add custom fields to the existing ones, with several types to choose from: heading, text, textarea, select, checkbox, radio, and/or upload.', 'profilebuilder');?><br/>
		&rarr; <?php _e('add an avatar field.', 'profilebuilder');?><br/>
		&rarr; <?php _e('create custom redirects.', 'profilebuilder');?><br/>
		&rarr; <?php echo $echoString = __('front-end userlisting using the', 'profilebuilder').' <strong>[wppb-list-users]</strong> '. __('shortcode.', 'profilebuilder');?><br/>
		<br/>

		<strong><?php _e('NOTE:', 'profilebuilder');?></strong>
		<?php _e('this plugin only adds/removes fields in the front-end.', 'profilebuilder');?><br/>
		<?php _e('The default information-fields will still be visible(and thus modifiable)', 'profilebuilder');?> 
		<?php _e('from the back-end, while custom fields will only be visible in the front-end.', 'profilebuilder');?>
		</p>
		
<?php
	}
?>

<?php
	function wppb_plugin_layout(){
?>		
		<form method="post" action="options.php#plugin-layout">
		<?php $wppb_showDefaultCss = get_option('wppb_default_style'); ?>
		<?php settings_fields('wppb_default_style'); ?>

		<h2><?php _e('Plugin Layout', 'profilebuilder');?></h2>
		<h3><?php _e('Plugin Layout', 'profilebuilder');?></h3>
		<font size="2"><?php _e('Stylesheet used:', 'profilebuilder');?></font>
		<select name="wppb_default_style" class="wppb_default_style">
			<option value="yes" <?php if ($wppb_showDefaultCss == 'yes') echo 'selected';?>><?php _e('Default', 'profilebuilder');?></option>
			<?php 
				$wppb_premiumStyle = WPPB_PLUGIN_DIR . '/premium/';	
				if (file_exists ( $wppb_premiumStyle.'premium.php' )){
			?>
					<option value="white" <?php if ($wppb_showDefaultCss == 'white') echo 'selected';?>><?php _e('White', 'profilebuilder');?></option>
					<option value="black" <?php if ($wppb_showDefaultCss == 'black') echo 'selected';?>><?php _e('Black', 'profilebuilder');?></option>
			<?php
				}
			?>
			<option value="no" <?php if ($wppb_showDefaultCss == 'no') echo 'selected';?>><?php _e('None', 'profilebuilder');?></option>
		</select>
		<?php	
			if (file_exists ( $wppb_premiumStyle.'premium.php' ))
				echo '<div id="layoutNoticeDiv"><font size="1" id="layoutNotice"><b>'. __('NOTE:', 'profilebuilder') .'</b><br/>&rarr; '. __('The black stylesheet is intended for sites/blogs with a dark background.', 'profilebuilder') .'<br/>&rarr; '. __('The white stylesheet is intended for a sites/blogs with a light background color.', 'profilebuilder') .'</font></div>';
		?>
		<div align="right">
			<input type="hidden" name="action" value="update" />
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /> 
			</p>
		</div>
		</form>
		
		
<?php
	}
?>		

<?php
	function wppb_display_admin_settings(){
?>		
		<form method="post" action="options.php#show-hide-admin-bar">
		<?php $wppb_showAdminBar = get_option('wppb_display_admin_settings'); ?>
		<?php settings_fields('wppb_display_admin_settings'); ?>

		
		<h2><?php _e('Show/Hide the Admin Bar on Front End', 'profilebuilder');?></h2>
		<h3><?php _e('Show/Hide the Admin Bar on Front End', 'profilebuilder');?></h3>
		<table class="wp-list-table widefat fixed pages" cellspacing="0">
			<thead>
				<tr>
					<th id="manage-column" scope="col"><?php _e('User-group', 'profilebuilder');?></th>
					<th id="manage-column" scope="col"><?php _e('Visibility', 'profilebuilder');?></th>
				</tr>
			</thead>
				<tbody>
					<?php
					foreach($wppb_showAdminBar as $key => $data){
						echo'<tr> 
								<td id="manage-columnCell">'.$key.'</td>
								<td id="manage-columnCell">
									<input type="radio" name="wppb_display_admin_settings['.$key.']" value="show"';if ($wppb_showAdminBar[$key] == 'show') echo ' checked';echo'/><font size="1">'; _e('Show', 'profilebuilder'); echo'</font><span style="padding-left:20px"></span>
									<input type="radio" name="wppb_display_admin_settings['.$key.']" value="hide"';if ($wppb_showAdminBar[$key] == 'hide') echo ' checked';echo'/><font size="1">'; _e('Hide', 'profilebuilder'); echo'</font>
								</td> 
							</tr>';
					}
					?>
				
		</table>
		<div align="right">
			<input type="hidden" name="action" value="update" />
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /> 
			</p>
		</div>
		</form>
		
		
<?php
	}
?>	
	
<?php
	function wppb_default_settings(){
?>
		<form method="post" action="options.php#default-fields">
		<?php $wppb_defaultOptions = get_option('wppb_default_settings'); ?>
		<?php settings_fields('wppb_option_group'); ?>
		
		
		<h2><?php _e('Default Profile Fields', 'profilebuilder');?></h2>
		<h3><?php _e('Default Profile Fields', 'profilebuilder');?></h3>
		<table class="wp-list-table widefat fixed pages" cellspacing="0">
			<thead>
				<tr>
					<th id="manage-column" scope="col"><?php _e('Input Field Name', 'profilebuilder');?></th>
					<th id="manage-column" scope="col"><?php _e('Visibility', 'profilebuilder');?></th>
					<th id="manage-column" scope="col"><?php _e('Required', 'profilebuilder');?></th>
				</tr>
			</thead>
				<tbody class="plugins" > 
					<tr>
						<td colspan="3"><font size="2"><?php _e('Name:', 'profilebuilder');?></font></td> 
					</tr>
				</tbody>
				<tbody>
					<tr>  
						<td id="manage-columnCell"> 
							<span style="padding-left:50px"></span><?php _e('Username', 'profilebuilder');?>
						</td> 
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[username]" value="show" checked /><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[username]" value="hide" disabled /><font size="1" color="grey"><?php _e('Hide', 'profilebuilder');?></font>
						</td> 						
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[usernameRequired]" value="yes" checked /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[usernameRequired]" value="no" disabled /><font size="1" color="grey"><?php _e('No', 'profilebuilder');?></font>
						</td> 
					</tr>
					<tr>  
						<td id="manage-columnCell"> 
							<span style="padding-left:50px"></span><?php _e('First Name', 'profilebuilder');?>
						</td> 
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[firstname]" value="show" <?php if ($wppb_defaultOptions['firstname'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[firstname]" value="hide" <?php if ($wppb_defaultOptions['firstname'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Hide', 'profilebuilder');?></font>
						</td> 						
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[firstnameRequired]" value="yes" <?php if ($wppb_defaultOptions['firstnameRequired'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[firstnameRequired]" value="no" <?php if ($wppb_defaultOptions['firstnameRequired'] == 'no') echo 'checked';?> /><font size="1"><?php _e('No', 'profilebuilder');?></font>
						</td> 
					</tr>
					<tr>  
						<td id="manage-columnCell"> 
							<span style="padding-left:50px"></span><?php _e('Last Name', 'profilebuilder');?>
						</td> 
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[lastname]" value="show" <?php if ($wppb_defaultOptions['lastname'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[lastname]" value="hide" <?php if ($wppb_defaultOptions['lastname'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Hide', 'profilebuilder');?></font>
						</td>
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[lastnameRequired]" value="yes" <?php if ($wppb_defaultOptions['lastnameRequired'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[lastnameRequired]" value="no" <?php if ($wppb_defaultOptions['lastnameRequired'] == 'no') echo 'checked';?> /><font size="1"><?php _e('No', 'profilebuilder');?></font>
						</td> 
					</tr>
					<tr>  
						<td id="manage-columnCell"> 
							<span style="padding-left:50px"></span><?php _e('Nickname', 'profilebuilder');?>
						</td> 
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[nickname]" value="show" <?php if ($wppb_defaultOptions['nickname'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[nickname]" value="hide" <?php if ($wppb_defaultOptions['nickname'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Hide', 'profilebuilder');?></font>
						</td>
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[nicknameRequired]" value="yes" <?php if ($wppb_defaultOptions['nicknameRequired'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[nicknameRequired]" value="no" <?php if ($wppb_defaultOptions['nicknameRequired'] == 'no') echo 'checked';?> /><font size="1"><?php _e('No', 'profilebuilder');?></font>
						</td> 						
					</tr>
					<tr>  
						<td id="manage-columnCell"> 
							<span style="padding-left:50px"></span><?php _e('Display name publicly as...', 'profilebuilder');?>
						</td> 
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[dispname]" value="show" <?php if ($wppb_defaultOptions['dispname'] == 'show') echo 'checked';?> /><?php _e('Show', 'profilebuilder');?><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[dispname]" value="hide" <?php if ($wppb_defaultOptions['dispname'] == 'hide') echo 'checked';?> /><?php _e('Hide', 'profilebuilder');?>
						</td>
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[dispnameRequired]" value="yes" <?php if ($wppb_defaultOptions['dispnameRequired'] == 'yes') echo 'checked';?> /><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[dispnameRequired]" value="no" <?php if ($wppb_defaultOptions['dispnameRequired'] == 'no') echo 'checked';?> /><?php _e('No', 'profilebuilder');?>
						</td> 						
					</tr>
				<tbody class="plugins">
					<tr> 
						<td colspan="3"><font size="2"><?php _e('Contact Info:', 'profilebuilder');?></font></td> 
					</tr>
				</tbody>
				<tbody>
					<tr>  
						<td id="manage-columnCell"> 
							<span style="padding-left:50px"></span><?php _e('E-mail', 'profilebuilder');?>
						</td> 
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[email]" value="show" checked><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[email]" value="hide" disabled><font size="1" color="grey"><?php _e('Hide', 'profilebuilder');?></font>
						</td>
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[emailRequired]" value="yes" checked /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[emailRequired]" value="no" disabled /><font size="1" color="grey"><?php _e('No', 'profilebuilder');?></font>
						</td> 		
					</tr>
					<tr>  
						<td id="manage-columnCell"> 
							<span style="padding-left:50px"></span><?php _e('Website', 'profilebuilder');?>
						</td> 
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[website]" value="show" <?php if ($wppb_defaultOptions['website'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[website]" value="hide" <?php if ($wppb_defaultOptions['website'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Hide', 'profilebuilder');?></font>
						</td>
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[websiteRequired]" value="yes" <?php if ($wppb_defaultOptions['websiteRequired'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[websiteRequired]" value="no" <?php if ($wppb_defaultOptions['websiteRequired'] == 'no') echo 'checked';?> /><font size="1"><?php _e('No', 'profilebuilder');?></font>
						</td> 						
					</tr>
				<tbody class="plugins">
				</tbody>
				<tbody>
					<tr>  
						<td id="manage-columnCell"> 
							<span style="padding-left:50px"></span><?php _e('AIM', 'profilebuilder');?>
						</td> 
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[aim]" value="show" <?php if ($wppb_defaultOptions['aim'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[aim]" value="hide" <?php if ($wppb_defaultOptions['aim'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Hide', 'profilebuilder');?></font>
						</td>
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[aimRequired]" value="yes" <?php if ($wppb_defaultOptions['aimRequired'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[aimRequired]" value="no" <?php if ($wppb_defaultOptions['aimRequired'] == 'no') echo 'checked';?> /><font size="1"><?php _e('No', 'profilebuilder');?></font>
						</td> 						
					</tr>
					<tr>  
						<td id="manage-columnCell"> 
							<span style="padding-left:50px"></span><?php _e('Yahoo IM', 'profilebuilder');?>
						</td> 
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[yahoo]" value="show" <?php if ($wppb_defaultOptions['yahoo'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[yahoo]" value="hide" <?php if ($wppb_defaultOptions['yahoo'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Hide', 'profilebuilder');?></font>
						</td>
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[yahooRequired]" value="yes" <?php if ($wppb_defaultOptions['yahooRequired'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[yahooRequired]" value="no" <?php if ($wppb_defaultOptions['yahooRequired'] == 'no') echo 'checked';?> /><font size="1"><?php _e('No', 'profilebuilder');?></font>
						</td> 						
					</tr>
					<tr>  
						<td id="manage-columnCell"> 
							<span style="padding-left:50px"></span><?php _e('Jabber / Google Talk', 'profilebuilder');?>
						</td> 
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[jabber]" value="show" <?php if ($wppb_defaultOptions['jabber'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[jabber]" value="hide" <?php if ($wppb_defaultOptions['jabber'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Hide', 'profilebuilder');?></font>
						</td>
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[jabberRequired]" value="yes" <?php if ($wppb_defaultOptions['jabberRequired'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[jabberRequired]" value="no" <?php if ($wppb_defaultOptions['jabberRequired'] == 'no') echo 'checked';?> /><font size="1"><?php _e('No', 'profilebuilder');?></font>
						</td> 						
					</tr>
				<tbody class="plugins">
					<tr> 
						<td  colspan="3"><font size="2"><?php _e('About Yourself:', 'profilebuilder');?></font></td> 
					</tr>
				</tbody>
				<tbody>
					<tr>  
						<td id="manage-columnCell"> 
							<span style="padding-left:50px"></span><?php _e('Biographical Info', 'profilebuilder');?>
						</td> 
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[bio]" value="show" <?php if ($wppb_defaultOptions['bio'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[bio]" value="hide" <?php if ($wppb_defaultOptions['bio'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Hide', 'profilebuilder');?></font>
						</td>
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[bioRequired]" value="yes" <?php if ($wppb_defaultOptions['bioRequired'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[bioRequired]" value="no" <?php if ($wppb_defaultOptions['bioRequired'] == 'no') echo 'checked';?> /><font size="1"><?php _e('No', 'profilebuilder');?></font>
						</td> 
					</tr>
				<tbody>
					<tr>  
						<td id="manage-columnCell"> 
							<span style="padding-left:50px"></span><?php _e('New Password', 'profilebuilder');?>
						</td> 
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[password]" value="show" checked><font size="1"><?php _e('Show', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[password]" value="hide" disabled><font size="1" color="grey"><?php _e('Hide', 'profilebuilder');?></font>
						</td>
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_default_settings[passwordRequired]" value="yes" checked /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[passwordRequired]" value="no" disabled /><font size="1" color="grey"><?php _e('No', 'profilebuilder');?></font>
						</td> 						
					</tr>
				</tbody>
		</table>
		<div align="right">
			<input type="hidden" name="action" value="update" />
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /> 
			</p>
			</form>
		</div>
		
<?php
	}
?>