<?php


function pudl_array($item) {
	if (is_array($item)) return true;
	return ($item instanceof ArrayAccess);
}



trait pudlHelper {}



class pudlException extends Exception {}



interface pudlId {
	public function pudl_getId($column=true);
}



interface pudlValue {
	public function pudlValue($db, $quote=true);
}
