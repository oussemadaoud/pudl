<?php


trait pudlUpdate {


	abstract public function updated();



	public function update($table, $data, $clause, $limit=false, $offset=false) {
		$query  = 'UPDATE ';
		$query .= $this->_table($table);
		$query .= ' SET ';
		$query .= $this->_update($data);
		$query .= $this->_clause($clause);
		$query .= $this->_limit($limit, $offset);
		return $this($query);
	}



	public function updateIn($table, $data, $field, $in, $limit=false, $offset=false) {
		if (pudl_array($in)) $in = implode(',', $in);
		$query  = 'UPDATE ';
		$query .= $this->_table($table);
		$query .= ' SET ';
		$query .= $this->_update($data);
		$query .= ' WHERE (';
		$query .= $this->identifiers($field);
		$query .= $this->_clause($in, 'IN');
		$query .= ')';
		$query .= $this->_limit($limit, $offset);
		return $this($query);
	}



	public function updateId($table, $data, $column, $id=false) {
		return $this->update($table, $data, $this->_clauseId($column,$id));
	}



	public function updateField($table, $field, $value, $clause) {
		return $this->update($table, [$field=>$value], $clause);
	}



	public function updateFieldId($table, $field, $value, $column, $id=false) {
		return $this->update($table, [$field=>$value], $this->_clauseId($column,$id));
	}



	public function updateCount($table_update, $field, $clause_update, $table_select, $clause_select=true) {
		if ($clause_select === true) $clause_select = $clause_update;
		return $this->update($table_update, [
			$field => $this->string()->count($table_select, $clause_select)
		], $clause_update);
	}



	public function increment($table, $col, $clause, $amount=1, $limit=false, $offset=false) {
		switch (true) {
			case $amount === NAN:
			case $amount === INF:
			case $amount === -INF:
			case is_bool($amount):
			case is_null($amount):
			case pudl_array($amount):
				throw new pudlException('Invalid value for increment: ' . gettype($amount));
			return false;
		}

		$value = $this->_value($amount);

		switch (true) {
			case is_int($value)		&&  $value >= 0:
			case is_float($value)	&&  $value >= 0:
			case !is_int($value)	&&  !is_float($value):
				$value = '+' . (string)$value;
		}

		return $this('UPDATE '
			. $this->_table($table)
			. ' SET '	. $this->identifiers($col)
			. '='		. $this->identifiers($col)
			. $value
			. $this->_clause($clause)
			. $this->_limit($limit, $offset)
		);
	}



	public function incrementId($table, $col, $column, $id=false, $amount=1) {
		return $this->increment($table, $col, $this->_clauseId($column,$id), $amount);
	}

}
