<?php 

class Config{
	private static $conf = array();
	
	public static function set($name, $value){
		self::$conf[$name] = $value;
	}
	
	public static function get($name){
		return self::$conf[$name];
	}
	
	public static function exist($name){
		return isset(self::$conf[$name]);
	}
	
}

Config::set('language', 'pl');
Config::set('path','jakas sciezka');
echo Config::get('language');
echo Config::get('path');
echo Config::exist('path');

?>