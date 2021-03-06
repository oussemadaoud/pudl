<?php


////////////////////////////////////////////////////////////////////////////////
//SUPPORT FOR CALLBACK TRACKING AND TRIGGERING
////////////////////////////////////////////////////////////////////////////////
trait pudlCallback {




	////////////////////////////////////////////////////////////////////////////
	//REGISTER A CALLBACK FOR A PARTICULAR ACTION
	////////////////////////////////////////////////////////////////////////////
	public function on($action, $callback) {
		if (!is_string($action)) {
			throw new pudlException($this, 'Not a valid callback action');
		}

		if (!is_callable($callback)) {
			throw new pudlException($this, 'Not a valid callback function');
		}

		$this->_callbacks[$action][] = $callback;
	}




	////////////////////////////////////////////////////////////////////////////
	//NOTIFY CALLBACKS OF A PARTICULAR ACTION
	////////////////////////////////////////////////////////////////////////////
	protected function trigger($action) {
		if (empty($this->_callbacks[$action])) return NULL;
		$args	= func_get_args();
		$return	= [];
		foreach ($this->_callbacks[$action] as $item) {
			$return[] = call_user_func_array($item, $args);
		}
		return $return;
	}




	////////////////////////////////////////////////////////////////////////////
	//PRIVATE VARIABLES - LIST OF REGISTERED CALLBACKS
	////////////////////////////////////////////////////////////////////////////
	private $_callbacks = [];

}
