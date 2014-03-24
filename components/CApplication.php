<?php

class CApplication extends CComponent
{
	private $_db;

	public function __construct($config)
	{
		Playdb::setApplication($this);

		if ( isset($config['database']) ) {
			$this->_db = new CDatabase($config['database']);
		}

		if (count($_SERVER['argv']) <= 2) {
			$this->usage();
			$this->end();
		}

		$argv = $_SERVER['argv'];

		$route = array_splice($argv, 1, 2);

		$class = ucfirst(strtolower($route[0])).'Controller';
		$action = 'action'.ucfirst(strtolower($route[1]));

		if ( !class_exists($class) ) {
			$this->usage('Controller '.$route[0].' is not found.');
			// throw new Exception('Controller '.$route[0].' is not found.');
		}

		$methods = get_class_methods($class);
		foreach ($methods as $method) {
			if (strcasecmp($method, $action) === 0) {
				$action = $method;
				break;
			}
		}

		if ( ! method_exists($class, $action) ) {
			throw new Exception('Controller '.$route[0].' has no such action '.$route[1].'.');
		}

		$extra = array_splice($argv, 1);
		$params = array();
		if (count($extra)) foreach ($extra as $part) {
			if (substr($part, 0, 2) == '--' && strstr($part, '=')) {
				list($key, $val) = explode('=', substr($part, 2));
				$params[$key] = $val;
			}
		}

		$controller = new $class();
		$controller->$action($params);
	}

	public function getDb()
	{
		return $this->_db;
	}

	public function usage($msg = '')
	{
		if ($msg) {
			echo $msg . PHP_EOL;
		}

		echo 'Usage: php '.$_SERVER['PHP_SELF'].' controller action [--param1=value1 [--param2=value2 [ ...]]]'.PHP_EOL;
		echo 'available commands: ' . PHP_EOL;
		echo "\tdatabase showStat - displays detail information about tables in database" . PHP_EOL;
		echo "\ttable desc --name=TABLE_NAME - displays table structure for TABLE_NAME" . PHP_EOL;
		echo "\ttable desc --name=TABLE_NAME - displays table structure for TABLE_NAME" . PHP_EOL;
		echo "\ttable fillRandom --name=TABLE_NAME [--count=NEW_FIELDS_COUNT] - fills TABLE_NAME with NEW_FIELDS_COUNT or 10 by default randomly generate records" . PHP_EOL;
		echo "\ttable showData --name=TABLE_NAME - browse last records in TABLE_NAME table" . PHP_EOL;
	}

	public function end()
	{
		exit;
	}
}