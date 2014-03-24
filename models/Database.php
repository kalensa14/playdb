<?php
class Database extends CModel
{
	private $_tables;
	private $_name;

	public function init()
	{
		$row = $this->db->query('SELECT DATABASE()')->fetch();

		if ( !$row[0] ) {
			throw new Exception('Database must be specified.');
		}

		$this->_name = $row[0];
	}

	public function getTables()
	{
		if ($this->_tables === null) {
			$q = $this->db->prepare('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.tables WHERE table_schema=:name');
			$q->execute(array(':name' => $this->_name));
			while ($name = $q->fetchColumn()) {
				$this->_tables[$name] = new Table($name, $this->_name);
			}
		}

		return $this->_tables;
	}

	public function getStats()
	{
		$q = $this->db->prepare('SELECT * FROM INFORMATION_SCHEMA.tables WHERE table_schema=:name');
		$q->execute(array(':name' => $this->_name));
		$stats = array();

		while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
			$newrow = array();
			foreach ($row as $key => $val)
				$newrow[strtolower($key)] = $val;

			$stats[] = $newrow;
		}

		return $stats;
	}
}

?>