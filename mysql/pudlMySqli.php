<?php


if (!class_exists('pudl',false)) require_once(__DIR__.'/../pudl.php');
require_once(is_owner(__DIR__.'/pudlMyShared.php'));
require_once(is_owner(__DIR__.'/pudlMySqliResult.php'));



class pudlMySqli extends pudlMyShared {



	public static function instance($data, $autoconnect=true) {
		return new pudlMySqli($data, $autoconnect);
	}



	public function connect() {
		$auth = $this->auth();

		pudl_require_extension('mysqli');

		$this->connection = mysqli_init();
		$this->connection->options(MYSQLI_OPT_CONNECT_TIMEOUT,	$auth['timeout']);
		$this->connection->options(MYSQLI_OPT_READ_TIMEOUT,		$auth['timeout']);

		//ATTEMPT TO CREATE A CONNECTION
		$ok = @$this->connection->real_connect(
			(empty($auth['persistent']) ? '' : 'p:') . $auth['server'],
			$auth['username'],
			$auth['password'],
			$auth['database']
		);

		//VERIFY WE CONNECTED OKAY!
		if ($ok) $ok = ($this->connectErrno() === 0);

		//ATTEMPT TO SET UTF8 CHARACTER SET
		if ($ok) $ok = @$this->connection->set_charset('utf8mb4');

		//CONNECTION IS GOOD!
		if (!empty($ok)) {
			$this->connection->options(
				MYSQLI_OPT_READ_TIMEOUT,
				ini_get('mysqlnd.net_read_timeout')
			);

			$this->strict()->timeout($auth);

			return true;
		}


		//CANNOT CONNECT - ERROR OUT
		$error  = "<br />\n";
		$error .= 'Unable to connect to database server "' . $auth['server'];
		$error .= '" with the username: "' . $auth['username'];
		$error .= "\"<br />\nError " . $this->connectErrno() . ': ' . $this->connectError();
		throw new pudlException($this, $error, PUDL_X_CONNECTION);
	}



	public function disconnect($trigger=true) {
		parent::disconnect($trigger);
		if (!$this->connection) return;
		@$this->connection->close();
		$this->connection = NULL;
	}



	public function escape($value) {
		if (!$this->connection) return false;
		return @$this->connection->real_escape_string($value);
	}



	protected function process($query) {
		if (!$this->connection) return new pudlMySqliResult($this);

		$result = @$this->connection->query($query);

		return new pudlMySqliResult($this,
			$result instanceof mysqli_result ?
			$result : NULL
		);
	}



	protected function _query($query) {
		if (!$this->connection) return false;
		return $this->connection->query($query);
	}



	public function insertId() {
		if (!$this->connection) return 0;
		return $this->connection->insert_id;
	}



	public function updated() {
		if (!$this->connection) return 0;
		return $this->connection->affected_rows;
	}



	public function errno() {
		if (!$this->connection) return @mysqli_connect_errno();
		return $this->connection->errno;
	}



	public function error() {
		if (!$this->connection) return @mysqli_connect_error();
		return $this->connection->error;
	}



	public function connectErrno() {
		if (!$this->connection) return @mysqli_connect_errno();
		return $this->connection->connect_errno;
	}



	public function connectError() {
		if (!$this->connection) return @mysqli_connect_error();
		return $this->connection->connect_error;
	}
}
