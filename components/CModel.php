<?php
/**
* @property PDO $db
*/
class CModel extends CComponent
{
	protected function getDb()
	{
		return Playdb::app()->getDb();
	}
}
?>