<?php
/** miniOrange enables user to log in using LDAP credentials.
    Copyright (C) 2015  miniOrange

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
*/
/**
This class contains all the utility functions

**/
class Mo_MSP_Util{

	public static function is_customer_registered() {
		$email 			= get_option('mo_wpns_admin_email');
		$customerKey 	= get_option('mo_wpns_admin_customer_key');
		if( ! $email || ! $customerKey || ! is_numeric( trim( $customerKey ) ) ) {
			return 0;
		} else {
			return 1;
		}
	}
	
	public static function check_empty_or_null( $value ) {
		if( ! isset( $value ) || empty( $value ) ) {
			return true;
		}
		return false;
	}
	
	public static function encrypt($str){
		$str = stripcslashes($str);
		if(!Mo_MSP_Util::is_extension_installed('mcrypt')) {
			return;
		}
		$key = get_option('mo_wpns_customer_token');
		$block = mcrypt_get_block_size('rijndael_128', 'ecb');
		$pad = $block - (strlen($str) % $block);
		$str .= str_repeat(chr($pad), $pad);
		return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $str, MCRYPT_MODE_ECB));
	}
	
	public static function decrypt($value)
	{
		if(!Mo_MSP_Util::is_extension_installed('mcrypt')) {
			return;
		}
		$key = get_option('mo_wpns_customer_token');
		$string = rtrim(
			mcrypt_decrypt(
				MCRYPT_RIJNDAEL_128, 
				$key, 
				base64_decode($value), 
				MCRYPT_MODE_ECB,
				mcrypt_create_iv(
					mcrypt_get_iv_size(
						MCRYPT_RIJNDAEL_128,
						MCRYPT_MODE_ECB
					), 
					MCRYPT_RAND
				)
			), "\0"
		);
		return trim($string,"\0..\32");
	}
	
	public static function is_curl_installed() {
		if  (in_array  ('curl', get_loaded_extensions())) {
			return 1;
		} else 
			return 0;
	}
	
	public static function is_extension_installed($name) {
		if  (in_array  ($name, get_loaded_extensions())) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public static function get_client_ip() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
		return '';
	}
}
?>