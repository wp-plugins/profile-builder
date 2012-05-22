<!--
Original Plugin Name: OptionTree
Original Plugin URI: http://wp.envato.com
Original Author: Derek Herman
Original Author URI: http://valendesigns.com
-->
<?php if (!defined('PROFILE_BUILDER_VERSION')) exit('No direct script access allowed'); ?>
<?php require_once('menu.file.php'); ?>

<div id="framework_wrap" class="wrap">
	
	<div id="header">
    <h1>Profile Builder</h1>
    <span class="icon">&nbsp;</span>
    <div class="version">
      <?php echo 'Version ' . PROFILE_BUILDER_VERSION; ?>
    </div>
	</div>
  
  <div id="content_wrap">
      
	  <?php 
	  $wppb_premium = WPPB_PLUGIN_DIR . '/premium/';
	  if (!file_exists ( $wppb_premium.'premium.php' )){
	  ?>
		  <div class="info basic-version-info">
				<img src="<?php echo WPPB_PLUGIN_URL ?>/assets/images/ad_image.png" alt="Profile Builder Pro" />
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="3J62P8ZXKFJM4">
					<input type="image" src="http://beta.cozmoslabs.com/wp-content/plugins/reflection-media-subscriber/includes/icons/buy_now_button.png" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
				<a href="http://www.cozmoslabs.com/wordpress-profile-builder/">Or Learn More</a>
				
		  </div>
	  <?php
	  }
	  ?>
      <div class="info top-info"></div>
      
	  <?php $wppb_premium = WPPB_PLUGIN_DIR . '/premium/';
		if (file_exists ( $wppb_premium.'premium.php' )){
			echo '<div class="ajax-message'; 
			if ( isset( $message ) ) { echo ' show'; } 
			echo '">';
			if ( isset( $message ) ) { echo $message; } 
			echo '</div>';
		}
		?>
      
      <div id="content">
      
        <div id="options_tabs">
        
          <ul class="options_tabs">
			<li><a href="#profile-builder"><?php _e('Basic Information','profilebuilder');?></a><span></span></li>
			<li><a href="#plugin-layout"><?php _e('Plugin Layout','profilebuilder');?></a><span></span></li>
			<li><a href="#show-hide-admin-bar"><?php _e('Show/Hide the Admin Bar on Front-end','profilebuilder');?></a><span></span></li>
			<li><a href="#default-fields"><?php _e('Default Profile Fields','profilebuilder');?></a><span></span></li>
			<?php 
				$wppb_premium = WPPB_PLUGIN_DIR . '/premium/';
				$wppb_addons = WPPB_PLUGIN_DIR . '/premium/addon/';
				
				if (file_exists ( $wppb_premium.'premium.php' )){
					echo '<li><a href="#create-extra-fields">'; _e('Extra Profile Fields','profilebuilder'); echo'</a><span></span></li>'; 
				}
				if (file_exists ( $wppb_addons.'addon.php' )){
					echo '<li><a href="#add-ons">'; _e('Addons','profilebuilder'); echo'</a><span></span></li>'; 
				}
				if (file_exists ( $wppb_premium.'premium.php' )){
					echo '<li><a href="#register-profile-builder">'; _e('Register Your Version','profilebuilder'); echo'</a><span></span></li>'; 
				}
			?>
			<?php 
			$addons_options_set = get_option('wppb_addon_settings','not_found');
			if ($addons_options_set != 'not_found'){ 
				$addons_options_description = get_option('wppb_addon_settings_description'); //fetch the descriptions array
				foreach ($addons_options_set as $key => $value)
					if ($value == 'show'){
						echo '<li><a href="#'.$key.'">'; _e($addons_options_description[$key],'profilebuilder'); echo '</a><span></span></li>';
					}
			}
			?>
			
          </ul>
			<div id="profile-builder" class="block">
			<?php wppb_basic_info(); ?>
			</div>

			<div id="plugin-layout" class="block">
			<?php wppb_plugin_layout(); ?>
			</div>
            
			
			<div id="show-hide-admin-bar" class="block has-table">
			<?php wppb_display_admin_settings(); ?>
			</div>
			
			<div id="default-fields" class="block has-table">
			<?php wppb_default_settings(); ?>
			</div>
			
			<?php $wppb_premium = WPPB_PLUGIN_DIR . '/premium/';
				if (file_exists ( $wppb_premium.'premium.php' )){
					require_once($wppb_premium.'premium.php');
					echo '<div id="create-extra-fields" class="block has-table">';
					wppb_custom_settings();
					echo '</div>';
					echo '<div id="register-profile-builder" class="block">';
					wppb_register_profile_builder();
					echo '</div>';
				}
			?>	
			
			<?php $wppb_addons = WPPB_PLUGIN_DIR . '/premium/addon/';
				if (file_exists ( $wppb_addons.'addon.php' )){
					require_once($wppb_addons.'addon.php');
					echo '<div id="add-ons" class="block has-table">';
					wppb_displayAddons();
					echo '</div>';
					
					$addons_options_set = get_option('wppb_addon_settings','not_found');
					if ($addons_options_set != 'not_found'){ 
						foreach ($addons_options_set as $key => $value)
							if ($value == 'show'){
								echo '<div id="'.$key.'" class="block has-table">';
								$key();
								echo '</div>';
							}
					}
				}
			?>
			
			<br class="clear" />
   
        </div>
        
      </div>
     
      <div class="info bottom"></div> 

  </div>

</div>
<!-- [END] framework_wrap -->