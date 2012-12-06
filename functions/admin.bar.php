<?php
function wppb_display_admin_settings(){
?>		
	<form method="post" action="options.php#show-hide-admin-bar">
	<?php 
		global $wp_roles;
	
		$wppb_showAdminBar = get_option('wppb_display_admin_settings');
		settings_fields('wppb_display_admin_settings');
	?>

	
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
							<td id="manage-columnCell">'.$wp_roles->roles[$key]['name'].'</td>
							<td id="manage-columnCell">
								<input type="radio" name="wppb_display_admin_settings['.$key.']" value="show"';if ($wppb_showAdminBar[$key] == 'show') echo ' checked';echo'/><font size="1">'; _e('Show', 'profilebuilder'); echo'</font><span style="padding-left:20px"></span>
								<input type="radio" name="wppb_display_admin_settings['.$key.']" value="hide"';if ($wppb_showAdminBar[$key] == 'hide') echo ' checked';echo'/><font size="1">'; _e('Hide', 'profilebuilder'); echo'</font>
							</td> 
						</tr>';
				}
				?>
			
	</table>
	
	<?php	
			echo '<div id="layoutNoticeDiv">
					<font size="1" id="layoutNotice">
						<b>'. __('NOTE:', 'profilebuilder') .'</b><br/>
						&rarr; '. __('If you added new roles (via another plugin) <u>after</u> Profile Builder was activated, please reactivate it, since the roles are initialized during plugin activation.', 'profilebuilder') .'
					</font>
				</div>';
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