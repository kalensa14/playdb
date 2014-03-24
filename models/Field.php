<?php
class Field extends CModel
{
	private $_attributes = array();
	private $_name;
	private $_table;
	private $_fkinfo;

	public function __construct(Table $table, $name)
	{
		$this->_table = $table;
		$this->_name = $name;
		parent::__construct();
	}

	public function __get($name)
	{
		if ( array_key_exists($name, $this->_attributes) )
			return $this->_attributes[$name];

		return parent::__get($name);
	}

	public function __isset($name)
	{
		if ( array_key_exists($name, $this->_attributes) )
			return true;

		return parent::__isset($name);
	}

	public function init()
	{
		$query = $this->db->prepare('SELECT column_name, column_type, column_key, column_default, is_nullable, data_type, collation_name, extra, column_comment FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema=:schema AND table_name=:table AND column_name=:name');
		$query->execute(array(
			':schema' => $this->table->dbname,
			':table'  => $this->table->name,
			':name'   => $this->_name
		));

		while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
			foreach ($row as $key => $val)
				$this->_attributes[strtolower($key)] = $val;
		}
	}

	public function getTable()
	{
		return $this->_table;
	}

	public function getName()
	{
		return $this->_name;
	}

	public function getAttributes()
	{
		return $this->_attributes;
	}

	public function getIsPrimaryKey()
	{
		return isset($this->column_key) && $this->column_key == 'PRI';
	}

	public function getFKInfo()
	{
		if ($this->_fkinfo === null) {
			$this->_fkinfo = array();

			$query = $this->db->prepare('SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE constraint_schema=:schema AND table_name=:table AND column_name=:name');
			$query->execute(array(
				':schema' => $this->table->dbName,
				':table'  => $this->table->name,
				':name'  => $this->_name,
			));

			while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

				if ($row['REFERENCED_TABLE_NAME']) {

					$this->_fkinfo[$row['CONSTRAINT_NAME']] = array(
						'table' => $row['REFERENCED_TABLE_NAME'],
						'key' => $row['REFERENCED_COLUMN_NAME'],
					);
				}
			}
		}

		return $this->_fkinfo;
	}

	public function getIsAutoIncrement()
	{
		return !!strstr($this->extra, 'auto_increment');
	}
}

?>