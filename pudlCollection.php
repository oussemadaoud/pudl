<?php


require_once('pudlObject.php');



class		pudlCollection
	extends	pudlObject {




	////////////////////////////////////////////////////////////////////////////
	//CONSTRUCTOR
	////////////////////////////////////////////////////////////////////////////
	public function __construct($classname) {
		$this->classname = $classname;
	}




	////////////////////////////////////////////////////////////////////////////
	//FORWARD METHOD CALL TO ALL OBJECTS WITHIN COLLECTION
	////////////////////////////////////////////////////////////////////////////
	public function __call($name, $arguments) {
		$method = new ReflectionMethod($this->classname, $name);
		if ($method->isStatic()) {
			return call_user_func_array(
				[$this->classname, $name],
				$arguments
			);
		}

		$return	= [];
		$list	= $this->_get();

		foreach ($list as $item) {
			$return[] = call_user_func_array(
				[$item, $name],
				$arguments
			);
		}

		return $return;
	}




	////////////////////////////////////////////////////////////////////////////
	//PRIVATE MEMBER VARIABLES
	////////////////////////////////////////////////////////////////////////////
	private $classname;

}
