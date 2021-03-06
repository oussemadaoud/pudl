<?php


require_once(is_owner(__DIR__.'/pudlImport.php'));



class			pudlImportCsv
	extends		pudlImport {




	////////////////////////////////////////////////////////////////////////////
	//CONSTRUCTOR
	////////////////////////////////////////////////////////////////////////////
	function __construct($database, $type=false) {
		parent::__construct($database, $type);
	}




	////////////////////////////////////////////////////////////////////////////
	//PARSE THE CSV FILE
	////////////////////////////////////////////////////////////////////////////
	public function parse($filename=false) {
		$this->filename = $filename;

		$handle	= @fopen(realpath($filename), 'rb');


		//VERIFY FILE IS OKAY
		if (!is_resource($handle)) {
			throw new pudlException(
				$this->db,
				'CANNOT OPEN CSV FILE - ' . $filename
			);
		}


		//PARSE HEADERS
		if (pudl_array($this->translate)) {
			$this->header = $this->translate;
		} else if ($this->translate !== true) {
			$tmp = fgetcsv($handle);
			$this->header = is_array($tmp) ? $tmp : [];
		}


		//PARSE BODY
		if ($this->translate === true) {
			while ($item = fgetcsv($handle)) {
				if (!empty($item)) $this[] = $item;
			}

		} else {
			while ($item = fgetcsv($handle)) {
				$data = [];
				foreach ($item as $key => $value) {
					if (!isset($this->header[$key])) continue;
					$data[$this->header[$key]] = $value;
				}
				if (!empty($data)) $this[] = $data;
			}
		}

		fclose($handle);


		//PROCESS THE FILE
		return $this->importing();
	}




	////////////////////////////////////////////////////////////////////////////
	//PARSE THE CSV INSIDE OF ZIP FILE
	////////////////////////////////////////////////////////////////////////////
	public function parseZip($filename) {
		$this->filename = $filename;

		$zip	= @zip_open(realpath($filename));

		//VERIFY FILE IS OKAY
		if (!is_resource($zip)) {
			throw new pudlException(
				$this->db,
				'CANNOT OPEN ZIP FILE - ' . $filename
			);
		}

		//VERIFY WE CAN READ THE FIRST ENTRY
		$entry	= zip_read($zip);
		if (!is_resource($entry)) {
			throw new pudlException(
				$this->db,
				'ZIP FILE IS EMPTY - ' . $filename
			);
		}

		$size	= zip_entry_filesize($entry);
		$data	= zip_entry_read($entry, $size);

		zip_close($zip);

		return $this->parseString($data);
	}





	////////////////////////////////////////////////////////////////////////////
	//PARSE THE CSV STRING
	////////////////////////////////////////////////////////////////////////////
	public function parseString($string) {
		$rows	= explode("\n", $string);


		//PARSE HEADERS
		if (pudl_array($this->translate)) {
			$this->header = $this->translate;
		} else if ($this->translate !== true  &&  count($rows)) {
			$this->header = str_getcsv(reset($rows));
		}


		//PARSE BODY
		if ($this->translate === true) {
			foreach ($rows as $row) {
				if (empty($row)) continue;
				$item = str_getcsv($row);
				if (!empty($item)) $this[] = $item;
			}

		} else {
			foreach ($rows as $row) {
				if (empty($row)) continue;
				$item = str_getcsv($row);
				$data = [];

				foreach ($item as $key => $value) {
					if (!isset($this->header[$key])) continue;
					$data[$this->header[$key]] = $value;
				}

				if (!empty($data)) $this[] = $data;
			}
		}


		//PROCESS THE FILE
		return $this->importing();
	}

}
