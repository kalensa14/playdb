<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');

define('APP_BASEPATH', dirname(__FILE__));
set_include_path(get_include_path() . PATH_SEPARATOR . APP_BASEPATH.'/components' . PATH_SEPARATOR . APP_BASEPATH . '/models');

function __autoload($class)
{
	if (substr(strtolower($class), -10) == 'controller' && is_readable(APP_BASEPATH . '/controllers/'.$class.'.php')) {
		include_once(APP_BASEPATH . '/controllers/'.$class.'.php');
	} else {
		include_once($class.'.php');
	}
}

class Playdb
{
	static private $_app;

	public static function createApplication($config)
	{
		new CApplication($config);
	}

	public static function setApplication($app)
	{
		if ( self::$_app === null )
			self::$_app = $app;
	}

	public static function app()
	{
		return self::$_app;
	}
}

Playdb::createApplication(array(
	'database' => array(
		// 'host' => '127.0.0.1' // default is localhost
		'user' => 'databaseuser',
		'database' => 'databasename',
		'password' => 'databaseaccesspassword',
	)
));
?>