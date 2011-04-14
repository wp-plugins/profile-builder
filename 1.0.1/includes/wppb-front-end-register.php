<?php

function wppb_front_end_register(){
	$wppb_defaultOptions = get_option('wppb_default_settings');
	global $current_user;
	get_currentuserinfo(); 
	
	/* Load registration file. */
	require_once( ABSPATH . WPINC . '/registration.php' );

	/* Check if users can register. */
	$registration = get_option( 'users_can_register' );

	/* If user registered, input info. */
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'adduser' && wp_verify_nonce($_POST['register_nonce_field'],'verify_true_registration') ) {
	
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
			'role' => get_option( 'default_role' ),
		);
		
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
		
		else{
			$the_registered_name = $_POST['user_name'];
			$new_user = wp_insert_user( $userdata );
			if (isset($_POST['send_password']) && ($_POST['send_password'] == 1)){
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

		<?php elseif ( $new_user ) : ?>
			
			
			<?php
				
					echo'<p class="success">';
					if ( current_user_can( 'create_users' ) )
						printf( __('A user account for %1$s has been created.', 'profilebuilder'), $the_registered_name );
					else 
						printf( __('Thank you for registering, %1$s.', 'profilebuilder'), $the_registered_name );
					echo'</p><!-- .success -->';
			?>
			
			<?php
				if(isset($_POST['send_password'])){
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

			<form method="post" id="adduser" class="user-forms" action="http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
			
				<p>
				<strong>Name</strong>
				</p>
				
				<?php
				if ($wppb_defaultOptions['username'] == 'show'){ echo'
				<p class="form-username">
					<label for="user_name">'; _e('Username', 'profilebuilder');  echo'<i>'; _e(' (required)', 'profilebuilder');echo'</i></label>
					<input class="text-input" name="user_name" type="text" id="user_name" value="'; if ( $error ) echo wp_specialchars( $_POST['user_name'], 1 ); echo'" />
				</p><!-- .form-username -->';
				}
				?>
				
				<?php
				if ($wppb_defaultOptions['firstname'] == 'show'){ echo'
				<p class="first_name">
					<label for="first_name">'; _e('First Name', 'profilebuilder'); echo'</label>
					<input class="text-input" name="first_name" type="text" id="first_name" value="'; if ( $error ) echo wp_specialchars( $_POST['first_name'], 1 ); echo'" />
				</p><!-- .first_name -->';
				}
				?>
				
				<?php 
				if ($wppb_defaultOptions['lastname'] == 'show'){ echo'
				<p class="last_name">
					<label for="last_name">'; _e('Last Name', 'profilebuilder'); echo'</label>
					<input class="text-input" name="last_name" type="text" id="last_name" value="'; if ( $error ) echo wp_specialchars( $_POST['last_name'], 1 ); echo'" />
				</p><!-- .last_name -->';
				}
				?>
				
				<?php 
				if ($wppb_defaultOptions['nickname'] == 'show'){ echo'
				<p class="nickname">
					<label for="nickname">'; _e('Nickname', 'profilebuilder'); echo'</label>
					<input class="text-input" name="nickname" type="text" id="nickname" value="'; if ( $error ) echo wp_specialchars( $_POST['nickname'], 1 ); echo'" />
				</p><!-- .nickname -->';
				}
				?>
				
				<p>
				<strong>Contact Info</strong>
				</p>
				
				<?php 
				if ($wppb_defaultOptions['email'] == 'show'){ echo'
				<p class="form-email">
					<label for="email">'; _e('E-mail', 'profilebuilder');  echo'<i>'; _e(' (required)', 'profilebuilder');echo'</i></label>
					<input class="text-input" name="email" type="text" id="email" value="'; if ( $error ) echo wp_specialchars( $_POST['email'], 1 ); echo'" />
				</p><!-- .form-email -->';
				}
				?>
				
				<?php 
				if ($wppb_defaultOptions['website'] == 'show'){ echo'
				<p class="form-website">
					<label for="website">'; _e('Website', 'profilebuilder'); echo'</label>
					<input class="text-input" name="website" type="text" id="website" value="'; if ( $error ) echo wp_specialchars( $_POST['website'], 1 ); echo'" />
				</p><!-- .form-website -->';
				}
				?>
				
				<?php 
				if ($wppb_defaultOptions['aim'] == 'show'){ echo'
				<p class="form-aim">
					<label for="aim">'; _e('AIM', 'profilebuilder'); echo'</label>
					<input class="text-input" name="aim" type="text" id="aim" value="'; if ( $error ) echo wp_specialchars( $_POST['aim'], 1 ); echo'" />
				</p><!-- .form-aim -->';
				}
				?>
				
				<?php 
				if ($wppb_defaultOptions['yahoo'] == 'show'){ echo'
				<p class="form-yim">
					<label for="yim">'; _e('Yahoo IM', 'profilebuilder'); echo'</label>
					<input class="text-input" name="yim" type="text" id="yim" value="'; if ( $error ) echo wp_specialchars( $_POST['yim'], 1 ); echo'" />
				</p><!-- .form-yim -->';
				}
				?>
				
				<?php 
				if ($wppb_defaultOptions['jabber'] == 'show'){ echo'
				<p class="form-jabber">
					<label for="jabber">'; _e('Jabber / Google Talk', 'profilebuilder'); echo'</label>
					<input class="text-input" name="jabber" type="text" id="jabber" value="'; if ( $error ) echo wp_specialchars( $_POST['jabber'], 1 ); echo'" />
				</p><!-- .form-jabber -->';
				}
				?>
				
				<p>
				<strong>About Yourself</strong>
				</p>
				
				<?php 
				if ($wppb_defaultOptions['bio'] == 'show'){ echo'
				<p class="form-description">
					<label for="description">'; _e('Biographical Info', 'profilebuilder'); echo'</label>
					<textarea class="text-input" name="description" id="description" rows="5" cols="30">'; if ( $error ) echo wp_specialchars( $_POST['description'], 1 ); echo'</textarea>
				</p><!-- .form-description -->';
				}
				?>
				
				<?php 
				if ($wppb_defaultOptions['password'] == 'show'){ echo'
				<p class="form-password">
					<label for="pass1">'; _e('Password', 'profilebuilder'); echo'</label>
					<input class="text-input" name="passw1" type="password" id="pass1" />
				</p><!-- .form-password -->
 
				<p class="form-password">
					<label for="pass2">'; _e('Repeat Password', 'profilebuilder'); echo'</label>
					<input class="text-input" name="passw2" type="password" id="pass2" />
				</p><!-- .form-password -->';
				}
				?>
				<?php
					if ( current_user_can( 'delete_users' ) )
						echo '
						<p class="send-confirmation-email">
						<label for="pass1">'; 
						//_e('Send Password? ', 'profilebuilder');
						echo'<input id="send_password" type="checkbox" name="send_password" value="1"';if (isset($_POST['send_password'])) echo 'checked';echo'/>
						<i>'; _e(' Send these credentials via email.', 'profilebuilder');echo'</i></label>
						</p><!-- .send-confirmation-email -->';
				?>
					
				<p class="form-submit">
					<?php echo $referer; ?>
					<input name="adduser" type="submit" id="addusersub" class="submit button" value="<?php if ( current_user_can( 'create_users' ) ) _e('Add User', 'profilebuilder'); else _e('Register', 'profilebuilder'); ?>" />
					<input name="action" type="hidden" id="action" value="adduser" />
				</p><!-- .form-submit -->
				<?php wp_nonce_field('verify_true_registration','register_nonce_field'); ?>
			</form><!-- #adduser -->

			<?php endif; ?>

		<?php endif; ?>
		
	
	</div>
<?php
}

?>