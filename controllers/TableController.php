<?php
class TableController extends CController
{
	public function actionDesc($args)
	{
		$t = new Table($args['name']);
		$out = new CConsoleTable();

		$heads = array();
		$count = 0;

		// $cols = array('table_name', 'engine', 'table_rows', 'data_length', 'create_time', 'update_time', 'table_comment');

		foreach ($t->fields as $i => $field) {
			if ($count == 0) {
				foreach ($field->attributes as $key => $val) {
					$out->addColumn($key);
				}
			}

			$row = array();
			foreach ($field->attributes as $val) {
				$row[] = $val;
			}

			$out->addRow($row);
			$count ++;
		}

		$out->draw();
	}

	public function actionShowdata($args)
	{
		$t = new Table($args['name']);
		$data = $t->data;

		if ($data) {
			CConsoleTable::fromArray($data);
		} else {
			echo 'Table \''.$args['name'].'\' is empty' . PHP_EOL;
		}
	}

	public function actionFillRandom($args)
	{
		if (!isset($args['name'])) {
			$this->out('Error: table name required.');
		}

		$count = isset($args['count']) && (int)$args['count'] > 0 ? (int)$args['count'] : 5;

		$t = new Table($args['name']);

		for ($i = 0; $i < $count; $i++) {
			$newrow = $t->createNewRow();

			foreach ($newrow->fields as $field) {

				if ($keys = $field->fKInfo) {
					foreach ($keys as $fkinfo) {

						$reftable = new Table($fkinfo['table']);
						$data = $reftable->getData();

						if (!$data) {
							$this->out('Error: can\'t generate values for '.$t->name.' row as of empty referance '.$reftable->name);
						}

						$newrow->setFieldValue($field->name, $data[mt_rand(0, count($data)-1)][$fkinfo['key']]);
					}

				} elseif ($field->isAutoIncrement) {
					// do nothing
				} elseif (in_array($field->data_type, array('varchar', 'text'))) {

					$newrow->setFieldValue($field->name, uniqid('randomtext'));

				} elseif (in_array($field->data_type, array('int', 'float'))) {

					// might be random data generator to concern on data limits
					$newrow->setFieldValue($field->name, mt_rand(0, 10000));

				}
			}

			$newrow->save();
		}
	}
}

?>