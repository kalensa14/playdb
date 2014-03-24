<?php
class CDatabase extends PDO
{
	public function __construct($c)
	{
		if ( !isset($c['host']) )
			$c['host'] = 'localhost';

		try {
			return parent::__construct('mysql:host='.$c['host'].';dbname='.$c['database'], $c['user'], $c['password'], array(
				PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
			));
		} catch (PDOException $e) {
			echo "Failed to get DB handle: " . $e->getMessage() . "\n";
			exit;
		}
	}
}
?>