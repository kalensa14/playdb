<?php

class TableRow extends CModel
{
	private $_table;
	private $_value;

	public function __construct(Table $table)
	{
		$this->_table = $table;
	}

	public function getTable()
	{
		return $this->_table;
	}

	public function getFields()
	{
		return $this->_table->fields;
	}

	public function setFieldValue($name, $value)
	{
		$this->_value[$name] = $value;
	}

	public function save()
	{
		/*echo 'save()' . PHP_EOL;
		var_export($this->_value);*/
		$sql = 'INSERT INTO ' . $this->table->name;
		$set = array();
		foreach ($this->_value as $key => $value) {
			$set[] = $key . '=:'.$key;
		}

		$sql .= ' SET ' . implode(', ', $set);

		$q = $this->db->prepare($sql);
		foreach ($this->_value as $key => $value) {
			$q->bindValue(':'.$key, $value);
		}

		$q->execute();
	}
}

?>