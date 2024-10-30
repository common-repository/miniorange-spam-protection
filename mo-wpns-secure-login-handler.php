<?php
/** Copyright (C) 2015  miniOrange

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>
* @package 		miniOrange OAuth
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*
**/

class Mo_MSP_Secure_Login_Handler{
	
	
	function enforce_strong_passwords($username,$password,$error){
		
		if(!$this->check_if_strong_password_enabled_for_user_role($username))
			return "success";
		else if(strlen($password) > 5 && preg_match("#[0-9]+#", $password) && preg_match("#[a-zA-Z]+#", $password) && preg_match('/[^a-zA-Z\d]/', $password)){
			return "success";
		} else {
		?>
		<html>
		<head>
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<?php
				echo '<link rel="stylesheet" type="text/css" href="' . plugins_url('includes/css/mo_customer_validation_style.css', __FILE__) . '" />';
				echo '<script src="' . plugins_url('includes/js/jquery-1.12.0.min.js', __FILE__) . '"></script>';
			?>
		</head>
		<body>
			<div class="mo-modal-backdrop">
				<div class="mo_customer_validation-modal" tabindex="-1" role="dialog" id="mo_site_otp_form">
					<div class="mo_customer_validation-modal-backdrop"></div>
					<div class="mo_customer_validation-modal-dialog mo_customer_validation-modal-md">
						<div class="login mo_customer_validation-modal-content">
							<div class="mo_customer_validation-modal-header">
								<h2>Strong password recommended</h2>
								<a class="close" href="#" onclick="window.location.href = window.location.protocol +'//'+ window.location.host + window.location.pathname;" ><?php printf( __( '&larr; Go Back' )); ?></a>
							 It's recommended for you to use strong password. Please update your password.
							</div>
							<div class="mo_customer_validation-modal-body center">
								<div style="color:red" id="error_message"><?php echo $error; ?></div><br /> 
								<?php if(!empty($username)){ ?>
								<div class="mo_customer_validation-login-container">
									<form name="f" method="post" action="" id="change_password_form">
										<input type='hidden' name="option" value='mo_wpns_change_password' />
										<input type="hidden" name="username" value="<?php echo $username;?>" />
										<input type="hidden" name="password" value="<?php echo $password; ?>" />
										<input type="password" name="new_password" id="new_password" class="mo_customer_validation-textbox" placeholder="New Password" />
										<input type="password" name="confirm_password" id="confirm_password" class="mo_customer_validation-textbox" placeholder="Confirm Password" />
										<input type="submit" name="miniorange_otp_token_submit" id="miniorange_otp_token_submit" class="miniorange_otp_token_submit"  value="Update Password" />
									</form>
								</div>
								<?php } else { ?>
									<script>
										window.location.href = window.location.protocol +'//'+ window.location.host + window.location.pathname;
									</script>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<style> .mo_customer_validation-modal{ display: block !important; } </style>
			<script>
				$('#change_password_form').submit(function(ev) {
					ev.preventDefault(); 
					
					var score   = 0;

					var txtpass = $("#new_password").val();
					var confirmPass = $("#confirm_password").val();
					if(txtpass!=confirmPass){
						$("#error_message").html("Both Passwords do not match.")
						return;
					}
					
					var errormessage = "<b>Please select strong password.</b><br>";
					if (txtpass.length > 5) score++;
					else errormessage += "<li>Password Should be Minimum 6 Characters</li>";
				
					if ( ( txtpass.match(/[a-z]/) ) && ( txtpass.match(/[A-Z]/) ) ) score++;
					else errormessage += "<li>Password should contain atleast one Capital Letter.</li>";
					
					if (txtpass.match(/\d+/)) score++;
					else errormessage += "<li>Password should contain atleast one Numeric Character.</li>";
						
					if ( txtpass.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/) ) score++;
					else errormessage += "<li>Password should contain atleast one Special Character (!,@,#,$,%,^,&,*,?,_,~,-) .</li>";
						
					if (txtpass.length < 6) {
						$("#error_message").html("Password Should be Minimum 6 Characters")
						return;
					} else if (score < 4) {
						$("#error_message").html(errormessage);
						return;
					} else
						this.submit();
				});
			</script>
		</body>
    </html>
	
	<?php	
	} 
	
  }
  
  
	function update_strong_passwords($username,$password,$newpassword,$confirmpassword){
		
		if($newpassword != $confirmpassword)
			$this->enforce_strong_passwords($username,$password,"Both Passwords do not match.");
		else if(strlen($newpassword) > 5 && preg_match("#[0-9]+#", $newpassword) && preg_match("#[a-zA-Z]+#", $newpassword) && preg_match('/[^a-zA-Z\d]/', $newpassword)){
			$user = get_user_by("login",$username);
			if ( $user && wp_check_password( $password, $user->data->user_pass, $user->ID ) ) {
				wp_set_password($_POST['new_password'],$user->ID);
				return "success";
			} else 
				wp_redirect(get_option('siteurl') . '/wp-admin/index.php',301);
		} else
			$this->enforce_strong_passwords($username,$password,"Please select strong password.");
	}

	function check_if_strong_password_enabled_for_user_role($username){
		
		//Check if strong password enable for user
		$mo_wpns_enforce_strong_passswords_for_accounts = get_option('mo_wpns_enforce_strong_passswords_for_accounts');
		if($mo_wpns_enforce_strong_passswords_for_accounts){
			if($mo_wpns_enforce_strong_passswords_for_accounts=="all")
				return true;
			
			$user = get_user_by("login",$username);
			$usermeta = get_userdata( $user->ID );
			$userroles = $usermeta->roles;
	
			if($mo_wpns_enforce_strong_passswords_for_accounts=="admin" && !in_array("administrator",$userroles))
				return false;
			else if($mo_wpns_enforce_strong_passswords_for_accounts=="user" && in_array("administrator",$userroles))
				return false;
		}
		return true;
	}
	
} ?>