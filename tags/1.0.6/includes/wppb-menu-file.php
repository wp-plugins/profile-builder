<?php

function wppb_display_menu(){

?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2> Profile Builder </h2>
		<?php  if ($_GET["settings-updated"] == 'true')	
						echo'<div id="message" class="updated below-h2">
							<p>
							Changes saved.
							</p>
						</div>';
		?>
		<h3><?php _e('Basic Information'); ?> </h3>
		<table class="wp-list-table widefat fixed pages" cellspacing="0">
		
		<tbody class="plugins">
			<tr height="10"></tr>
			<tr>
				<td><font size="2">Welcome to Profile Builder!</font></td>
			</tr>
			<tr height="10"></tr>
			<tr>
				<td>Profile Builder lets you customize your website by adding a front-end menu for all your users, giving them a more flexible way to modify their user-information or to register new users. Also, grants users with administrator rights to customize basic fields. To achieve this, just create a new page, and give it an intuitive name(i.e. Edit Profile).</td>
			</tr>
			<tr>
				<td>Now all you need to do is add the following shortcode(for the previous example): [wppb-edit-profile]. Publish your page and you are done!</td>
			</tr>	
			<tr>
				<td>You can use the following shortcodes:</td>
			</tr>
			<tr>
				<td><span style="padding-left:50px"></span>&rarr; [wppb-edit-profile] - to grant users a front-end acces to their personal information(requires user to be logged in).</td>
			</tr>
			<tr>
				<td><span style="padding-left:50px"></span>&rarr; [wppb-login] - for a basic log-in menu.</td>
			</tr>
			<tr>
				<td><span style="padding-left:50px"></span>&rarr; [wppb-register] - to add a registration form.</td>
			</tr>
			<tr height="10"></tr>
			<tr>
				<td>Also, users with administrator rights have access to the following features:</td>
			</tr>
			<tr>
				<td><span style="padding-left:50px"></span>&rarr; add a custom stylesheet/inherit values from the current theme or use the default one, built into this plug-in.</td>
			</tr>
			<tr>
				<td><span style="padding-left:50px"></span>&rarr; select whether to display or not the admin bar in the front end for a specific user-group registered to the site.</td>
			</tr>
			<tr>
				<td><span style="padding-left:50px"></span>&rarr; select which information-field can the users see/modify. The hidden fields' values remain unmodified.</td>
			</tr>
			<tr>
				<td>NOTE: this plugin only adds/removes fields in the front-end. The default information-fields will still be visible(and thus modifiable) from the back-end, while custom fields will only be visible in the front-end.</td>
			</tr>
		</tbody>
		
		</table>
		
		
		<form method="post" action="options.php">
		<?php $wppb_showDefaultCss = get_option('wppb_default_style'); ?>
		<?php settings_fields('wppb_default_style'); ?>
		<br/>
		
		<h3><?php _e('Plug-in Layout'); ?> </h3>
		<table class="wp-list-table widefat fixed pages" cellspacing="0">

		<tbody class="plugins">
			<tr height="10"></tr>
			<tr>
				<td><font size="2">Use default stylesheet:</font></td>
				<td>
					<select name="wppb_default_style" width="60" style="width: 60px">
						<option value="yes" <?php if ($wppb_showDefaultCss == 'yes') echo ' selected';?>>yes</option>
						<option value="no" <?php if ($wppb_showDefaultCss == 'no') echo ' selected';?>>no</option>
					</select>
				
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<input type="hidden" name="action" value="update" />
					<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />    <?php //Note the use of the _e() function to handle translation of the text ?>
					</p>
					</form>
				</td>
			</tr>		
		</tbody>
		</table>
		
		
		
		
		
		
		<form method="post" action="options.php">
		<?php $wppb_showAdminBar = get_option('wppb_display_admin_settings'); ?>
		<?php settings_fields('wppb_display_admin_settings'); ?>
		
		<br/>
		
		<h3><?php _e('Show/Hide the Admin Bar on Front End'); ?> </h3>
		<table class="wp-list-table widefat fixed pages" cellspacing="0">
			<thead>
				<tr>
					<th class="manage-column" scope="col">User-group</th>
					<th class="manage-column" scope="col">Visibility</th>
				</tr>
			</thead>
				<tbody>
					<tr height="10"></tr>
					<?php
					foreach($wppb_showAdminBar as $key => $data){
						echo'<tr> 
								<td> 
									<font size="2">'.$key.'</font>
								</td>
								<td>
									<input type="radio" name="wppb_display_admin_settings['.$key.']" value="show"';if ($wppb_showAdminBar[$key] == 'show') echo ' checked';echo'/><font size="1">Show</font><span style="padding-left:20px"></span>
									<input type="radio" name="wppb_display_admin_settings['.$key.']" value="hide"';if ($wppb_showAdminBar[$key] == 'hide') echo ' checked';echo'/><font size="1">Hide</font>
								</td> 
							</tr>';
					}
					?>
				
				<tbody class="plugins">
					<tr height="10"></tr>
					<tr> 
						<td>  
						</td> 
						<td> 
							<input type="hidden" name="action" value="update" />
							<p class="submit">
								<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />    <?php //Note the use of the _e() function to handle translation of the text ?>
							</p>
							</form>
						</td> 
					</tr>

				</tbody>
		</table>
		
		
		
		<form method="post" action="options.php">
		<?php $wppb_defaultOptions = get_option('wppb_default_settings'); ?>
		<?php settings_fields('wppb-option-group'); ?>
		
		<br/>
		
		<h3><?php _e('Default Profile Fields'); ?> </h3>
		<table class="wp-list-table widefat fixed pages" cellspacing="0">
			<thead>
				<tr>
					<th class="manage-column" scope="col">Input Field Name</th>
					<th class="manage-column" scope="col">Visibility</th>
				</tr>
			</thead>
				<tbody class="plugins" > 
					<tr height="10"></tr>
					<tr>
						<td><font size="4">Name:</font></td> 
						<td></td> 
					</tr>
				</tbody>
				<tbody>
					<tr>  
						<td> 
							<span style="padding-left:50px"></span><font size="2">Username</font>
						</td> 
						<td> 
							<input type="radio" name="wppb_default_settings[username]" value="show" checked /><font size="1">Show</font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[username]" value="hide" disabled /><font size="1" color="grey">Hide</font>
						</td> 
					</tr>
					<tr>  
						<td> 
							<span style="padding-left:50px"></span><font size="2">First Name</font>
						</td> 
						<td> 
							<input type="radio" name="wppb_default_settings[firstname]" value="show" <?php if ($wppb_defaultOptions['firstname'] == 'show') echo 'checked';?> /><font size="1">Show</font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[firstname]" value="hide" <?php if ($wppb_defaultOptions['firstname'] == 'hide') echo 'checked';?> /><font size="1">Hide</font>
						</td> 
					</tr>
					<tr>  
						<td> 
							<span style="padding-left:50px"></span><font size="2">Last Name</font>
						</td> 
						<td> 
							<input type="radio" name="wppb_default_settings[lastname]" value="show" <?php if ($wppb_defaultOptions['lastname'] == 'show') echo 'checked';?> /><font size="1">Show</font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[lastname]" value="hide" <?php if ($wppb_defaultOptions['lastname'] == 'hide') echo 'checked';?> /><font size="1">Hide</font>
						</td> 
					</tr>
					<tr>  
						<td> 
							<span style="padding-left:50px"></span><font size="2">Nickname</font>
						</td> 
						<td> 
							<input type="radio" name="wppb_default_settings[nickname]" value="show" <?php if ($wppb_defaultOptions['nickname'] == 'show') echo 'checked';?> /><font size="1">Show</font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[nickname]" value="hide" <?php if ($wppb_defaultOptions['nickname'] == 'hide') echo 'checked';?> /><font size="1">Hide</font>
						</td> 
					</tr>
					<tr>  
						<td> 
							<span style="padding-left:50px"></span><font size="2">Display name publicly as ...</font>
						</td> 
						<td> 
							<input type="radio" name="wppb_default_settings[dispname]" value="show" <?php if ($wppb_defaultOptions['dispname'] == 'show') echo 'checked';?> /><font size="1">Show</font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[dispname]" value="hide" <?php if ($wppb_defaultOptions['dispname'] == 'hide') echo 'checked';?> /><font size="1">Hide</font>
						</td> 
					</tr>
				<tbody class="plugins">
					<tr height="10"></tr>
					<tr> 
						<td><font size="4">Contact Info:</font></td> 
						<td></td> 
					</tr>
				</tbody>
				<tbody>
					<tr>  
						<td> 
							<span style="padding-left:50px"></span><font size="2">E-mail</font>
						</td> 
						<td> 
							<input type="radio" name="wppb_default_settings[email]" value="show" checked><font size="1">Show</font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[email]" value="hide" disabled><font size="1" color="grey">Hide</font>
						</td> 
					</tr>
					<tr>  
						<td> 
							<span style="padding-left:50px"></span><font size="2">Website</font>
						</td> 
						<td> 
							<input type="radio" name="wppb_default_settings[website]" value="show" <?php if ($wppb_defaultOptions['website'] == 'show') echo 'checked';?> /><font size="1">Show</font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[website]" value="hide" <?php if ($wppb_defaultOptions['website'] == 'hide') echo 'checked';?> /><font size="1">Hide</font>
						</td> 
					</tr>
				<tbody class="plugins">
				</tbody>
				<tbody>
					<tr>  
						<td> 
							<span style="padding-left:50px"></span><font size="2">AIM</font>
						</td> 
						<td> 
							<input type="radio" name="wppb_default_settings[aim]" value="show" <?php if ($wppb_defaultOptions['aim'] == 'show') echo 'checked';?> /><font size="1">Show</font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[aim]" value="hide" <?php if ($wppb_defaultOptions['aim'] == 'hide') echo 'checked';?> /><font size="1">Hide</font>
						</td> 
					</tr>
					<tr>  
						<td> 
							<span style="padding-left:50px"></span><font size="2">Yahoo IM</font>
						</td> 
						<td> 
							<input type="radio" name="wppb_default_settings[yahoo]" value="show" <?php if ($wppb_defaultOptions['yahoo'] == 'show') echo 'checked';?> /><font size="1">Show</font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[yahoo]" value="hide" <?php if ($wppb_defaultOptions['yahoo'] == 'hide') echo 'checked';?> /><font size="1">Hide</font>
						</td> 
					</tr>
					<tr>  
						<td> 
							<span style="padding-left:50px"></span><font size="2">Jabber / Google Talk</font>
						</td> 
						<td> 
							<input type="radio" name="wppb_default_settings[jabber]" value="show" <?php if ($wppb_defaultOptions['jabber'] == 'show') echo 'checked';?> /><font size="1">Show</font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[jabber]" value="hide" <?php if ($wppb_defaultOptions['jabber'] == 'hide') echo 'checked';?> /><font size="1">Hide</font>
						</td> 
					</tr>
				<tbody class="plugins">
					<tr height="10"></tr>
					<tr> 
						<td><font size="4">About Yourself:</font></td> 
						<td></td> 
					</tr>
				</tbody>
				<tbody>
					<tr>  
						<td> 
							<span style="padding-left:50px"></span><font size="2">Biographical Info</font>
						</td> 
						<td> 
							<input type="radio" name="wppb_default_settings[bio]" value="show" <?php if ($wppb_defaultOptions['bio'] == 'show') echo 'checked';?> /><font size="1">Show</font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[bio]" value="hide" <?php if ($wppb_defaultOptions['bio'] == 'hide') echo 'checked';?> /><font size="1">Hide</font>
						</td> 
					</tr>
				<tbody class="plugins">
					<tr> 
						<td>  
						</td> 
						<td> 
						</td> 
					</tr>
				</tbody>
				<tbody>
					<tr>  
						<td> 
							<span style="padding-left:50px"></span><font size="2">New Password</font>
						</td> 
						<td> 
							<input type="radio" name="wppb_default_settings[password]" value="show" checked><font size="1">Show</font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_default_settings[password]" value="hide" disabled><font size="1" color="grey">Hide</font>
						</td> 
					</tr>
				<tbody class="plugins">
					<tr height="10"></tr>
					<tr> 
						<td>  
						</td> 
						<td> 
							<input type="hidden" name="action" value="update" />
							<p class="submit">
								<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />    <?php //Note the use of the _e() function to handle translation of the text ?>
							</p>
							</form>
						</td> 
					</tr>

				</tbody>
		</table>	
		
</div>

<?php
}
?>