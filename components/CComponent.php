<?php

class CComponent
{
	public function __get($name)
	{
		$getter='get'.$name;
		if(method_exists($this,$getter))
			return $this->$getter();

		throw new Exception("Property ".__CLASS__.".".$name." is not defined.");
	}

	public function __set($name, $value)
	{
		$setter='set'.$name;
		if(method_exists($this,$setter))
			return $this->$setter($value);

		throw new Exception("Property ".__CLASS__.".".$name." is not defined.");
	}

	public function init()
	{
	}

	public function __construct()
	{
		$this->init();
	}
}

?>