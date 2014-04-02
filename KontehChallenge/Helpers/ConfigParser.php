<?php
class ConfigParser {
	private static function getDatabaseConfig($asKey) {
		$file = "";
		if(file_exists ( 'config.ini' ) )
			$file = "config.ini";
		if(file_exists ( '../config.ini' ) )
			$file = "../config.ini";
		$config = parse_ini_file($file, true);
		return $config['DB'][$asKey];
	}

	public static function DBHOST(){
		return self::getDatabaseConfig('host');
	}

	public static function DBUSERNAME(){
		return self::getDatabaseConfig('username');
	}

	public static function DBPASSWORD(){
		return self::getDatabaseConfig('password');
	}

	public static function DBDATABASE(){
		return self::getDatabaseConfig('database');
	}
	
	public static function DBDUMMYUSER(){
		return self::getDatabaseConfig('dummyname');
	}
	
	public static function DBDUMMYPASS(){
		return self::getDatabaseConfig('dummypass');
	}
}
?>