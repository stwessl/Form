<?php

namespace Form\File;

class Upload {

	var $name = null;
	private $src_data;

	public function __construct($source) {
		$this->name = $source;
	}

	public function uploaded() {
		//Check if the file was uploaded
		if (isset($_FILES[$this->name]) && $_FILES[$this->name]['error'] == UPLOAD_ERR_OK) {
			return true;
		} else
			return false;
	}

	public function __get($name) {
		if (isset($_FILES[$this->name][$name])) {
			return $_FILES[$this->name][$name];
		} else if (method_exists($this, $name)) {
			return $this->$name();
		} else {
			return null;
		}
	}

	public function __set($name, $value) {
		if (isset($_FILES[$this->name][$name])) {
			return $_FILES[$this->name][$name] = $value;
		} else if (method_exists($this, $name)) {
			return $this->$name($value);
		} else {
			return null;
		}
	}

	public function move($location) {
		if (!is_dir(dirname($location))) {
			mkdir(dirname($location), 0777, true);
		}
		return move_uploaded_file($this->tmp_name, $location);
	}

	public function src($data = false) {
		if ($data) {
			if (is_readable($data)) {
				$data = $this->data_uri($data);
			}

			$this->src_data = $data;
		}

		if (isset($this->tmp_name)) {
			return $this->src_data = $this->data_uri($this->tmp_name, $this->type);
		} else {
			return $this->src_data;
		}
	}

	function data_uri($file, $mime = false) {

		if ($mime == false) {
			$mime = $this->mime($file);
		}

		$contents = file_get_contents($file);
		$base64 = base64_encode($contents);
		return ('data:' . $mime . ';base64,' . $base64);
	}

	function mime($file_location) {
		$mimepath = '/usr/share/magic'; // may differ depending on your machine
		// try /usr/share/file/magic if it doesn't work
		$mime = finfo_open(FILEINFO_MIME);

		if ($mime === FALSE) {
			throw new Exception('Unable to open finfo');
		}
		$filetype = finfo_file($mime, $file_location);
		finfo_close($mime);
		if ($filetype === FALSE) {
			throw new Exception('Unable to recognise filetype');
		}
		
		return $filetype;
	}

}
