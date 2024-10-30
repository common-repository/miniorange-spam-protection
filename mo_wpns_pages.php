<?php

/*Main function*/
function mo_msp_show_settings() {
	if( isset( $_GET[ 'tab' ] ) ) {
		$active_tab = $_GET[ 'tab' ];
	} else {
		$active_tab = 'default';
	}
	
	?>
	<h2>miniOrange Spam Protection</h2>
	<?php
		if(!Mo_MSP_Util::is_curl_installed()) {
			?>
			
			<div id="help_curl_warning_title" class="mo_wpns_title_panel">
				<p><a target="_blank" style="cursor: pointer;"><font color="#FF0000">Warning: PHP cURL extension is not installed or disabled. <span style="color:blue">Click here</span> for instructions to enable it.</font></a></p>
			</div>
			<div hidden="" id="help_curl_warning_desc" class="mo_wpns_help_desc">
					<ul>
						<li>Step 1:&nbsp;&nbsp;&nbsp;&nbsp;Open php.ini file located under php installation folder.</li>
						<li>Step 2:&nbsp;&nbsp;&nbsp;&nbsp;Search for <b>extension=php_curl.dll</b> </li>
						<li>Step 3:&nbsp;&nbsp;&nbsp;&nbsp;Uncomment it by removing the semi-colon(<b>;</b>) in front of it.</li>
						<li>Step 4:&nbsp;&nbsp;&nbsp;&nbsp;Restart the Apache Server.</li>
					</ul>
					For any further queries, please <a href="mailto:info@xecurify.com">contact us</a>.
			</div>
					
			<?php
		}
		
		if(!Mo_MSP_Util::is_extension_installed('mcrypt')) {
			?>
			<p><font color="#FF0000">(Warning: <a target="_blank" href="http://php.net/manual/en/mcrypt.installation.php">PHP mcrypt extension</a> is not installed or disabled)</font></p>
			<?php
		}
		
	?>
	<div class="mo2f_container">
		<h2 class="nav-tab-wrapper">
		
			<a class="nav-tab <?php echo $active_tab == 'default' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'default'), $_SERVER['REQUEST_URI'] ); ?>">SPAM Protection</a>
			<a class="nav-tab <?php echo $active_tab == 'content-protection' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'content-protection'), $_SERVER['REQUEST_URI'] ); ?>">Content Protection</a>
			<a class="nav-tab <?php echo $active_tab == 'licencing' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'licencing'), $_SERVER['REQUEST_URI'] ); ?>">Licensing</a>
					
		</h2>
		<table style="width:100%;">
			<tr>
				<td style="width:75%;vertical-align:top;" id="configurationForm">
					<?php
							if($active_tab == 'content-protection'){ 
								mo_msp_content_protection();
							} else if($active_tab == 'licencing'){ 
								mo_msp_licencing();
							} else if($active_tab == 'account'){ 
								if (get_option ( 'mo_wpns_verify_customer' ) == 'true') {
									mo_msp_login_page();
								} else if(get_option('mo_wpns_registration_status') == 'MO_OTP_DELIVERED_SUCCESS' || get_option('mo_wpns_registration_status') == 'MO_OTP_VALIDATION_FAILURE' || get_option('mo_wpns_registration_status') == 'MO_OTP_DELIVERED_FAILURE'){
									mo_msp_show_otp_verification();
								} else if (! Mo_MSP_Util::is_customer_registered()) {
									mo_msp_registration_page();
								} else{
									mo_msp_account_page();
								}
							} else{
								mo_msp_spam();
							}
					?>
				</td>
				<?php if(!$active_tab == 'licencing'){?>
				<td style="vertical-align:top;padding-left:1%;">
					<?php echo mo_msp_support(); ?>
				</td>
					<?php }?>
			</tr>
		</table>
	</div>
	<?php
}
/*End of main function*/

/* Create Customer function */
function mo_msp_registration_page(){
	
	?>

<!--Register with miniOrange-->
<form name="f" method="post" action="">
	<input type="hidden" name="option" value="mo_wpns_register_customer" />
	<p>Just complete the short registration below to configure miniOrange Spam Protection plugin. Please enter a valid email id that you have access to. You will be able to move forward after verifying an OTP that we will send to this email.</p>
	<div class="mo_wpns_table_layout" style="min-height: 274px;">
		<h3>Register with miniOrange</h3>
		<div id="panel1">
			<table class="mo_wpns_settings_table">
				<tr>
					<td><b><font color="#FF0000">*</font>Email:</b></td>
					<td>
					<?php 	$current_user = wp_get_current_user();
							if(get_option('mo_wpns_admin_email'))
								$admin_email = get_option('mo_wpns_admin_email');
							else
								$admin_email = $current_user->user_email; ?>
					<input class="mo_wpns_table_textbox" type="email" name="email"
						required placeholder="person@example.com"
						value="<?php echo $admin_email;?>" /></td>
				</tr>

				<tr>
					<td><b>Phone number:</b></td>
					<td><input class="mo_wpns_table_textbox" type="tel" id="phone"
						pattern="[\+]\d{11,14}|[\+]\d{1,4}([\s]{0,1})(\d{0}|\d{9,10})" name="phone"
						title="Phone with country code eg. +1xxxxxxxxxx"
						placeholder="Phone with country code eg. +1xxxxxxxxxx"
						value="<?php echo get_option('mo_wpns_admin_phone');?>" />
						<i>We will call only if you call for support</i><br><br></td>
				</tr>
				<tr>
					<td><b><font color="#FF0000">*</font>Password:</b></td>
					<td><input class="mo_wpns_table_textbox" required type="password"
						name="password" placeholder="Choose your password (Min. length 6)" />
					</td>
				</tr>
				<tr>
					<td><b><font color="#FF0000">*</font>Confirm Password:</b></td>
					<td><input class="mo_wpns_table_textbox" required type="password"
						name="confirmPassword" placeholder="Confirm your password" /></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" value="Save"
						class="button button-primary button-large" />
						<input type="submit" value="Login"
						class="button button-primary button-large" />
					</td>
				</tr>
			</table>
		</div>
	</div>
</form>
<!--<script>
	jQuery("#phone").intlTelInput();
</script> -->
<?php
}
/* End of Create Customer function */

/* Login for customer*/
function mo_msp_login_page() {
	?>
		<!--Verify password with miniOrange-->
		<form name="f" method="post" action="">
			<input type="hidden" name="option" value="mo_wpns_verify_customer" />
			<div class="mo_wpns_table_layout">
				<h3>Login with miniOrange</h3>
				<div id="panel1">
					<table class="mo_wpns_settings_table">
						<tr>
							<td><b><font color="#FF0000">*</font>Email:</b></td>
							<td><input class="mo_wpns_table_textbox" type="email" name="email"
								required placeholder="person@example.com"
								value="<?php echo get_option('mo_wpns_admin_email');?>" /></td>
						</tr>
						<tr>
							<td><b><font color="#FF0000">*</font>Password:</b></td>
							<td><input class="mo_wpns_table_textbox" required type="password"
								name="password" placeholder="Enter your miniOrange password" /></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><input type="submit" class="button button-primary button-large" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a
								href="#cancel_link">Cancel</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<a href="#mo_wpns_forgot_password_link">Forgot
									your password?</a></td>
						</tr>
					</table>
				</div>
			</div>
		</form>
		<form id="forgot_password_form" method="post" action="">
			<input type="hidden" name="option" value="mo_wpns_user_forgot_password" />
		</form>
		<form id="cancel_form" method="post" action="">
			<input type="hidden" name="option" value="mo_wpns_cancel" />
		</form>
		<script>

			jQuery('a[href="#cancel_link"]').click(function(){
				jQuery('#cancel_form').submit();
			});

			jQuery('a[href="#mo_wpns_forgot_password_link"]').click(function(){
				jQuery('#forgot_password_form').submit();
			});
		</script>
	<?php
}
/* End of Login for customer*/

/* Account for customer*/
function mo_msp_account_page() {
	?>

			<div style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; width:98%;height:344px">
				<div>
					<h4>Thank You for registering with miniOrange.</h4>
					<h3>Your Profile</h3>
					<table border="1" style="background-color:#FFFFFF; border:1px solid #CCCCCC; border-collapse: collapse; padding:0px 0px 0px 10px; margin:2px; width:85%">
						<tr>
							<td style="width:45%; padding: 10px;">Username/Email</td>
							<td style="width:55%; padding: 10px;"><?php echo get_option('mo_wpns_admin_email')?></td>
						</tr>
						<tr>
							<td style="width:45%; padding: 10px;">Customer ID</td>
							<td style="width:55%; padding: 10px;"><?php echo get_option('mo_wpns_admin_customer_key')?></td>
						</tr>
						<tr>
							<td style="width:45%; padding: 10px;">API Key</td>
							<td style="width:55%; padding: 10px;"><?php echo get_option('mo_wpns_admin_api_key')?></td>
						</tr>
						<tr>
							<td style="width:45%; padding: 10px;">Token Key</td>
							<td style="width:55%; padding: 10px;"><?php echo get_option('mo_wpns_customer_token')?></td>
						</tr>
					</table>
					<br/>
					<p><a href="#mo_wpns_forgot_password_link">Click here</a> if you forgot your password to your miniOrange account.</p>
				</div>
			</div>

			<form id="forgot_password_form" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_reset_password" />
			</form>
			
			<script>
				jQuery('a[href="#mo_wpns_forgot_password_link"]').click(function(){
					jQuery('#forgot_password_form').submit();
				});
			</script>			
			
			<?php
			if( isset($_POST['option']) && ($_POST['option'] == "mo_wpns_verify_customer" ||
					$_POST['option'] == "mo_wpns_register_customer") ){ ?>
				<script>
					//window.location.href = "<?php echo add_query_arg( array('tab' => 'licencing'), $_SERVER['REQUEST_URI'] ); ?>";
				</script>
			<?php }
}
/* End of Account for customer*/

function mo_msp_content_protection(){
	?>
	<div class="mo_wpns_small_layout">	
		<?php if (!Mo_MSP_Util::is_customer_registered()) { ?>
				<div class="warning_div">Please <a href="<?php echo add_query_arg( array('tab' => 'account'), $_SERVER['REQUEST_URI'] ); ?>">Register or Login with miniOrange</a> to configure the miniOrange Spam Protection Plugin.</div>
		<?php } ?>
		<h3>Content Protection</h3>
		<form id="mo_wpns_content_protection" method="post" action="">
			<input type="hidden" name="option" value="mo_wpns_content_protection">
			<p><input type="checkbox" name="protect_wp_config" <?php if(get_option('protect_wp_config')) echo "checked";?>> <b>Protect your wp-config.php file</b> &nbsp;&nbsp;<a href="<?php echo get_site_url();?>/wp-config.php" target="_blank" style="text-decoration:none">( Test it )</a></p>
			<p>Your WordPress wp-config.php file contains your information like database username and password and it's very important to prevent anyone to access contents of your wp-config.php file.</p>
			<p><input type="checkbox" name="prevent_directory_browsing" <?php if(get_option('prevent_directory_browsing')) echo "checked";?>> <b>Prevent Directory Browsing</b> &nbsp;&nbsp;<a href="<?php echo get_site_url();?>/wp-content/uploads" target="_blank" style="text-decoration:none">( Test it )</a></p>
			<p>Prevent access to user from browsing directory contents like images, pdf's and other data from URL e.g. http://website-name.com/wp-content/uploads</p>
			<p><input type="checkbox" name="disable_file_editing" <?php if(get_option('disable_file_editing')) echo "checked";?>> <b>Disable File Editing from WP Dashboard (Themes and plugins)</b> &nbsp;&nbsp;<a href="<?php echo get_site_url();?>/wp-admin/plugin-editor.php" target="_blank" style="text-decoration:none">( Test it )</a></p>
			<p>The WordPress Dashboard by default allows administrators to edit PHP files, such as plugin and theme files. This is often the first tool an attacker will use if able to login, since it allows code execution.</p>
			<br><input type="submit" name="submit" style="width:100px;" value="Save" class="button button-primary button-large">
		</form>
	</div>
	<script>
	<?php if (!Mo_MSP_Util::is_customer_registered()) { ?>
		jQuery( document ).ready(function() {
			jQuery(".mo_wpns_small_layout :input").prop("disabled", true);
		});
	<?php } ?>
	</script>
	<?php
}

function mo_msp_spam(){
	?> 
	<div class="mo_wpns_small_layout">
		<?php if (!Mo_MSP_Util::is_customer_registered()) { ?>
				<div class="warning_div">Please <a href="<?php echo add_query_arg( array('tab' => 'account'), $_SERVER['REQUEST_URI'] ); ?>">Register or Login with miniOrange</a> to configure the miniOrange Spam Protection Plugin.</div>
		<?php } ?>
		<h3>Comment SPAM</h3>
		<p>This plugins prevents comment spam without requiring you to moderate any comments.</p>
		<form id="mo_wpns_enable_comment_spam_blocking" method="post" action="">
			<input type="hidden" name="option" value="mo_wpns_enable_comment_spam_blocking">
			<input type="checkbox" name="mo_wpns_enable_comment_spam_blocking" <?php if(get_option('mo_wpns_enable_comment_spam_blocking')) echo "checked";?> onchange="document.getElementById('mo_wpns_enable_comment_spam_blocking').submit();"> Enable comments SPAM blocking by robots or automated scripts. <span style="color:green;font-weight:bold;">(Recommended)</span>
		</form><br>
		<form id="mo_wpns_enable_comment_recaptcha" method="post" action="">
			<input type="hidden" name="option" value="mo_wpns_enable_comment_recaptcha">
			<input type="checkbox" name="mo_wpns_enable_comment_recaptcha" <?php if(get_option('mo_wpns_enable_comment_recaptcha')) echo "checked";?> onchange="document.getElementById('mo_wpns_enable_comment_recaptcha').submit();"> Add google reCAPTCHA verification for comments <span style="color:green;font-weight:bold;">(Recommended)</span>
		</form><br>
		<?php if(get_option('mo_wpns_enable_comment_recaptcha')){ ?>
			<p>Before you can use reCAPTCHA, you must need to <b>register your domain/webiste</b> <a href="https://www.google.com/recaptcha/admin#list">here</a>.</p>
			<p>Enter Site key and Secret key that you get after registration.</p>
			<form id="mo_wpns_comment_recaptcha_settings" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_comment_recaptcha_settings">
				<table class="mo_wpns_settings_table">
					<tr>
						<td style="width:30%">Site key  : </td>
						<td style="width:25%"><input class="mo_wpns_table_textbox" type="text" name="mo_wpns_recaptcha_site_key" required placeholder="site key" value="<?php echo get_option('mo_wpns_recaptcha_site_key');?>" /></td>
						<td style="width:25%"></td>
					</tr>
					<tr>
						<td>Secret key  : </td>
						<td><input class="mo_wpns_table_textbox" type="text"  name="mo_wpns_recaptcha_secret_key" required placeholder="secret key" value="<?php echo get_option('mo_wpns_recaptcha_secret_key');?>" /></td>
					</tr>
				</table>
				<input type="submit" value="Save Settings" class="button button-primary button-large" />
				<input type="button" value="Test reCAPTCHA Configuration" onclick="testRecaptchaConfiguration()" class="button button-primary button-large" />
			</form>
		<?php } ?>
	</div>
	<script>
		function testRecaptchaConfiguration(){
			var myWindow = window.open('<?php echo site_url(); ?>' + '/?option=testrecaptchaconfig', "Test Google reCAPTCHA Configuration", "width=600, height=600");	
		}
	</script>
	
	
	
		<div class="mo_wpns_small_layout">		
		<h3>Google reCAPTCHA</h3>
		<div class="mo_wpns_subheading">Google reCAPTCHA protects your website from spam and abuse. reCAPTCHA uses an advanced risk analysis engine and adaptive CAPTCHAs to keep automated software from engaging in abusive activities on your site. It does this while letting your valid users pass through with ease.</div>
		<form id="mo_wpns_activate_recaptcha" method="post" action="">
			<input type="hidden" name="option" value="mo_wpns_activate_recaptcha">
			<input type="checkbox" name="mo_wpns_activate_recaptcha" <?php if(get_option('mo_wpns_activate_recaptcha')) echo "checked";?> onchange="document.getElementById('mo_wpns_activate_recaptcha').submit();"> Enable Google reCAPTCHA
		</form>
		<?php if(get_option('mo_wpns_activate_recaptcha')){ ?>
			<p>Before you can use reCAPTCHA, you must need to register your domain/webiste <a href="https://www.google.com/recaptcha/admin#list">here</a>.</p>
			<p>Enter Site key and Secret key that you get after registration.</p>
			<form id="mo_wpns_recaptcha_settings" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_recaptcha_settings">
				<table class="mo_wpns_settings_table">
					<tr>
						<td style="width:30%">Site key  : </td>
						<td style="width:25%"><input class="mo_wpns_table_textbox" type="text" name="mo_wpns_recaptcha_site_key" required placeholder="site key" value="<?php echo get_option('mo_wpns_recaptcha_site_key');?>" /></td>
						<td style="width:25%"></td>
					</tr>
					<tr>
						<td>Secret key  : </td>
						<td><input class="mo_wpns_table_textbox" type="text"  name="mo_wpns_recaptcha_secret_key" required placeholder="secret key" value="<?php echo get_option('mo_wpns_recaptcha_secret_key');?>" /></td>
					</tr>
					<tr>
						<td style="vertical-align:top;">Enable reCAPTCHA for :</td>
						<td><input type="checkbox" name="mo_wpns_activate_recaptcha_for_login" <?php if(get_option('mo_wpns_activate_recaptcha_for_login')) echo "checked";?>> Login form<br>
						<input type="checkbox" name="mo_wpns_activate_recaptcha_for_registration" <?php if(get_option('mo_wpns_activate_recaptcha_for_registration')) echo "checked";?>> Registeration form</td>
					    <td><input type="checkbox" name="mo_wpns_activate_recaptcha_for_bbpress" <?php if(get_option('mo_wpns_activate_recaptcha_for_bbpress')) echo "checked";?>> BBPress Forum<br>
					</tr>
				</table>
				<input type="submit" value="Save Settings" class="button button-primary button-large" />
				<input type="button" value="Test reCAPTCHA Configuration" onclick="testRecaptchaConfiguration()" class="button button-primary button-large" />
			</form>
		<?php } ?>
	</div>
	
	
	
	<div class="mo_wpns_small_layout">	
		<h3>Block Registerations from fake users</h3>
		<div class="mo_wpns_subheading">
			Disallow Disposable / Fake / Temporary email addresses
		</div>
		
		<form id="mo_wpns_enable_fake_domain_blocking" method="post" action="">
			<input type="hidden" name="option" value="mo_wpns_enable_fake_domain_blocking">
			<input type="checkbox" name="mo_wpns_enable_fake_domain_blocking" <?php if(get_option('mo_wpns_enable_fake_domain_blocking')) echo "checked";?> onchange="document.getElementById('mo_wpns_enable_fake_domain_blocking').submit();"> Enable blocking registrations from fake users.
		</form>
		<br>
	</div>
	
	<script>
	<?php if (!Mo_MSP_Util::is_customer_registered()) { ?>
		jQuery( document ).ready(function() {
			jQuery(".mo_wpns_small_layout :input").prop("disabled", true);
		});
	<?php } ?>
	</script>
	<?php
}


function mo_msp_licencing(){
		include 'upgrade.php';
}

/* Show OTP verification page*/
function mo_msp_show_otp_verification(){
	?>
		<div class="mo_wpns_table_layout">
			<div id="panel2">
				<table class="mo_wpns_settings_table">
		<!-- Enter otp -->
				<form name="f" id="back_registration_form" method="post" action="">
							<td>
							<input type="hidden" name="option" value="mo_wpns_registeration_back"/>
							</td>
						</tr>
					</form>
					<form name="f" method="post" id="wpns_form" action="">
						<input type="hidden" name="option" value="mo_wpns_validate_otp" />
						<h3>Verify Your Email</h3>
						<tr>
							<td><b><font color="#FF0000">*</font>Enter OTP:</b></td>
							<td colspan="2"><input class="mo_wpns_table_textbox" autofocus="true" type="text" name="otp_token" required placeholder="Enter OTP" style="width:61%;" />
							 &nbsp;&nbsp;<a style="cursor:pointer;" onclick="document.getElementById('resend_otp_form').submit();">Resend OTP over Email</a></td>
						</tr>
						<tr><td colspan="3"></td></tr>
						<tr><td></td><td>
						<a style="cursor:pointer;" onclick="document.getElementById('back_registration_form').submit();"><input type="button" value="Back" id="back_btn" class="button button-primary button-large" /></a>
						<input type="submit" value="Validate OTP" class="button button-primary button-large" />
						</td>
						</form>
						<td><form method="post" action="" id="mo_wpns_cancel_form">
							<input type="hidden" name="option" value="mo_wpns_cancel" />
						</form></td></tr>
					<form name="f" id="resend_otp_form" method="post" action="">
							<td>
							<input type="hidden" name="option" value="mo_wpns_resend_otp"/>
							</td>
						</tr>
					</form>
				</table>
				<br>
				<hr>

				<h3>I did not recieve any email with OTP . What should I do ?</h3>
				<form id="phone_verification" method="post" action="">
					<input type="hidden" name="option" value="mo_wpns_phone_verification" />
					 If you can't see the email from miniOrange in your mails, please check your <b>SPAM Folder</b>. If you don't see an email even in SPAM folder, verify your identity with our alternate method.
					 <br><br>
						<b>Enter your valid phone number here and verify your identity using one time passcode sent to your phone.</b><br><br><input class="mo_wpns_table_textbox" required="true" pattern="[\+]\d{1,3}\d{10}" autofocus="true" type="text" name="phone_number" id="phone" placeholder="Enter Phone Number" style="width:40%;" value="<?php echo get_option('mo_wpns_admin_phone');  ?>" title="Enter phone number without any space or dashes."/>
						<br><input type="submit" value="Send OTP" class="button button-primary button-large" />
				
				</form>
			</div>
		</div>
		<script>
	jQuery("#phone").intlTelInput();
	jQuery('#back_btn').click(function(){
			jQuery('#mo_wpns_cancel_form').submit();
	});
	
</script>
<?php
}
/* End Show OTP verification page*/



?>