<?php
class ConfigParser {
	private static function getDatabaseConfig($asKey) {
		$config = parse_ini_file("config.ini", true);
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