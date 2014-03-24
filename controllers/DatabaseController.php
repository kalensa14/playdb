<?php

class DatabaseController extends CController
{
	function actionShowStat()
	{
		$d = new Database();
		$stats = $d->stats;

		$cols = array('table_name', 'engine', 'table_rows', 'data_length', 'create_time', 'update_time', 'table_comment');
		$t = new CConsoleTable();

		foreach ($cols as $one)
			$t->addColumn(ucfirst(str_replace('_', ' ', $one)));

		foreach ($stats as $row) {
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