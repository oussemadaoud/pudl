<?php




require_once(pudl_file_owner(__DIR__.'/pudlObject.php'));




class pudlList extends pudlObject {


	////////////////////////////////////////////////////////////////////////////
	//CONSTRUCTOR
	////////////////////////////////////////////////////////////////////////////
	public function __construct($data=NULL, $process=false) {
		parent::__construct($data, $process);
	}

}
