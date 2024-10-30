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

class Mo_MSP_Htaccess_Handler{

	function update_htaccess_configuration(){
		$base = dirname(dirname(dirname(dirname(__FILE__))));
		$this->change_wp_config_protection($base);
		$this->change_content_protection($base);
	}
	
	function change_wp_config_protection($base){
		$htaccesspath = $base.DIRECTORY_SEPARATOR.".htaccess";
		$contents = file_get_contents($htaccesspath);
		if (strpos($contents, "\n<files wp-config.php>\norder allow,deny\ndeny from all\n</files>") !== false) {
			if(!get_option('protect_wp_config')){
				$contents = str_replace("\n<files wp-config.php>\norder allow,deny\ndeny from all\n</files>", '', $contents);
				file_put_contents($htaccesspath, $contents);
			}
		} else{
			if(get_option('protect_wp_config')){
				$f = fopen($base.DIRECTORY_SEPARATOR.".htaccess", "a");
				fwrite($f, "\n<files wp-config.php>\norder allow,deny\ndeny from all\n</files>");
				fclose($f);
			}
		}
	}
	
	function change_content_protection($base){
		$htaccesspath = $base.DIRECTORY_SEPARATOR.".htaccess";
		$contents = file_get_contents($htaccesspath);
		if (strpos($contents, "\nOptions All -Indexes") !== false) {
			if(!get_option('prevent_directory_browsing')){
				$contents = str_replace("\nOptions All -Indexes", '', $contents);
				file_put_contents($htaccesspath, $contents);
			}
		} else{
			if(get_option('prevent_directory_browsing')){
				$f = fopen($base.DIRECTORY_SEPARATOR.".htaccess", "a");
				fwrite($f, "\nOptions All -Indexes");
				fclose($f);
			}
		}
	}
	

}