<?php

function mo_msp_support(){

	$current_user = wp_get_current_user();
	if(get_option('mo_wpns_admin_email'))
		$admin_email = get_option('mo_wpns_admin_email');
	else
		$admin_email = $current_user->user_email;
	
// 	update_option('mo_wpns_admin_email',get_option('admin_email'));
	
?>
	<div class="mo_wpns_support_layout">
		<h3>Support</h3>
			<form name="f" method="post" action="">
				<div>Need any help? Just send us a query so we can help you.<br /><br /></div>
				<div>
					<table class="mo_wpns_settings_table">
						<tr><td>
							<input type="email" class="mo_wpns_table_textbox" id="query_email" name="query_email" value="<?php echo $admin_email; ?>" placeholder="Enter your email" required />
							</td>
						</tr>
						<tr><td>
							<input type="text" class="mo_wpns_table_textbox" name="query_phone" id="query_phone" value="<?php echo get_option('mo_wpns_admin_phone'); ?>" placeholder="Enter your phone"/>
							</td>
						</tr>
						<tr>
							<td>
								<textarea id="query" name="query" class="mo_wpns_settings_textarea" style="border-radius:4px;resize: vertical;width:100%" cols="52" rows="7" onkeyup="mo_wpns_valid(this)" onblur="mo_wpns_valid(this)" onkeypress="mo_wpns_valid(this)" placeholder="Write your query here"></textarea>
							</td>
						</tr>
					</table>
				</div>
				<input type="hidden" name="option" value="mo_wpns_send_query"/>
				<input type="submit" name="send_query" id="send_query" value="Submit Query" style="margin-bottom:3%;" class="button button-primary button-large" />
			</form>
			<br />			
	</div>
	<script>
		jQuery("#query_phone").intlTelInput();
		function mo_wpns_valid(f) {
			!(/^[a-zA-Z?,.\(\)\/@ 0-9]*$/).test(f.value) ? f.value = f.value.replace(/[^a-zA-Z?,.\(\)\/@ 0-9]/, '') : null;
		}
	</script>
<?php
}
?>