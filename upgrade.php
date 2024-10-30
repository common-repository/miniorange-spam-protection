<?php
global $mainDir;

function mo2f_lt( $string ) {
	return __($string ,'push-authenticator' );
}
const FAQ_PAYMENT_URL					= 'https://faq.miniorange.com/knowledgebase/all-i-want-to-do-is-upgrade-to-a-premium-licence/';
const SUPPORT_EMAIL						= 'info@xecurify.com';
const MO_HOST_NAME 						='https://login.xecurify.com';


	$user = wp_get_current_user();
	$currentUserId = $user->ID;	
	$is_customer_registered = Mo_MSP_Util::is_customer_registered();
?>

<br><br>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous"/>
<div class="mo2f_upgrade_super_div" id="mo2f_twofa_plans">
<div id="mo2fa_compare">
<div id="mo_ns_features_only"">
	<div class="mo_wpns_upgrade_security_title" >
		<div class="mo_wpns_upgrade_page_title_name">
			<h1 style="margin-top: 0%;padding: 10% 0% 0% 0%; color: white;font-size: 200%;">
		WAF</h1><hr class="mo_wpns_upgrade_page_hr"></div>
		
	<div class="mo_wpns_upgrade_page_ns_background">
			<center>
			<h4 class="mo_wpns_upgrade_page_starting_price">Starting From</h4>
			<h1 class="mo_wpns_upgrade_pade_pricing">$50</h1>
			
				<?php echo mo2f_waf_yearly_standard_pricing(); ?>
				
				
			</center>
	
	<div style="text-align: center;">
	<?php	
	if(isset($is_customer_registered) && $is_customer_registered) {
			?>
	            <button
	                        class="button button-primary button-large mo_wpns_upgrade_page_button"
	                        onclick="mo2f_upgradeform('wp_security_waf_plan','2fa_plan')">Upgrade</button>
	        <?php }

			
	        else{ ?>
				<button
	                        class="button button-primary button-large mo_wpns_upgrade_page_button"
	                        onclick="mo2f_register_and_upgradeform('wp_security_waf_plan','2fa_plan')">Upgrade</button>
	        <?php } 
	        ?>
	</div>
			<div><center><b>
		<ul>
			<li>Realtime IP Blocking</li>
			<li>Live Traffic and Audit</li>
			<li>IP Blocking and Whitelisting</li>
			<li>OWASP TOP 10 Firewall Rules</li>
			<li>Standard Rate Limiting/ DOS Protection</li>
			<li><a onclick="wpns_pricing()">Know more</a></li>
		</ul>
		</b></center></div>
	</div>
	</div>
	<div class="mo_wpns_upgrade_page_space_in_div"></div>
	<div class="mo_wpns_upgrade_security_title" >
		<div class="mo_wpns_upgrade_page_title_name">
			<h1 style="margin-top: 0%;padding: 10% 0% 0% 0%; color: white;font-size: 200%;">
		Login and Spam</h1><hr class="mo_wpns_upgrade_page_hr"></div>
		
		<div class="mo_wpns_upgrade_page_ns_background">
			<center>
			<h4 class="mo_wpns_upgrade_page_starting_price">Starting From</h4>
			<h1 class="mo_wpns_upgrade_pade_pricing">$15</h1>
			
				<?php echo mo2f_login_yearly_standard_pricing(); ?>
				
				
			</center>
			
		<div style="text-align: center;">
		<?php if( isset($is_customer_registered)&& $is_customer_registered ) {
			?>
	            <button class="button button-primary button-large mo_wpns_upgrade_page_button"
	                        onclick="mo2f_upgradeform('wp_security_login_and_spam_plan','2fa_plan')">Upgrade</button>
	        <?php }else{ ?>

	           <button class="button button-primary button-large mo_wpns_upgrade_page_button"
	                    onclick="mo2f_register_and_upgradeform('wp_security_login_and_spam_plan','2fa_plan')">Upgrade</button>
	        <?php } 
	        ?>
		</div>
			<div><center><b>
				<ul>
					<li>Limit login Attempts</li>
					<li>CAPTCHA on login</li>
					<li>Blocking time period</li>
					<li>Enforce Strong Password</li>
					<li>SPAM Content and Comment Protection</li>
					<li><a onclick="wpns_pricing()">Know more</a></li>
				</ul>
			</b></center></div>
		</div>
		
		
	</div>
	<div class="mo_wpns_upgrade_page_space_in_div"></div>
	<div class="mo_wpns_upgrade_security_title" >
		<div class="mo_wpns_upgrade_page_title_name">
			<h1 style="margin-top: 0%;padding: 10% 0% 0% 0%; color: white;font-size: 200%;">
		Malware Scanner</h1><hr class="mo_wpns_upgrade_page_hr"></div>
		
			<div class="mo_wpns_upgrade_page_ns_background">
			<center>
			<h4 class="mo_wpns_upgrade_page_starting_price">Starting From</h4>
			<h1 class="mo_wpns_upgrade_pade_pricing">$15</h1>
			
				<?php echo mo2f_scanner_yearly_standard_pricing(); ?>
				
				
			</center>
			<div style="text-align: center;">
			<?php if( isset($is_customer_registered) && $is_customer_registered) {
			?>
                <button
                            class="button button-primary button-large mo_wpns_upgrade_page_button"
                            onclick="mo2f_upgradeform('wp_security_malware_plan','2fa_plan')">Upgrade</button>
            <?php }else{ ?>

               <button
                            class="button button-primary button-large mo_wpns_upgrade_page_button"
                            onclick="mo2f_register_and_upgradeform('wp_security_malware_plan','2fa_plan')">Upgrade</button>
            <?php } 
            ?>
		</div>
			<div><center><b>
				<ul>
					<li>Malware Detection</li>
					<li>Blacklisted Domains</li>
					<li>Action On Malicious Files</li>
					<li>Repository Version Comparison</li>
					<li>Detect any changes in the files</li>
					<li><a onclick="wpns_pricing()">Know more</a></li>
				</ul>
			</b></center></div>
	</div>
	</div>
	<div class="mo_wpns_upgrade_page_space_in_div"></div>
	<div class="mo_wpns_upgrade_security_title" >
		<div class="mo_wpns_upgrade_page_title_name">
			<h1 style="margin-top: 0%;padding: 10% 0% 0% 0%; color: white;font-size: 200%;">
		Encrypted Backup</h1><hr class="mo_wpns_upgrade_page_hr"></div>
		
	<div class="mo_wpns_upgrade_page_ns_background">

		<center>
			<h4 class="mo_wpns_upgrade_page_starting_price">Starting From</h4>
			<h1 class="mo_wpns_upgrade_pade_pricing">$30</h1>
			
				<?php echo mo2f_backup_yearly_standard_pricing(); ?>
				
				
			</center>
			<div style="text-align: center;">
	<?php	if( isset($is_customer_registered) && $is_customer_registered) {
		?>
            <button
                        class="button button-primary button-large mo_wpns_upgrade_page_button"
                        onclick="mo2f_upgradeform('wp_security_backup_plan','2fa_plan')">Upgrade</button>
        <?php }else{ ?>
			<button
                        class="button button-primary button-large mo_wpns_upgrade_page_button"
                        onclick="mo2f_register_and_upgradeform('wp_security_backup_plan' ,'2fa_plan')">Upgrade</button>
        <?php } 
        ?>
		
		</div>
			<div><center><b>
				<ul>
					<li>Schedule Backup</li>
					<li>Encrypted Backup</li>
					<li>Files/Database Backup</li>
					<li>Restore and Migration</li>
					<li>Password Protected Zip files</li>
					<li><a onclick="wpns_pricing()">Know more</a></li>
				</ul>
			</b></center></div>
	</div></div>
</div>

<center>
	<br>
	<div id="mo2f_more_details" style="display:none;">
<div class="mo2fa_table-scrollbar"></br></br>
<table class="table mo2fa_table_features table-striped">
	<caption class="mo2f_pricing_head_mo_2fa"><h1>Feature Details</h1></caption>
  <thead>
    <tr class="mo2fa_main_category_header" style="font-size: 20px;">
      <th scope="col">Features</th>
      <th scope="col" class="mo2fa_plugins"><center>Premium Lite</center></th>
      <th scope="col" class="mo2fa_plugins"><center>Premium</center></th>
      <th scope="col" class="mo2fa_plugins"><center>Enterprise</center></th> 
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">Unlimited Sites</th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>      
    </tr>
   
    <tr>
     <th scope="row">Unlimited Users</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>

    </tr>
   <tr class="bg_category_main_mo_2fa">
     <th scope="row">Authentication Methods</th>
      <td></td>
      <td></td>   
      <td></td>
    </tr>
    <tr>
     <th scope="row" class="category_feature_mo_2fa">Google Authenticator</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
    </tr>
    <tr>
     <th scope="row" class="category_feature_mo_2fa">Security Questions</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr>
    <tr>
    <th scope="row" class="category_feature_mo_2fa">TOTP Based Authenticator</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr>
    <tr>
    <th scope="row" class="category_feature_mo_2fa">Authy Authenticator</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr> 
   <tr>
    <th scope="row" class="category_feature_mo_2fa">Email Verification</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr> 
    <tr>
    <th scope="row" class="category_feature_mo_2fa">OTP Over Email (Email Charges apply)</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>


    </tr> 
    <tr>
    <th scope="row" class="category_feature_mo_2fa">OTP Over SMS (SMS Charges apply)</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr>

   <tr>
    <th scope="row" class="category_feature_mo_2fa">miniOrange QR Code Authentication</th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
    </tr>
     <tr>
    <th scope="row" class="category_feature_mo_2fa">miniOrange Soft Token</th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
    </tr>
    <tr>
    <th scope="row" class="category_feature_mo_2fa">miniOrange Push Notification</th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
    </tr>
    <tr>
    <th scope="row" class="category_feature_mo_2fa">OTP Over SMS and Email (SMS and Email Charges apply)</th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr>
    <tr>
    <th scope="row" class="category_feature_mo_2fa">Hardware Token</th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr>
    <tr>
    <th scope="row" class="category_feature_mo_2fa">OTP Over Whatsapp (Add-on)</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
    </tr>
    <tr>
    <th scope="row" class="category_feature_mo_2fa">OTP Over Telegram</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>  
      <td><center><i class="fas fa-check"></i></center></td>
    </tr>
     <tr class="bg_category_main_mo_2fa">
     <th scope="row">Backup Login Methods</th>
      <td></td>   
      <td></td>   
      <td></td>   
    </tr>
    <tr>
    <th scope="row" class="category_feature_mo_2fa">Security Questions (KBA)</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
    </tr>
    <tr>
    <th scope="row" class="category_feature_mo_2fa">OTP Over Email</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
    </tr>
    <tr>
    <th scope="row" class="category_feature_mo_2fa">Backup Codes</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
    </tr>
    <tr class="bg_category_main_mo_2fa">
     <th scope="row">Password Policy</th>
      <td></td>   
      <td></td>   
      <td></td>   

    </tr>
   <tr>
    <th scope="row" class="category_feature_mo_2fa">Passwordless Login</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
    </tr> 
    <tr>
    <th scope="row" class="category_feature_mo_2fa">Strong Password</th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>

    </tr>
    <tr>
     <th scope="row">Custom Gateway</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr>
  <tr class="bg_category_main_mo_2fa">
     <th scope="row">Add-Ons</th>
      <td></td>   
      <td></td>   
      <td></td>   

    </tr>
     <tr>
     <th scope="row" class="category_feature_mo_2fa">Remember Device Add-on</br><p class="description_mo_2fa">You can save your device using the Remember device addon and you will get a two-factor authentication </br>prompt to check your identity if you try to login from different devices.</p></th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
    </tr>
    <tr>
     <th scope="row" class="category_feature_mo_2fa">Personalization Add-on<p class="description_mo_2fa">You'll get many more customization options in Personalization, such as </br>ustom Email and SMS Template, Custom Login Popup, Custom Security Questions, and many more.</p></th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
    </tr>
     <tr>
     <th scope="row" class="category_feature_mo_2fa">Short Codes Add-on<p class="description_mo_2fa">Shortcode Add-ons mostly include Allow 2fa shortcode (you can use this this to add 2fa on any page), </br>Reconfigure 2fa add-on (you can use this add-on to reconfigure your 2fa if you have lost your 2fa verification ability), remember device shortcode.</p></th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
    </tr>
   <tr>
     <th scope="row" class="category_feature_mo_2fa">Session Management</th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
    </tr><tr>
     <th scope="row" class="category_feature_mo_2fa">Page Restriction Add-On</th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
    </tr><tr>
     <th scope="row" class="category_feature_mo_2fa">Attribute Based Redirection</th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
    </tr>
    <th scope="row" class="category_feature_mo_2fa">SCIM-User Provisioning</th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
    </tr>
 

    <tr class="bg_category_main_mo_2fa">
     <th scope="row">Advance Wordpress Login Settings</th>
      <td></td>
      <td></td> 
      <td></td>   
  
    </tr>
     <tr>
     <th scope="row" class="category_feature_mo_2fa">Force Two Factor for Users</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
    </tr>
    <tr>
     <th scope="row" class="category_feature_mo_2fa">Role Based and User Based Authentication settings</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
    </tr>
    <tr>
     <th scope="row" class="category_feature_mo_2fa">Email Verififcation during Two-Factor Registration</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr>
<tr>
     <th scope="row" class="category_feature_mo_2fa">Custom Redirect URL</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr><tr>
     <th scope="row" class="category_feature_mo_2fa">Inline Registration</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr><tr>
     <th scope="row" class="category_feature_mo_2fa">Mobile Support</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr><tr>
     <th scope="row" class="category_feature_mo_2fa">Privacy Policy Settings</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr><tr>
     <th scope="row" class="category_feature_mo_2fa">XML-RPC </th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr>
     <tr class="bg_category_main_mo_2fa">
     <th scope="row">Advance Security Features</th>
      <td></td>
      <td></td>
      <td></td>   
   
    </tr>
     <tr>
     <th scope="row" class="category_feature_mo_2fa">Brute Force Protection</th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr>
    <tr>
     <th scope="row" class="category_feature_mo_2fa">IP Blocking </th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr>
     <tr>
     <th scope="row" class="category_feature_mo_2fa">Monitoring</th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr> <tr>
     <th scope="row" class="category_feature_mo_2fa">File Protection</th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr>
   <tr>
     <th scope="row" class="category_feature_mo_2fa">Country Blocking </th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr>
    <tr>
     <th scope="row" class="category_feature_mo_2fa">HTACCESS Level Blocking </th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr>
    <tr>
     <th scope="row" class="category_feature_mo_2fa">Browser Blocking </th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr>
   <tr>
     <th scope="row" class="category_feature_mo_2fa">Block Global Blacklisted Email Domains</th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr>
 <tr>
     <th scope="row" class="category_feature_mo_2fa">Manual Block Email Domains</th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr>
   <tr>
     <th scope="row" class="category_feature_mo_2fa">DB Backup</th>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr>
<tr>
     <th scope="row">Multi-Site Support</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
      <td><center><i class="fas fa-times mo2fa_hide"></i></center></td>
    </tr><tr>
     <th scope="row">Language Translation Support</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr><tr>
     <th scope="row">Get online support with GoTo/Zoom meeting</th>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>
      <td><center><i class="fas fa-check"></i></center></td>

    </tr>
  </tbody>
</table>
</div>
</div>
</center>
</br>
<div id="mo2f_payment_option" class="mo2f_table_layout" style="width: 90%; display: flex;">
	<div>
		<h3>Supported Payment Methods</h3><hr>
		<div class="mo_2fa_container">
			<div class="mo_2fa_card-deck">
				<div class="mo_2fa_card mo_2fa_animation">
					<div class="mo_2fa_Card-header">
						<?php 
						echo'<img src="'.(plugin_dir_url(__FILE__)).'includes/images/card.png" class="mo2fa_card">';?>
					</div>
					<hr class="mo2fa_hr">
					<div class="mo_2fa_card-body">
						<p class="mo2fa_payment_p">If payment is done through Credit Card/Intenational debit card, the license would be created automatically once payment is completed. </p>
						<p class="mo2fa_payment_p"><i><b>For guide 
							<?php echo'<a href='.FAQ_PAYMENT_URL.' target="blank">Click Here.</a>';?></b></i></p>

						</div>
					</div>
					<div class="mo_2fa_card mo_2fa_animation">
						<div class="mo_2fa_Card-header">
							<?php 
							echo'<img src="'.(plugin_dir_url(__FILE__)).'includes/images/paypal.png" class="mo2fa_card">';?>
						</div>
						<hr class="mo2fa_hr">
						<div class="mo_2fa_card-body">
							<?php echo'<p class="mo2fa_payment_p">Use the following PayPal id for payment via PayPal.</p><p><i><b style="color:#1261d8"><a href="mailto:'.SUPPORT_EMAIL.'">info@xecurify.com</a></b></i>';?>

						</div>
					</div>
					<div class="mo_2fa_card mo_2fa_animation">
						<div class="mo_2fa_Card-header">
							<?php 
							echo'<img src="'.(plugin_dir_url(__FILE__)).'includes/images/bank-transfer.png" class="mo2fa_card mo2fa_bank_transfer">';?>

						</div>
						<hr class="mo2fa_hr">
						<div class="mo_2fa_card-body">
							<?php echo'<p class="mo2fa_payment_p">If you want to use Bank Transfer for payment then contact us at <i><b style="color:#1261d8"><a href="mailto:'.SUPPORT_EMAIL.'">info@xecurify.com</a></b></i> so that we can provide you bank details. </i></p>';?>
						</div>
					</div>
				</div>
			</div>
			<div class="mo_2fa_mo-supportnote">
				<p class="mo2fa_payment_p"><b>Note :</b> Once you have paid through PayPal/Bank Transfer, please inform us at <i><b style="color:#1261d8"><a href="mailto:'.MoWpnsConstants::SUPPORT_EMAIL.'">info@xecurify.com</a></b></i>, so that we can confirm and update your License.</p> 
			</div>
		</div>
	</div>


	<?php
function mo2f_waf_yearly_standard_pricing() {
	?>
    <p class="mo2f_pricing_text mo_wpns_upgrade_page_starting_price"
       id="mo2f_yearly_sub"><?php echo __( 'Yearly subscription fees', 'miniorange-2-factor-authentication' ); ?><br>

	<select id="mo2f_yearly" class="form-control mo2fa_form_control1">
		<option> <?php echo mo2f_lt( '1 site - $50 per year' ); ?> </option>
		<option> <?php echo mo2f_lt( 'Upto 5 sites - $100 per year' ); ?> </option>
		<option> <?php echo mo2f_lt( 'Upto 10 sites - $150 per year' ); ?> </option>

	</select>
</p>

	<?php
}
function mo2f_login_yearly_standard_pricing() {
	?>
    <p class="mo2f_pricing_text mo_wpns_upgrade_page_starting_price"
       id="mo2f_yearly_sub"><?php echo __( 'Yearly subscription fees', 'miniorange-2-factor-authentication' ); ?><br>

	<select id="mo2f_yearly" class="form-control mo2fa_form_control1">
		<option> <?php echo mo2f_lt( '1 site - $15 per year' ); ?> </option>
		<option> <?php echo mo2f_lt( 'Upto 5 sites - $35 per year' ); ?> </option>
		<option> <?php echo mo2f_lt( 'Upto 10 sites - $60 per year' ); ?> </option>

	</select>
</p>

	<?php
}
function mo2f_backup_yearly_standard_pricing() {
	?>
    <p class="mo2f_pricing_text mo_wpns_upgrade_page_starting_price"
       id="mo2f_yearly_sub"><?php echo __( 'Yearly subscription fees', 'miniorange-2-factor-authentication' ); ?><br>

	<select id="mo2f_yearly" class="form-control mo2fa_form_control1">
		<option> <?php echo mo2f_lt( '1 site - $30 per year' ); ?> </option>
		<option> <?php echo mo2f_lt( 'Upto 5 sites - $50 per year' ); ?> </option>
		<option> <?php echo mo2f_lt( 'Upto 10 sites - $70 per year' ); ?> </option>

	</select>
</p>

	<?php
}
function mo2f_scanner_yearly_standard_pricing() {
	?>
    <p class="mo2f_pricing_text mo_wpns_upgrade_page_starting_price" 
       id="mo2f_yearly_sub"><?php echo __( 'Yearly subscription fees', 'miniorange-2-factor-authentication' ); ?><br>

	<select id="mo2f_yearly" class="form-control mo2fa_form_control1">
		<option> <?php echo mo2f_lt( '1 site - $15 per year' ); ?> </option>
		<option> <?php echo mo2f_lt( 'Upto 5 sites - $35 per year' ); ?> </option>
		<option> <?php echo mo2f_lt( 'Upto 10 sites - $60 per year' ); ?> </option>

	</select>
</p>

	<?php
}

function mo2f_get_binary_equivalent_2fa_lite( $mo2f_var ) {
	switch ( $mo2f_var ) {
		case 1:
			return "<div style='color: #20b2aa;font-size: x-large;float:left;margin:0px 5px;'>ðŸ—¸</div>";
		case 0:
			return "<div style='color: red;font-size: x-large;float:left;margin:0px 5px;'>Ã—</div>";
		default:
			return $mo2f_var;
	}
}

function mo2f_feature_on_hover_2fa_upgrade( $mo2f_var ) {

	return '<div class="mo2f_tooltip" style="float: right;width: 6%;"><span class="dashicons dashicons-info mo2f_info_tab"></span><span class="mo2f_tooltiptext" style="margin-left:-232px;margin-top: 9px;">'. $mo2f_var .'</span></div>';
}

?>
			<form class="mo2f_display_none_forms" id="mo2fa_loginform" action="<?php echo MO_HOST_NAME . '/moas/login'; ?> "target="_blank" method="post">
                <input type="hidden" name="username" value="<?php echo get_option( 'mo2f_email' ); ?>"/>
                <input type="hidden" name="redirectUrl" value="<?php echo MO_HOST_NAME . '/moas/initializepayment'; ?>"/>
                <input type="hidden" name="requestOrigin" id="requestOrigin"/>
            </form>

            <form class="mo2f_display_none_forms" id="mo2fa_register_to_upgrade_form"
                   method="post">
                <input type="hidden" name="requestOrigin" />
                <input type="hidden" name="mo2fa_register_to_upgrade_nonce"
                       value="<?php echo wp_create_nonce( 'miniorange-2-factor-user-reg-to-upgrade-nonce' ); ?>"/>
            </form>

    <script type="text/javascript">

function mo2fa_show_details()
		{
			jQuery('#mo2f_more_details').toggle();
			jQuery('.mo2fa_more_details_p1').toggle();
			jQuery('.mo2fa_more_details_p').toggle();
			jQuery('.mo2fa_compare1').toggle();
		}


	var switcher = document.getElementById("mo2f_switcher"),
    unlimited_users = document.getElementById("mo2f_unlimited_users"),
    unlimited_sites = document.getElementById("mo2f_unlimited_sites"),
    premium_plan = document.getElementById("mo2f_premium_plan"),
    premium_lite_plan = document.getElementById("mo2f_premium_lite_plan"),
    standard_lite_plan = document.getElementById("mo2f_standard_lite_plan"),
    enterprise_plan = document.getElementById("mo2f_enterprise_plan");




		function mo2f_upgradeform(planType) 
		{
            jQuery('#requestOrigin').val(planType);
            jQuery('#mo2fa_loginform').submit();
        }
        function mo2f_register_and_upgradeform(planType) 
        {
                    jQuery('input[name="requestOrigin"]').val(planType);
                    jQuery('#mo2fa_register_to_upgrade_form').submit();
        }

    	function show_2fa_plans()
    	{
    		document.getElementById('mo2f_info').style.display = "none";
    		document.getElementById('mo_ns_features_only').style.display = "none";
    		document.getElementById('mo2f_twofa_plans').style.display = "flex";
    		document.getElementById('mo2f_plan_type').style.display = "block";
    		document.getElementById('mo_2fa_lite_licensing_plans_title').style.display = "none";
    		document.getElementById('mo_2fa_lite_licensing_plans_title1').style.display = "block";
    		document.getElementById('mo_ns_licensing_plans_title').style.display = "block";
    		document.getElementById('mo_ns_licensing_plans_title1').style.display = "none";
    		document.getElementById('mo2fa_compare').style.display = "block";
    	}
    	function mo_ns_show_plans()
    	{
    		document.getElementById('mo_ns_features_only').style.display = "block";
    		document.getElementById('mo2f_twofa_plans').style.display = "none";
    		document.getElementById('mo2f_plan_type').style.display = "none";
    		document.getElementById('mo2f_info').style.display = "none";
    		document.getElementById('mo2f_more_details').style.display = "none";
    		document.getElementById('mo_2fa_lite_licensing_plans_title').style.display = "block";
    		document.getElementById('mo_2fa_lite_licensing_plans_title1').style.display = "none";
    		document.getElementById('mo_ns_licensing_plans_title').style.display = "none";
    		document.getElementById('mo_ns_licensing_plans_title1').style.display = "block";
    		document.getElementById('mo2fa_compare').style.display = "none";
    	}

    	function wpns_pricing()
		{
			window.open("https://security.miniorange.com/pricing/");
		}

		
    </script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function(){




});
</script>