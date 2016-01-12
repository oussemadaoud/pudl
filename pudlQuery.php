<?php

trait pudlQuery {



	public function escape($value) {
		switch (true) {
			case is_int($value):
			case is_float($value):
			case is_null($value):
			case is_bool($value):
				return $value;
		}

		return str_replace(
			['\\',		"\0",	"\x08",	"\x26",	"'",	'"',	"\n",	"\r",	"\t"],
			['\\\\',	'\0',	'\b',	'\Z',	"\'",	'\"',	'\n',	'\r',	'\t'],
			(string)$value
		);
	}



	public function likeEscape($value) {
		return addcslashes($this->escape($value), '%_');
	}



	public function setEscape($value) {
		return str_replace(',', '', $value);
	}



	protected function _cache() {
		return '';
	}



	protected function _value($value, $quote=true, $isnull=false) {
		if (is_int($value)  ||  is_float($value))
			return $value;

		if (is_string($value))
			return $quote ? "'".$this->escape($value)."'" : $value;

		if (is_null($value))
			return $isnull ? ' IS NULL' : 'NULL';

		if (is_bool($value))
			return $value ? 'TRUE' : 'FALSE';

		if ($value instanceof pudlFunction)
			return $this->_function($value);

		if ($value instanceof pudlStringResult)
			return (string)$value;

		if ($value instanceof pudlLike  &&  !is_object($value->value))
			return "'" . $value->left . $this->likeEscape($value->value) . $value->right . "'";

		if ($value instanceof pudlLike)
			return "CONCAT('" . $value->left . "'," . $this->_value($value->value) . ",'" . $value->right . "')";

		if ($value instanceof pudlRegexp)
			return $this->_regexp($value->value);

		if ($value instanceof pudlSet)
			return '(' . $this->_inSet($value->value) . ')';

		if ($value instanceof pudlAppendSet)
			return false;

		if ($value instanceof pudlRemoveSet)
			return false;

		if ($value instanceof pudlBetween)
			return $this->_value($value->value[0], $quote) .
				' AND ' . $this->_value($value->value[1], $quote);

		if ($value instanceof pudlColumn)
			return $this->identifiers($value->column);

		if ($value instanceof pudlEquals  &&  is_array($value->value))
			return '(' . $this->_inSet($value->value) . ')';

		if ($value instanceof pudlEquals)
			return $this->_value($value->value, $quote);

		if ($value instanceof pudlRaw)
			return $value->value;

		return false;
	}



	private function _regexp($value) {
		$query = '';
		if (!is_array($value)) $value = [$value];
		foreach ($value as $item) {
			$query .= is_string($item) ?
				$this->escape(preg_quote($item)) :
				$this->_value($item, false);
		}
		return "'".$query."'";
	}



	protected function _top($limit) {
		if (!$this->top) return '';
		if (is_array($limit)) $limit = reset($limit);
		if ($limit === false) return '';
		return 'TOP ' . ((int) $limit) . ' ';
	}



	protected function _column($column) {
		if (!is_array($column)) {
			switch ($column) {
				case '':
				case '*':
				case null:
				case false:
					return '*';
			}
			return $this->_value($column, false);
		}

		$query = '';
		foreach ($column as $key => $value) {
			if (strlen($query)) $query .= ', ';
			if (is_string($key)) {
				if (is_string($value)) {
					$query .= $this->identifiers($value);
				} else {
					$query .= $this->_value($value, true);
				}
				$query .= ' AS ' . $this->identifier($key);
			} else {
				$query .= $this->_value($value, is_string($key));
			}
		}

		return $query;
	}



	public function identifier($identifier) {
		return $this->identifier . str_replace(
			$this->identifier,
			$this->identifier.$this->identifier,
			$identifier
		) . $this->identifier;
	}



	public function identifiers($identifiers, $prefix=false) {
		if ($identifiers === false) return '';

		$list = explode('.', $identifiers);

		foreach ($list as &$item) $item = trim($item);
		unset($item);

		if ($prefix) {
			$table = array_pop($list);
			if (substr($table, 0, 5) === 'pudl_') {
				$table = $this->prefix . substr($table, 5);
			}
			$list[] = $table;
		}

		foreach ($list as &$item) $item = $this->identifier($item);
		unset($item);

		return implode('.', $list);
	}



	public function table($table) {
		return $this->identifiers($table, true);
	}



	protected function _table($table, $prefix=true) {
		return $this->identifiers($table, $prefix);
	}



	protected function _tables($table) {
		if ($table === false) return;
		if (is_string($table)) return ' FROM ' . $this->table($table);

		if (!is_array($table)) trigger_error(
			'Invalid data type for table: ' . gettype($value),
			E_USER_ERROR
		);

		$query = '';
		foreach ($table as $key => $value) {
			if (strlen($query)) $query .= ', ';

			if (!is_array($value)) {
				if ($value instanceof pudlStringResult) {
					$query .= (string) $value;
				} else {
					$query .= $this->table($value);
				}
				if (is_string($key)) $query .= ' AS ' . $this->identifier($key);

			} else {
				$query .= $this->table(reset($value));
				if (is_string($key)) $query .= ' AS ' . $this->identifier($key);
				foreach ($value as $join) {
					if (!empty($join['join'])) {
						$query .= $this->_joinTable($join['join'], false);
					} else if (!empty($join['cross'])) {
						$query .= $this->_joinTable($join['cross'], 'CROSS');
					} else if (!empty($join['left'])) {
						$query .= $this->_joinTable($join['left'], 'LEFT');
					} else if (!empty($join['right'])) {
						$query .= $this->_joinTable($join['right'], 'RIGHT');
					} else if (!empty($join['natural'])) {
						$query .= $this->_joinTable($join['natural'], 'NATURAL');
					} else if (!empty($join['inner'])) {
						$query .= $this->_joinTable($join['inner'], 'INNER');
					} else if (!empty($join['outer'])) {
						$query .= $this->_joinTable($join['outer'], 'OUTER');
					} else if (!empty($join['hack'])) {
						$query .= ' LEFT JOIN (' . $join['hack'] . ')';
					}

					if (!empty($join['clause'])) {
						$query .= $this->_clause($join['clause'], 'ON');
					} else if (!empty($join['on'])) {
						$query .= $this->_clause($join['on'], 'ON');
					} else if (!empty($join['using'])) {
						$query .= $this->_joinUsing($join['using']);
					}
				}
			}
		}

		return ' FROM ' . $query;
	}



	protected function _clause($clause, $type='WHERE') {
		if ($clause === false)	return '';
		if ($clause instanceof pudlStringResult) return (string) $clause;
		if (is_array($clause))	return ' ' . $type . ' (' . $this->_clauseRecurse($clause) .')';
		if (is_object($clause))	return ' ' . $type . ' (' . $this->_clauseRecurse($clause) .')';
		return ' ' . $type . ' (' . $clause . ')';
	}



	protected function _order($order) {
		if (is_string($order)) return ' ORDER BY ' . $order;
		if (!is_array($order)  &&  !is_object($order)) return '';
		if ($order instanceof pudlStringResult) return (string) $order;
		if (empty($order)) return '';
		return ' ORDER BY ' . $this->_clauseRecurse($order,', ');
	}



	protected function _group($group) {
		if ($group === false)	return '';
		if ($group instanceof pudlStringResult) return (string) $group;
		if (is_array($group))	return ' GROUP BY ' . $this->_clauseRecurse($group,', ');
		if (is_object($group))	return ' GROUP BY ' . $this->_clauseRecurse($group,', ');
		return ' GROUP BY ' . $group;
	}



	private function _clauseRecurse($clause, $joiner=' AND ') {
		static $depth = 0;
		if ($depth > 31) {
			trigger_error('Recursion limit reached', E_USER_ERROR);
			return '';
		}
		$depth++;

		if (is_object($clause)) {
			$traits = class_uses($clause, false);
			if (!empty($traits['pudlHelper'])) {
				return $this->_value($clause);
			}
		}

		$query = '';
		foreach ($clause as $key => $value) {
			if (strlen($query)) $query .= $joiner;

			if (is_string($key)) {
				$query .= $this->identifiers($key);
				$query .= $this->_clauseEquals($value);
				if (is_array($value)) continue;

			} else if ($value instanceof pudlColumn  &&  $value->args) {
				$key	 = '';
				if (is_string($value->column)) {
					$query	.= $this->identifiers($value->column);
				} else {
					$query	.= $this->_value($value->column);
				}
				$value	 = $value->value;
				$query	.= $this->_clauseEquals($value);
				if (is_array($value)) continue;
			}

			$new = $this->_value($value, is_string($key), is_string($key));

			if ($new !== false) {
				$query .= $new;

			} else if ((is_array($value)  ||  is_object($value))  &&  $joiner === ' AND ') {
				$query .= '(' . $this->_clauseRecurse($value, ' OR ') . ')';

			} else if ((is_array($value)  ||  is_object($value))  &&  $joiner === ' OR ') {
				$query .= '(' . $this->_clauseRecurse($value, ' AND ') . ')';

			} else {
				trigger_error(
					'Invalid data type for clause: ' . gettype($value),
					E_USER_ERROR
				);
			}
		}

		$depth--;
		return $query;
	}



	private function _clauseEquals($value) {
		if ($value instanceof pudlEquals) {
			if (is_array($value->value)) {
				if ($value->equals == '=')	return ' IN ';
				if ($value->equals == '!=')	return ' NOT IN ';
			}
			return $value->equals;
		}

		if ($value instanceof pudlStringResult) return $value->type;

		if (is_array($value)) return ' IN (' . $this->_inSet($value) . ')';

		if (!is_null($value)) return '=';

		return '';
	}



	protected function _clauseId($column, $id) {
		if (is_array($id)) {
			$list	= explode('.', $column);
			$id		= $id[end($list)];

		} else if (is_object($id)) {
			$traits = class_uses($id, false);
			if (empty($traits['pudlHelper'])) {
				$list	= explode('.', $column);
				$id		= $id->{end($list)};
			}
		}

		return [$column => $id];
	}



	protected function _inSet($value) {
		$query = '';
		foreach ($value as $item) {
			if (strlen($query)) $query .= ', ';
			$query .= $this->_value( is_array($item) ? reset($item) : $item );
		}
		return $query;
	}



	protected function _limit($limit, $offset=false) {
		if (!$this->limit) return '';

		if (is_array($limit)) {
			if (count($limit) > 1) $offset = $limit[1];
			$limit = reset($limit);
		}

		if ($limit !== false  &&  $offset === false)
			return ' LIMIT ' . ((int)$limit);

		if ($limit !== false  &&  $offset !== false)
			return ' LIMIT ' . ((int)$offset) . ',' . ((int)$limit);

		if ($limit === false  &&  $offset !== false)
			return ' LIMIT ' . ((int)$offset) . ',18446744073709551615';

		return '';
	}



	protected function _lock($lock) {
		if ($lock === 'SHARE')	return ' LOCK IN SHARE MODE';
		if ($lock === 'UPDATE')	return ' FOR UPDATE';
		if ($lock === true)		return ' FOR UPDATE';
		return '';
	}



	protected function _lockTable($table, $lock) {
		if (!is_array($table)) return $this->table($table) . ' ' . $lock;

		$query = '';
		foreach ($table as $key => $value) {
			if (is_array($value)) continue;
			if (strlen($query)) $query .= ', ';
			$query .= $this->table($value);
			if (is_string($key)) $query .= ' ' . $this->table($key);
			$query .= ' ' . $lock;
		}
		return $query;
	}



	protected function _union($type='') {
		if ($type !== 'ALL'  &&  $type !== 'DISTINCT') $type = '';
		return '(' . implode(") UNION $type (", $this->union) . ')';
	}



	protected function _joinUsing($using) {
		if ($using === false)	return '';
		if (!is_array($using))	return ' USING (' . $this->identifiers($using) . ')';
		if (!count($using))	return '';

		$query = '';
		foreach ($using as $item) {
			if (strlen($query)) $query .= ', ';
			$query .= $this->identifiers($item);
		}
		return ' USING (' . $query . ')';
	}



	protected function _joinTable($join, $type='LEFT') {
		$query = (empty($type) ? '' : ' '.$type) . ' JOIN ';

		if (is_string($join)) {
			return $query . '(' . $this->table($join) . ')';

		} else if (is_array($join)) {
			$value = reset($join);
			if ($value instanceof pudlStringResult) {
				$query .= (string)$value;
			} else {
				$query .= $this->table($value);
			}

			$alias = key($join);
			if (is_string($alias)) $query .= ' AS ' . $this->identifier($alias);
			return $query;
		}

		trigger_error(
			'Invalid data type for join: ' .
			(gettype($join)==='object'?get_class($join):gettype($join)),
			E_USER_ERROR
		);
	}



	protected function _update($data) {
		if (!is_array($data)  &&  !is_object($data)) return $data;

		$query = '';

		foreach ($data as $column => $value) {
			if (strlen($query)) $query .= ', ';

			if (is_int($column)) {
				$query .= $value;
				continue;
			}

			$query .= $this->identifier($column) . '=';

			if ($value instanceof pudlFunction  &&  isset($value->__INCREMENT)) {
				$query .= $this->identifier($column);
				$query .= '+' . $this->_value(reset($value->__INCREMENT));

			} else if ($value instanceof pudlAppendSet) {
				$query .= 'CONCAT_WS(\',\', ' .
					$this->identifier($column) . ', ' .
					$this->setEscape($this->_value($value->value)) . ')';

			} else if ($value instanceof pudlStringResult) {
				$query .= '(' . (string)$value . ')';

			} else if ($value instanceof pudlRemoveSet) {
				$query .= 'REPLACE(CONCAT(\',\', ' .
					$this->identifier($column) . ', \',\'), \',' .
					$this->setEscape($this->_value($value->value, false)) . ',\', \',\')';

			} else {
				$query .= $this->_columnData($value);
			}
		}

		return $query;
	}



	protected function _columnData($value) {
		$new = $this->_value($value);
		if ($new !== false) return $new;

		if (is_array($value)  ||  is_object($value)) {
			if (empty($value)) return 'NULL';
			return 'COLUMN_CREATE(' . $this->_dynamic($value) . ')';
		}

		trigger_error(
			'Invalid data type for column: ' .
			(gettype($value)==='object'?get_class($value):gettype($value)),
			E_USER_ERROR
		);
	}



	protected function _function($data) {
		foreach ($data as $property => $value) {
			$query	= '';
			foreach ($value as $item) {
				if (strlen($query)) $query .= ', ';
				$query .= $this->_value($item);
			}
			return ltrim($property, '_') . '(' . $query . ')';
		}

		trigger_error('Invalid pudlFunction', E_USER_ERROR);
	}



	protected function _dynamic($data) {
		static $depth = 0;
		if ($depth > 31) {
			trigger_error('Recursion limit reached', E_USER_ERROR);
			return '';
		}
		$depth++;

		$query = '';
		foreach ($data as $property => $value) {
			if (strlen($query)) $query .= ', ';
			$query .= $this->_value($property) . ',' . $this->_columnData($value);
		}

		$depth--;
		return $query;
	}



	public function prefixColumns($table, $col=false, $unprefixed=true) {
		$joiners = array(
			'join', 'cross', 'left', 'right',
			'natural', 'inner', 'outer', 'hack',
		);

		$prefix = array();

		if (!is_array($table)) return false;

		foreach ($table as $key => $val) {
			if (is_array($val)) {
				foreach ($val as $subtable) {
					if (is_array($subtable)) {
						foreach ($subtable as $joinkey => $jointable) {
							if (in_array($joinkey, $joiners)) {
								foreach ($jointable as $subkey => $subname) {
									$fields = $this->listFields($subname);
									foreach ($fields as $field) {
										if (!isset($prefix[$field['Field']])) $prefix[$field['Field']] = $subkey;
									}
								}
							}
						}
					} else {
						$fields = $this->listFields($subtable);
						foreach ($fields as $field) {
							if (!isset($prefix[$field['Field']])) $prefix[$field['Field']] = $key;
						}
					}
				}
			} else {
				$fields = $this->listFields($val);
				foreach ($fields as $field) {
					if (!isset($prefix[$field['Field']])) $prefix[$field['Field']] = $key;
				}
			}
		}

		if ($col === false) return $prefix;

		$column = array();
		foreach ($col as $val) {
			if (isset($prefix[$val])) {
				$column[] = $prefix[$val] . '.' . $val;
			} else if ($unprefixed) {
				$column[] = $val;
			}
		}
		return $column;
	}



	protected $identifier	= '"';
	protected $limit		= true;
	protected $top			= false;
	protected $union		= false;
	protected $prefix		= false;
}
