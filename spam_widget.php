<?php
    /*
    Plugin Name: miniOrange Spam Protection
    Plugin URI: http://miniorange.com
    Description: Spam Protection and Anti Spam against comments, registrations, login and content protection.
    Author: miniorange
    Version: 2.5.2
    Author URI: http://miniorange.com
    */

	require_once 'mo_wpns_pages.php';
	require('mo_wpns_support.php');
	require('class-mo-wpns-customer-setup.php');
	require('class-mo-wpns-utility.php');
	require('mo-wpns-handler.php');
	require('mo-wpns-secure-login-handler.php');
	require('mo-wpns-recaptcha-handler.php');
	require('mo-wpns-backup-handler.php');
	require('mo-wpns-htaccess-handler.php');
	require('resources/constants.php');
	require('resources/messages.php');
	require('resources/domains.php');
	
	class miniOrange_Spam_Protection{

		function __construct(){
			add_action('admin_menu', array($this, 'mo_wpns_widget_menu'));
			add_action('admin_init', array($this, 'mo_wpns_widget_save_options'));
			add_action('init', array($this, 'mo_wpns_init'));
			add_action( 'admin_enqueue_scripts', array( $this, 'mo_wpns_settings_style' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'mo_wpns_settings_script' ) );

			remove_action( 'admin_notices', array( $this, 'success_message') );
			remove_action( 'admin_notices', array( $this, 'error_message') );
			add_filter('query_vars', array($this, 'plugin_query_vars'));
			register_deactivation_hook(__FILE__, array( $this, 'mo_wpns_deactivate'));
			register_activation_hook( __FILE__, array($this,'mo_wpns_activate')) ;
			if(get_option('mo_wpns_activate_recaptcha_for_bbpress')){
				add_action('bbp_theme_before_reply_form_submit_wrapper', array($this,'custom_login_fields'));
				add_action('bbp_new_reply_pre_extras', array($this,'bbp_newrecaptcha_verify_result'));
			}
			if(get_option('mo_wpns_activate_recaptcha_for_login')){
				add_action('login_form',array($this,'custom_login_fields'));
			}
			add_action('register_form', array($this,'register_with_captcha'));
			add_filter( 'registration_errors', array($this,'mo_wpns_registeration_validations'), 10, 3 );
			if(get_option('mo_wpns_activate_recaptcha_for_login')){
				remove_filter('authenticate', 'wp_authenticate_username_password', 20);
				add_filter('authenticate', array($this, 'custom_authenticate'), 999999, 3);
			}
			if(get_option('mo_wpns_enable_comment_spam_blocking')){
				add_filter( 'preprocess_comment', array($this, 'comment_spam_check') );
				add_action( 'comment_form_after_fields', array($this, 'comment_spam_custom_field') );
			}
			if(get_option('disable_file_editing'))
				define('DISALLOW_FILE_EDIT', true);
		}
		
		function mo_wpns_login_redirect(){
			if (!is_user_logged_in()) 
				auth_redirect();
		}

		function mo_wpns_widget_menu(){
			add_menu_page ('miniOrange Spam Protection', 'miniOrange Spam Protection', 'activate_plugins', 'miniorange_spam_protection', array( $this, 'mo_wpns_widget_options'),plugin_dir_url(__FILE__) . 'includes/images/miniorange_icon.png');
		}

		function mo_wpns_widget_options(){
			update_option( 'mo_wpns_host_name', 'https://login.xecurify.com' );
			mo_msp_show_settings();
		}

		function mo_wpns_widget_save_options(){
			if  ( isset( $_POST['mo2fa_register_to_upgrade_nonce'] ) ) { //registration with miniOrange for upgrading
				$nonce = $_POST['mo2fa_register_to_upgrade_nonce'];
				if ( ! wp_verify_nonce( $nonce, 'miniorange-2-factor-user-reg-to-upgrade-nonce' ) ) {
					update_option( 'mo2f_message', Mo2fConstants:: langTranslate( "INVALID_REQ" ) );
				} else {
					$requestOrigin = $_POST['requestOrigin'];
					update_option( 'mo2f_customer_selected_plan', $requestOrigin );
					header( 'Location: admin.php?page=miniorange_spam_protection&tab=account' );
	
				}
			}else if(isset($_POST['option']) && current_user_can('manage_options')){
				if($_POST['option'] == "mo_wpns_register_customer") {		//register the customer
					//validate and sanitize
					$email = '';
					$phone = '';
					$password = '';
					$confirmPassword = '';
					if( Mo_MSP_Util::check_empty_or_null( $_POST['email'] ) || Mo_MSP_Util::check_empty_or_null( $_POST['password'] ) || Mo_MSP_Util::check_empty_or_null( $_POST['confirmPassword'] ) ) {
						update_option( 'mo_wpns_message', 'All the fields are required. Please enter valid entries.');
						$this->show_error_message();
						return;
					} else if( strlen( $_POST['password'] ) < 6 || strlen( $_POST['confirmPassword'] ) < 6){	//check password is of minimum length 6
						update_option( 'mo_wpns_message', 'Choose a password with minimum length 6.');
						$this->show_error_message();
						return;
					} else{
						$email = sanitize_email( $_POST['email'] );
						$phone = sanitize_text_field( $_POST['phone'] );
						$password = sanitize_text_field( $_POST['password'] );
						$confirmPassword = sanitize_text_field( $_POST['confirmPassword'] );
					}

					update_option( 'mo_wpns_admin_email', $email );
					if($phone != '')
						update_option( 'mo_wpns_admin_phone', $phone );

					if( strcmp( $password, $confirmPassword) == 0 ) {
						update_option( 'mo_wpns_password', $password );

						$customer = new Mo_MSP_Customer();
						$content = json_decode($customer->check_customer(), true);
						if( strcasecmp( $content['status'], 'CUSTOMER_NOT_FOUND') == 0 ){
							$auth_type = 'EMAIL';
							$content = json_decode($customer->send_otp_token($auth_type, null), true);
							if(strcasecmp($content['status'], 'SUCCESS') == 0) {
								
								update_option('mo_wpns_email_count',1);
								update_option( 'mo_wpns_message', 'A One Time Passcode has been sent to <b>' . ( get_option('mo_wpns_admin_email') ) . '</b>. Please enter the OTP below to verify your email. ');
								
								update_option('mo_wpns_transactionId',$content['txId']);
								update_option('mo_wpns_registration_status','MO_OTP_DELIVERED_SUCCESS');

								$this->show_success_message();
							} else {
								update_option('mo_wpns_message','There was an error in sending email. Please click on Resend OTP to try again.');
								update_option('mo_wpns_registration_status','MO_OTP_DELIVERED_FAILURE');
								$this->show_error_message();
							}
						} else if( strcasecmp( $content['status'], 'CURL_ERROR') == 0 ){
							update_option('mo_wpns_message', $content['statusMessage']);
							update_option('mo_wpns_registration_status','MO_OTP_DELIVERED_FAILURE');
							$this->show_error_message();
						} else{
							$content = $customer->get_customer_key();
							$customerKey = json_decode($content, true);
							if(json_last_error() == JSON_ERROR_NONE) {
								$this->save_success_customer_config($customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['appSecret'],'Your account has been retrieved successfully.');
								update_option('mo_wpns_password', '');
							} else {
								update_option( 'mo_wpns_message', 'You already have an account with miniOrange. Please enter a valid password.');
								update_option('mo_wpns_verify_customer', 'true');
								delete_option('mo_wpns_new_registration');
								$this->show_error_message();
							}
						}

					} else {
						update_option( 'mo_wpns_message', 'Password and Confirm password do not match.');
						delete_option('mo_wpns_verify_customer');
						$this->show_error_message();
					}
				}else if( $_POST['option'] == "mo_wpns_verify_customer" ) {	//login the admin to miniOrange

					//validation and sanitization
					$email = '';
					$password = '';
					if( Mo_MSP_Util::check_empty_or_null( $_POST['email'] ) || Mo_MSP_Util::check_empty_or_null( $_POST['password'] ) ) {
						update_option( 'mo_wpns_message', 'All the fields are required. Please enter valid entries.');
						$this->show_error_message();
						return;
					} else{
						$email = sanitize_email( $_POST['email'] );
						$password = sanitize_text_field( $_POST['password'] );
					}

					update_option( 'mo_wpns_admin_email', $email );
					update_option( 'mo_wpns_password', $password );
					$customer = new Mo_MSP_Customer();
					$content = $customer->get_customer_key();
					$customerKey = json_decode( $content, true );
					if( strcasecmp( $customerKey['apiKey'], 'CURL_ERROR') == 0) {
						update_option('mo_wpns_message', $customerKey['token']);
						$this->show_error_message();
					} else if( json_last_error() == JSON_ERROR_NONE ) {
						update_option( 'mo_wpns_admin_phone', $customerKey['phone'] );
						$this->save_success_customer_config($customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['appSecret'], 'Your account has been retrieved successfully.');
						update_option('mo_wpns_password', '');
					} else {
						update_option( 'mo_wpns_message', 'Invalid username or password. Please try again.');
						$this->show_error_message();
					}
					update_option('mo_wpns_password', '');
				}   else if( $_POST['option'] == "mo_wpns_validate_otp"){		//verify OTP entered by user

					//validation and sanitization
					$otp_token = '';
					if( Mo_MSP_Util::check_empty_or_null( $_POST['otp_token'] ) ) {
						update_option( 'mo_wpns_message', 'Please enter a value in otp field.');
						update_option('mo_wpns_registration_status','MO_OTP_VALIDATION_FAILURE');
						$this->show_error_message();
						return;
					} else{
						$otp_token = sanitize_text_field( $_POST['otp_token'] );
					}

					$customer = new Mo_MSP_Customer();
					$content = json_decode($customer->validate_otp_token(get_option('mo_wpns_transactionId'), $otp_token ),true);
					if(strcasecmp($content['status'], 'SUCCESS') == 0) {
						$customerKey = json_decode($customer->create_customer(), true);
						if(strcasecmp($customerKey['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS') == 0) {	//admin already exists in miniOrange
							$content = $customer->get_customer_key();
							$customerKey = json_decode($content, true);
							if(json_last_error() == JSON_ERROR_NONE) {
								$this->save_success_customer_config($customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['appSecret'], 'Your account has been retrieved successfully.');
							} else {
								update_option( 'mo_wpns_message', 'You already have an account with miniOrange. Please enter a valid password.');
								update_option('mo_wpns_verify_customer', 'true');
								delete_option('mo_wpns_new_registration');
								$this->show_error_message();
							}
						} else if(strcasecmp($customerKey['status'], 'SUCCESS') == 0) { 	//registration successful
							$this->save_success_customer_config($customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['appSecret'], 'Registration complete!');
						}
						update_option('mo_wpns_password', '');
					} else if( strcasecmp( $content['status'], 'CURL_ERROR') == 0) {
						update_option('mo_wpns_message', $content['statusMessage']);
						update_option('mo_wpns_registration_status','MO_OTP_VALIDATION_FAILURE');
						$this->show_error_message();
					} else{
						update_option( 'mo_wpns_message','Invalid one time passcode. Please enter a valid otp.');
						update_option('mo_wpns_registration_status','MO_OTP_VALIDATION_FAILURE');
						$this->show_error_message();
					}
				} else if( $_POST['option'] == "mo_wpns_resend_otp" ) {			//send OTP to user to verify email
					$customer = new Mo_MSP_Customer();
					$auth_type = 'EMAIL';
					$content = json_decode($customer->send_otp_token($auth_type, null), true);
					if(strcasecmp($content['status'], 'SUCCESS') == 0) {
							update_option( 'mo_wpns_message', ' A one time passcode is sent to ' . get_option('mo_wpns_admin_email') . ' again. Please enter the OTP recieved.');
							update_option('mo_wpns_transactionId',$content['txId']);
							update_option('mo_wpns_registration_status','MO_OTP_DELIVERED_SUCCESS');
							$this->show_success_message();
					} else if( strcasecmp( $content['status'], 'CURL_ERROR') == 0) {
						update_option('mo_wpns_message', $content['statusMessage']);
						update_option('mo_wpns_registration_status','MO_OTP_DELIVERED_FAILURE');
						$this->show_error_message();
					} else{
							update_option('mo_wpns_message','There was an error in sending email. Please click on Resend OTP to try again.');
							update_option('mo_wpns_registration_status','MO_OTP_DELIVERED_FAILURE');
							$this->show_error_message();
					}
				} else if($_POST['option'] == 'mo_wpns_phone_verification'){
					$phone = sanitize_text_field($_POST['phone_number']);
					$phone = str_replace(' ', '', $phone);
					
					$pattern = "/[\+][0-9]{1,3}[0-9]{10}/";					
					
					if(preg_match($pattern, $phone, $matches, PREG_OFFSET_CAPTURE)){
						$auth_type = 'SMS';
						$customer = new Mo_MSP_Customer();
						$content = json_decode($customer->send_otp_token($auth_type, $phone), true);
						if(strcasecmp($content['status'], 'SUCCESS') == 0) {
								update_option('mo_wpns_message', 'One Time Passcode has been sent for verification to ' . $phone);
								update_option('mo_wpns_transactionId',$content['txId']);
								$this->show_success_message();
						}
					}else{
						update_option('mo_wpns_message', 'Please enter the phone number in the following format: <b>+##country code## ##phone number##');
						$this->show_error_message();
					}
				} else if($_POST['option'] == "mo_wpns_registeration_back"){
					delete_option('mo_wpns_registration_status');
				} else if($_POST['option'] == 'mo_wpns_cancel'){
					delete_option('mo_wpns_admin_email');
					delete_option('mo_wpns_registration_status');
					delete_option('mo_wpns_verify_customer');
				} else if($_POST['option'] == 'mo_wpns_user_forgot_password'){
					$admin_email = get_option('mo_wpns_admin_email');
					$customer = new Mo_MSP_Customer();
					$forgot_password_response = json_decode($customer->mo_wpns_forgot_password($admin_email));
					if($forgot_password_response->status == 'SUCCESS'){
						$message = 'You password has been reset successfully. Please enter the new password sent to your registered mail here.';
						update_option('mo_wpns_message', $message);
						$this->show_success_message();
					}
				}  else if($_POST['option'] == "mo_wpns_content_protection"){
					isset($_POST['protect_wp_config']) ? update_option('protect_wp_config', $_POST['protect_wp_config']): update_option('protect_wp_config',0);
					isset($_POST['prevent_directory_browsing']) ? update_option('prevent_directory_browsing', $_POST['prevent_directory_browsing']): update_option('prevent_directory_browsing',0);
					isset($_POST['disable_file_editing']) ? update_option('disable_file_editing', $_POST['disable_file_editing']): update_option('disable_file_editing',0);
					$mo_wpns_htaccess_handler = new Mo_MSP_Htaccess_Handler();
					$mo_wpns_htaccess_handler->update_htaccess_configuration();
					update_option( 'mo_wpns_message', 'Your configuration for Content Protection has been saved.');
					$this->show_success_message();
				} else if($_POST['option'] == "mo_wpns_activate_recaptcha"){
					$mo_wpns_activate_recaptcha = false;
					if(isset($_POST['mo_wpns_activate_recaptcha'])  && $_POST['mo_wpns_activate_recaptcha']){
						$mo_wpns_activate_recaptcha = true;
						update_option( 'mo_wpns_message', 'Google reCAPTCHA is enabled.');
						$this->show_success_message();
					}else {
						update_option('mo_wpns_activate_recaptcha_for_login',0);
						update_option('mo_wpns_activate_recaptcha_for_registration',0);
						update_option('mo_wpns_activate_recaptcha_for_bbpress',0);
						update_option( 'mo_wpns_message', 'Google reCAPTCHA is disabled.');
						$this->show_error_message();
					}
					update_option( 'mo_wpns_activate_recaptcha', $mo_wpns_activate_recaptcha);
				}  else if($_POST['option'] == "mo_wpns_recaptcha_settings"){
					update_option('mo_wpns_recaptcha_site_key', $_POST['mo_wpns_recaptcha_site_key']);
					update_option('mo_wpns_recaptcha_secret_key', $_POST['mo_wpns_recaptcha_secret_key']);
					isset($_POST['mo_wpns_activate_recaptcha_for_login']) ? update_option('mo_wpns_activate_recaptcha_for_login', $_POST['mo_wpns_activate_recaptcha_for_login']): update_option('mo_wpns_activate_recaptcha_for_login',0);
					isset($_POST['mo_wpns_activate_recaptcha_for_registration']) ? update_option('mo_wpns_activate_recaptcha_for_registration', $_POST['mo_wpns_activate_recaptcha_for_registration']): update_option('mo_wpns_activate_recaptcha_for_registration',0);
					isset($_POST['mo_wpns_activate_recaptcha_for_bbpress']) ? update_option('mo_wpns_activate_recaptcha_for_bbpress', $_POST['mo_wpns_activate_recaptcha_for_bbpress']): update_option('mo_wpns_activate_recaptcha_for_bbpress',0);
					update_option( 'mo_wpns_message', 'Google reCAPTCHA configuration is saved.');
					$this->show_success_message();
				} else if ($_POST['option'] == "custom_user_template"){
					if(isset($_POST['custom_user_template'])){
						update_option('custom_user_template', stripslashes($_POST['custom_user_template']));
						update_option( 'mo_wpns_message', 'Your template has been saved.');
						$this->show_success_message();
					}
				} else if($_POST['option'] == "mo_wpns_send_query"){
					$query = '';
					if( Mo_MSP_Util::check_empty_or_null( $_POST['query_email'] ) || Mo_MSP_Util::check_empty_or_null( $_POST['query'] ) ) {
						update_option( 'mo_wpns_message', 'Please submit your query along with email.');
						$this->show_error_message();
						return;
					} else{
						$query = sanitize_text_field( $_POST['query'] );
						$email = sanitize_text_field( $_POST['query_email'] );
						$phone = sanitize_text_field( $_POST['query_phone'] );
						$contact_us = new Mo_MSP_Customer();
						$submited = json_decode($contact_us->submit_contact_us($email, $phone, $query),true);

						if( strcasecmp( $submited['status'], 'CURL_ERROR') == 0) {
							update_option('mo_wpns_message', $submited['statusMessage']);
							$this->show_error_message();
						} else if(json_last_error() == JSON_ERROR_NONE) {
							if ( $submited == false ) {
								update_option('mo_wpns_message', 'Your query could not be submitted. Please try again.');
								$this->show_error_message();
							} else {
								update_option('mo_wpns_message', 'Thanks for getting in touch! We shall get back to you shortly.');
								$this->show_success_message();
							}
						}

					}
				} else if($_POST['option'] == "mo_wpns_block_referrer"){
					$referrers = "";
					foreach($_POST as $key => $value){
						if(strpos($key, 'referrer_') !== false){
							if(!empty($value))
								$referrers .= $value.";";
						}
					}
					update_option( 'mo_wpns_referrers', $referrers);
				} else if($_POST['option'] == "mo_wpns_enable_fake_domain_blocking"){
					$enable_fake_emails = false;
					if(isset($_POST['mo_wpns_enable_fake_domain_blocking'])  && $_POST['mo_wpns_enable_fake_domain_blocking']){
						$enable_fake_emails = $_POST['mo_wpns_enable_fake_domain_blocking'];
						update_option( 'mo_wpns_message', 'Blocking fake user registerations is Enabled.');
						$this->show_success_message();
					}else {
						update_option( 'mo_wpns_message', 'Blocking fake user registerations is Disabled.');
						$this->show_error_message();
					}
					update_option( 'mo_wpns_enable_fake_domain_blocking', $enable_fake_emails);
				} else if($_POST['option'] == "mo_msp_content_protection"){
					isset($_POST['protect_wp_config']) ? update_option('protect_wp_config', $_POST['protect_wp_config']): update_option('protect_wp_config',0);
					isset($_POST['prevent_directory_browsing']) ? update_option('prevent_directory_browsing', $_POST['prevent_directory_browsing']): update_option('prevent_directory_browsing',0);
					isset($_POST['disable_file_editing']) ? update_option('disable_file_editing', $_POST['disable_file_editing']): update_option('disable_file_editing',0);
					$mo_wpns_htaccess_handler = new Mo_MSP_Htaccess_Handler();
					$mo_wpns_htaccess_handler->update_htaccess_configuration();
					update_option( 'mo_wpns_message', 'Your configuration for Content Protection has been saved.');
					$this->show_success_message();
				} else if($_POST['option'] == "mo_wpns_enable_comment_spam_blocking"){
					isset($_POST['mo_wpns_enable_comment_spam_blocking']) ? update_option('mo_wpns_enable_comment_spam_blocking', $_POST['mo_wpns_enable_comment_spam_blocking']): update_option('mo_wpns_enable_comment_spam_blocking',0);
					update_option( 'mo_wpns_message', 'Your configuration for Comment SPAM has been saved.');
					$this->show_success_message();
				} else if($_POST['option'] == "mo_wpns_db_backup"){
					$mo_wpns_backup_handler = new Mo_MSP_Backup_Handler();
					$filename = $mo_wpns_backup_handler->backup_db();
					if(isset($_POST['backup']) && $_POST['backup']=="download"){
						header('Location: '.get_site_url().'/db-backups/'.$filename);
					}else{
						update_option( 'mo_wpns_message', 'Your backup is ready and saved in <b>'.$filename.'</b> file under <b>db-backups</b> folder');
						$this->show_success_message();
					}
				} else if($_POST['option'] == "mo_wpns_enable_comment_recaptcha"){
					isset($_POST['mo_wpns_enable_comment_recaptcha']) ? update_option('mo_wpns_enable_comment_recaptcha', $_POST['mo_wpns_enable_comment_recaptcha']): update_option('mo_wpns_enable_comment_recaptcha',0);
					update_option( 'mo_wpns_message', 'Your configuration for Comment SPAM has been saved.');
					$this->show_success_message();
				} else if($_POST['option'] == "mo_wpns_comment_recaptcha_settings"){
					update_option('mo_wpns_recaptcha_site_key', $_POST['mo_wpns_recaptcha_site_key']);
					update_option('mo_wpns_recaptcha_secret_key', $_POST['mo_wpns_recaptcha_secret_key']);
					update_option( 'mo_wpns_message', 'Google reCAPTCHA configuration is saved.');
					$this->show_success_message();
				} else if($_POST['option'] == "mo_wpns_enable_htaccess_blocking"){
					if(isset($_POST['mo_wpns_enable_htaccess_blocking']))
						update_option( 'mo_wpns_enable_htaccess_blocking',$_POST['mo_wpns_enable_htaccess_blocking']);
					else
						update_option( 'mo_wpns_enable_htaccess_blocking',0);
					if(get_option( 'mo_wpns_enable_htaccess_blocking')){
						$mo_wpns_config = new Mo_MSP_Handler();
						$mo_wpns_config->add_htaccess_ips();
						update_option( 'mo_wpns_message', 'htaccess level security is Enabled.');
						$this->show_success_message();
					}else {
						$mo_wpns_config = new Mo_MSP_Handler();
						$mo_wpns_config->remove_htaccess_ips();
						update_option( 'mo_wpns_message', 'htaccess level security is Disabled.');
						$this->show_error_message();
					}
				} else if($_POST['option'] == 'mo_wpns_reset_password'){
					$admin_email = get_option('mo_wpns_admin_email');
					$customer = new Mo_MSP_Customer();
					$forgot_password_response = json_decode($customer->mo_wpns_forgot_password($admin_email));
					if($forgot_password_response->status == 'SUCCESS'){
						$message = 'You password has been reset successfully and sent to your registered email. Please check your mailbox.';
						update_option('mo_wpns_message', $message);
						$this->show_success_message();
					}
				}				
			}
	
		}
		
		function mo_wpns_init(){


			if(isset($_POST['option']) && $_POST['option']=="mo_wpns_change_password"){
					$secure_login_handler = new Mo_MSP_Secure_Login_Handler();
					$username = $_POST['username'];
					if($secure_login_handler->update_strong_passwords($username,$_POST['password'],$_POST['new_password'],$_POST['confirm_password'])=="success"){
						$user = get_user_by("login",$username);
						//$user = wp_signon( $creds, false );
						wp_set_auth_cookie($user->ID,false,false);
						$this->mo_wpns_login_success($username);
						wp_redirect(get_option('siteurl') . '/wp-admin/index.php',301);
					} else 
						exit();
			}
			
			if(isset($_REQUEST['option']) && $_REQUEST['option'] == 'testrecaptchaconfig'){
				$mo_wpns_recaptcha_handler = new Mo_MSP_Recaptcha_Handler();
				$mo_wpns_recaptcha_handler->test_configuration();
			} 
		}
		
		function custom_login_fields(){ if( ! is_user_logged_in()){?>
			<script src='https://www.google.com/recaptcha/api.js'></script>
			<div class="g-recaptcha" data-sitekey="<?php echo get_option('mo_wpns_recaptcha_site_key');?>"></div>
		<?php }}
		
		function register_with_captcha(){
			if(get_option('mo_wpns_activate_recaptcha_for_registration')){ ?>
				<script src='https://www.google.com/recaptcha/api.js'></script>
				<div class="g-recaptcha" data-sitekey="<?php echo get_option('mo_wpns_recaptcha_site_key');?>"></div>
			<?php }
		}
		
		function mo_wpns_registeration_validations( $errors, $sanitized_user_login, $user_email ) {
			
			if(Mo_MSP_Blacklisted_Domains::check_if_valid_email($user_email))
				$errors->add( 'blocked_email_error', __( '<strong>ERROR</strong>: Your email address is not allowed to register. Please select different email address.') );
			
			if(get_option('mo_wpns_activate_recaptcha_for_registration')){
				$mo_wpns_recaptcha_handler = new Mo_MSP_Recaptcha_Handler();
				if(!$mo_wpns_recaptcha_handler->verify())
					$errors->add('recaptcha_error', __( '<strong>ERROR</strong> : Invalid captcha. Please verify captcha again.'));
			}
			return $errors;
		}

		function bbp_newrecaptcha_verify_result() {
			
			$bbp_newrecaptcha_options = get_option('bbp_newrecaptcha');
			if( is_user_logged_in() && !isset( $bbp_newrecaptcha_options['show_to_logged_in'] ) )
				return;
			if(get_option('mo_wpns_activate_recaptcha_for_bbpress')){	
				$mo_wpns_recaptcha_handler = new Mo_MSP_Recaptcha_Handler();
				if(!$mo_wpns_recaptcha_handler->verify())	
				bbp_add_error( 'bbp_reply_duplicate', __( '<strong>ERROR</strong>: reCAPTCHA Failure. Please try again.', 'bbpress-newrecaptcha' ) );
			}
		}
		function custom_authenticate($user, $username, $password){
		
			if(empty($username) && empty ($password)){
				//bug here...call hook on page load
				$error = new WP_Error();
				return $error;
			} else if(empty($username) || empty ($password)){
				$error = new WP_Error();
				if(empty($username)){ //No email
					$error->add('empty_username', __('<strong>ERROR</strong>: Username field is empty.'));
				}
				if(empty($password)){ //No password
					$error->add('empty_password', __('<strong>ERROR</strong>: Password field is empty.'));
				}
				$this->mo_wpns_login_failed($username);
				return $error;
			}
			
			$user = get_user_by("login",$username);
			if (!$user) $user= get_user_by("email",$username);
			$error = new WP_Error();
			if($user){
				if ( $user && wp_check_password( $password, $user->data->user_pass, $user->ID) ){
					if(get_option('mo_wpns_activate_recaptcha_for_login')){
						$mo_wpns_recaptcha_handler = new Mo_MSP_Recaptcha_Handler();
						if(!$mo_wpns_recaptcha_handler->verify()){							
							$error->add('recaptcha_error', __( '<strong>ERROR</strong> : Invalid captcha. Please verify captcha again.'));
							$this->mo_wpns_login_failed($username);
							return $error;
						}else
							return $user;
					}
				} else
					$error->add('empty_password', __('<strong>ERROR</strong>: Wrong password.'));
			} else

				$error->add('empty_password', __('<strong>ERROR</strong>: User does not exist.'));
			$this->mo_wpns_login_failed($username);
			return $error;
		}

		/*
		 * Save all required fields on customer registration/retrieval complete.
		 */
		function save_success_customer_config($id, $apiKey, $token, $appSecret, $message) {
			update_option( 'mo_wpns_admin_customer_key', $id );
			update_option( 'mo_wpns_admin_api_key', $apiKey );
			update_option( 'mo_wpns_customer_token', $token );
			update_option( 'mo_wpns_app_secret', $appSecret );
			update_option( 'mo_wpns_enable_log_requests', true);
			update_option('mo_wpns_password', '');
			update_option( 'mo_wpns_message', $message);
			delete_option('mo_wpns_verify_customer');
			delete_option('mo_wpns_registration_status');
			$this->show_success_message();
		}

		function mo_wpns_login_failed($username){
			
			if(!get_option('mo_wpns_enable_brute_force'))
				return;
			
			$userIp = Mo_MSP_Util::get_client_ip();
			if(empty($userIp))
				return;
			else if(empty($username))
				return;
				
			$mo_wpns_config = new Mo_MSP_Handler();
			$mo_wpns_config->add_transactions($userIp, $username, Mo_MSP_Constants::LOGIN_TRANSACTION, Mo_MSP_Constants::FAILED);
			
			$isWhitelisted = $mo_wpns_config->is_whitelisted($userIp);
			if(!$isWhitelisted){
				$failedAttempts = $mo_wpns_config->get_failed_attempts_count($userIp);
				
				//Slow Down
				if(get_option('mo_wpns_slow_down_attacks')){
					session_start();
					if(isset($_SESSION["mo_wpns_failed_attepmts"]) && is_numeric($_SESSION["mo_wpns_failed_attepmts"]))
						$_SESSION["mo_wpns_failed_attepmts"] += 1;
					else
						$_SESSION["mo_wpns_failed_attepmts"] = 1;
					$mo_wpns_slow_down_attacks_delay = 2;
					if(get_option('mo_wpns_slow_down_attacks_delay'))
						$mo_wpns_slow_down_attacks_delay = get_option('mo_wpns_slow_down_attacks_delay');
					sleep($_SESSION["mo_wpns_failed_attepmts"]*$mo_wpns_slow_down_attacks_delay);
				}
					
				
				$allowedLoginAttepts = 5;
				if(get_option('mo_wpns_allwed_login_attempts'))
					$allowedLoginAttepts = get_option('mo_wpns_allwed_login_attempts');
				
				if(get_option('mo_wpns_enable_unusual_activity_email_to_user'))
						$mo_wpns_config->sendNotificationToUserForUnusualActivities($username, $userIp, Mo_MSP_Messages::FAILED_LOGIN_ATTEMPTS_FROM_NEW_IP);
					
				if($allowedLoginAttepts - $failedAttempts<=0){
					$mo_wpns_config->block_ip($userIp, Mo_MSP_Messages::LOGIN_ATTEMPTS_EXCEEDED, false);
					if(get_option('mo_wpns_enable_ip_blocked_email_to_admin'))
						$mo_wpns_config->sendIpBlockedNotification($userIp,Mo_MSP_Messages::LOGIN_ATTEMPTS_EXCEEDED);
					require_once 'templates/403.php';
					exit();
				}else {
					if(get_option('mo_wpns_show_remaining_attempts')){
						global $error;
						$diff = $allowedLoginAttepts - $failedAttempts;
						$error = "<br>You have <b>".$diff."</b> login attempts remaining.";
					}
				}
			}
			
		}
		function mo_wpns_login_success($username){
			
			$mo_wpns_config = new Mo_MSP_Handler();
			$userIp = Mo_MSP_Util::get_client_ip();
			if(get_option('mo_wpns_enable_unusual_activity_email_to_user'))
				$mo_wpns_config->sendNotificationToUserForUnusualActivities($username, $userIp, Mo_MSP_Messages::LOGGED_IN_FROM_NEW_IP);
					
			if(!get_option('mo_wpns_enable_brute_force'))
				return;
			$mo_wpns_config->move_failed_transactions_to_past_failed($userIp);
			$mo_wpns_config->add_transactions($userIp, $username, Mo_MSP_Constants::LOGIN_TRANSACTION, Mo_MSP_Constants::SUCCESS);
		}
		
		function mo_wpns_settings_style() {
			wp_enqueue_style( 'mo_wpns_admin_settings_upgrade_style', plugins_url('includes/css/upgrade.css', __FILE__));
			wp_enqueue_style( 'mo_wpns_admin_settings_style', plugins_url('includes/css/style_settings.css', __FILE__));
			wp_enqueue_style( 'mo_wpns_admin_settings_phone_style', plugins_url('includes/css/phone.css', __FILE__));
			wp_enqueue_style( 'mo_wpns_admin_settings_datatable_style', plugins_url('includes/css/jquery.dataTables.min.css', __FILE__));
			
		}

		function mo_wpns_settings_script() {
			wp_enqueue_script( 'mo_wpns_admin_settings_phone_script', plugins_url('includes/js/phone.js', __FILE__ ));
			wp_enqueue_script( 'mo_wpns_admin_settings_script', plugins_url('includes/js/settings_page.js', __FILE__ ), array('jquery'));
			wp_enqueue_script( 'mo_wpns_admin_datatable_script', plugins_url('includes/js/jquery.dataTables.min.js', __FILE__ ), array('jquery'));
		}

		function error_message() {
			$class = "error";
			$message = get_option('mo_wpns_message');
			echo "<div class='" . $class . "'><p>" . $message . "</p></div>";
		}

		function success_message() {
			$class = "updated";
			$message = get_option('mo_wpns_message');
			echo "<div class='" . $class . "'><p>" . $message . "</p></div>";
		}

		function show_success_message() {
			remove_action( 'admin_notices', array( $this, 'error_message') );
			add_action( 'admin_notices', array( $this, 'success_message') );
		}

		function show_error_message() {
			remove_action( 'admin_notices', array( $this, 'success_message') );
			add_action( 'admin_notices', array( $this, 'error_message') );
		}

		function plugin_query_vars($vars) {
			$vars[] = 'app_name';
			return $vars;
		}

		function mo_wpns_activate() {
			$mo_wpns_config = new Mo_MSP_Handler();
			$mo_wpns_config->create_db();
		}
		
		function mo_wpns_deactivate() {
			//delete all stored key-value pairs
			if( !Mo_MSP_Util::check_empty_or_null( get_option('mo_wpns_registration_status') ) ) {
				delete_option('mo_wpns_admin_email');
			}
			delete_option('mo_wpns_admin_customer_key');
			delete_option('mo_wpns_admin_api_key');
			delete_option('mo_wpns_customer_token');
			delete_option('mo_wpns_message');
			delete_option('mo_wpns_transactionId');
			delete_option('mo_wpns_registration_status');
		}
		
		function comment_spam_check( $comment_data ) {
			if( isset($_POST['mocomment']) && !empty($_POST['mocomment']))
				wp_die( __( 'You are not authorised to perform this action.'));
			else if(get_option('mo_wpns_enable_comment_recaptcha')){
				$mo_wpns_recaptcha_handler = new Mo_MSP_Recaptcha_Handler();
				if(!$mo_wpns_recaptcha_handler->verify())
					wp_die( __( 'Invalid captcha. Please verify captcha again.'));
			}
			return $comment_data;
		}
		
		
		function comment_spam_custom_field(){
			echo '<input type="hidden" name="mocomment" />';
			if(get_option('mo_wpns_enable_comment_recaptcha')){ ?>
				<script src='https://www.google.com/recaptcha/api.js'></script>
				<div class="g-recaptcha" data-sitekey="<?php echo get_option('mo_wpns_recaptcha_site_key');?>"></div>
			<?php }
		}

	}

	new miniOrange_Spam_Protection;
?>