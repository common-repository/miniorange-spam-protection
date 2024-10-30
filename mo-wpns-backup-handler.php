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

class Mo_MSP_Backup_Handler{

	function backup_db(){
		
		if ( function_exists('memory_get_usage') && ( (int) ini_get('memory_limit') < 128 ) ) {
			ini_set('memory_limit', '128M' );
		}
		global $table_prefix, $wpdb;
		$tables = $wpdb->get_results("SHOW TABLES", ARRAY_N);
		$return = "";
		$tableswithfk = array();
		foreach($tables as $table){
			if(is_array($table))
				$table = $table[0];
			$createtable = $wpdb->get_results("SHOW CREATE TABLE  $table", ARRAY_A);
			if(isset($createtable[0])){
				$createquery = $createtable[0]['Create Table'];
				if (strpos($createquery, 'FOREIGN KEY') !== false) {
					array_push($tableswithfk,$table);
					continue;
				}
				$return.= 'DROP TABLE IF EXISTS '.$table.";\n";
				$return.= $createquery.";\n\n";
				$data = $wpdb->get_results("SELECT * FROM $table", ARRAY_A);
				$num_fields = count($data);
				foreach($data as $record){
					if(count($record)>0){
						$return.= 'INSERT INTO '.$table.' VALUES(';
						$i=0;
						foreach($record as $key=>$value){
							$value = addslashes($value);
							//$value = ereg_replace ("\n","\\n",$value);
							if (isset($value)) { $return.= '"'.$value.'"' ; } else { $return.= '""'; }
							if ($i < (count($record)-1)) { $return.= ','; }
							$i++;
						}
						$return.= ");\n";
					}
				}
				$return.="\n\n";
			}
		}
		
		foreach($tableswithfk as $table){
			$createtable = $wpdb->get_results("SHOW CREATE TABLE  $table", ARRAY_A);
			if(isset($createtable[0])){
				$createquery = $createtable[0]['Create Table'];
				$return.= 'DROP TABLE IF EXISTS '.$table.";\n";
				$return.= $createquery.";\n\n";
				$data = $wpdb->get_results("SELECT * FROM $table", ARRAY_A);
				$num_fields = count($data);
				foreach($data as $record){
					if(count($record)>0){
						$return.= 'INSERT INTO '.$table.' VALUES(';
						$i=0;
						foreach($record as $key=>$value){
							$value = addslashes($value);
							if (isset($value)) { $return.= '"'.$value.'"' ; } else { $return.= '""'; }
							if ($i < (count($record)-1)) { $return.= ','; }
							$i++;
						}
						$return.= ");\n";
					}
				}
				$return.="\n\n";
			}
		}
		
		$basepath = get_home_path();
		if(!file_exists($basepath."db-backups")){
			mkdir($basepath."db-backups");
			$f = fopen($basepath."db-backups".DIRECTORY_SEPARATOR.".htaccess", "w");
			fwrite($f, "Options All -Indexes");
			fclose($f);
		}
		
		$filename = 'db-backup-'.gmdate("D-M-j-G.i.s-Y-T",time()).'.sql';
		$handle = fopen($basepath."db-backups".DIRECTORY_SEPARATOR .$filename,'w+');
		fwrite($handle,$return);
		fclose($handle);
		return $filename;
	}
	
}