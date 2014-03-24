<?php
class Table extends CModel
{
	private $_fields;
	private $_field_count = 0;
	private $_dbname;
	private $_name;
	private $_attributes = array();

	public function __construct($name, $dbname = null)
	{
		$this->_name = $name;

		if ($dbname === null)
			$this->_dbname = $this->getDbName();

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
		$q = $this->db->prepare('SELECT * FROM INFORMATION_SCHEMA.tables WHERE table_schema=:db AND table_name=:name');
		$q->execute(array(':db' => $this->_dbname, ':name' => $this->_name));

		if ($q->rowCount() == 0) {
			throw new Exception('Wrong table '.$this->_name);
		}

		while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
			foreach ($row as $key => $val)
				$this->_attributes[strtolower($key)] = $val;
		}

		$this->_fields = $this->getFields();
	}

	public function getDbName()
	{
		if ( $this->_dbname === null ) {
			$row = $this->db->query('SELECT DATABASE()')->fetch();

			if ( !$row[0] ) {
				throw new Exception('Database must be specified.');
			}

			$this->_dbname = $row[0];
		}

		return $this->_dbname;
	}

	/**
	* @return Field[]
	*/
	public function getFields()
	{
		if ($this->_fields === null) {
			$q = $this->db->prepare('SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name=:name');
			$q->execute(array(':name' => $this->_name));
			while ($name = $q->fetchColumn()) {
				$this->_fields[$name] = new Field($this, $name);
			}

			$this->_field_count = count($this->_fields);
		}

		return $this->_fields;

	}

	public function getName()
	{
		return $this->_name;
	}

	public function getFieldsCount()
	{
		return $this->_field_count;
	}

	public function getPrimaryKey()
	{
		foreach ($this->getFields() as $field) {
			if ($field->column_key == 'PRI')
				return $field->name;
		}

		return null;
	}

	public function getData($limit = null)
	{
		$pk = $this->primaryKey;
		$sql = 'SELECT * FROM '.$this->_name.' '.($pk !== null ? 'ORDER BY '.$pk.' DESC ' : '');
		$sql .= $limit !== null ? ' LIMIT ' .(int)$limit : '';
		return $this->db->query($sql)
			->fetchAll(PDO::FETCH_ASSOC);
	}

	public function createNewRow()
	{
		return new TableRow($this);
	}
}