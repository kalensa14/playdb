<?php

class CConsoleTable
{
	private $_row = array();
	private $_column = array();

	public function addColumn($title)
	{
		$this->_column[] = array('title' => $title, 'maxlen' => strlen($title));
	}

	public function addRow($data)
	{
		$newrow = array();
		foreach ($this->_column as $i => $column) {
			$newrow[$i] = isset($data[$i]) ? $data[$i] : null;
			if ($column['maxlen'] < strlen($newrow[$i]))
				$this->_column[$i]['maxlen'] = strlen($newrow[$i]);
		}

		$this->_row[] = $newrow;
	}

	public function draw()
	{

		// var_export($this->_column); return;

		foreach ($this->_column as $i => $column)
			echo '+' . str_repeat('-', $column['maxlen'] + 2);
			echo '+' . PHP_EOL;

		foreach ($this->_column as $i => $column)
			echo sprintf('| %-'.$column['maxlen'].'s ', $column['title']);
			echo '|' . PHP_EOL;

		foreach ($this->_column as $i => $column)
			echo '+' . str_repeat('-', $column['maxlen'] + 2);
			echo '+' . PHP_EOL;

		foreach ($this->_row as $row) {
			foreach ($this->_column as $i => $column) {
				echo sprintf('| %'.($i==0?'-':'').$column['maxlen'].'s ', $row[$i]);
			}
			echo '|' . PHP_EOL;
		}

		foreach ($this->_column as $i => $column)
			echo '+' . str_repeat('-', $column['maxlen'] + 2);
			echo '+' . PHP_EOL . PHP_EOL;
	}

	static function fromArray($data, $cols = null)
	{
		// $cols = array('table_name', 'engine', 'table_rows', 'data_length', 'create_time', 'update_time', 'table_comment');
		$t = new CConsoleTable();

		if (!$data)
			return false;

		if ( !($cols && is_array($cols)) ) {
			list(, $row) = each($data); reset($data);
			$cols = array_keys($row);
		}

		foreach ($cols as $one)
			$t->addColumn(ucfirst(str_replace('_', ' ', $one)));

		foreach ($data as $row) {
			$insert = array();
			foreach ($cols as $column_name) {
				foreach ($row as $key => $val) {
					if ($column_name == $key) {
						$insert[] = $val;
					}
				}
			}

			$t->addRow($insert);
		}

		$t->draw();
	}

}

?>