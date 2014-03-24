<?php

class CController extends CComponent
{
	public function getId()
	{
		return str_replace('controller', '', strtolower(get_class($this)));
	}

	public function out($msg)
	{
		echo $msg . PHP_EOL;
		Playdb::app()->end();
	}

	public function error($msg, $code = 500)
	{
		throw new Exception($msg, $code);
	}
}

?>