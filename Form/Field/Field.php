<?php

namespace Form\Field;

abstract class Field {
	
	/**
	 * 
	 * 
	 * @var \Wa72\HtmlPageDom\HtmlPageCrawler
	 */
	protected $node , $key, $valid, $errors = [];

	abstract static function get_selector();

	abstract function validate();
	public function invalidate($message) {
		$this->errors[] = $message;
		$this->valid = false;
	}

	abstract function name();
	abstract function value($value = false);

	public function __construct(\Wa72\HtmlPageDom\HtmlPageCrawler &$c, $key = false) {
		$this->node = $c;
		
		if($key === false) {
			$key = md5( session_id( ) );
		}
		
		$this->key = $key;
		
		$this->secure_input($key);
		$this->process_post();
	}

	abstract function secure_input($key);

	abstract function process_post();
	
	function errors() {
		return $this->errors;
	}
	/**
	 * Translate a field name to a post value
	 * @param type $name
	 */
	protected function get_post($name){
		$name = str_replace(['[','][',']'], '---', $name);
		$parts = explode('---', $name);
		$part_filtered = [];
		foreach($parts as $part)
		{
			if($part !== "") $part_filtered[] = $part;
		}
		
		$data = $_POST;
		$first_key = array_shift($part_filtered);
		while($first_key) {
			$data = $data[$first_key];
			$first_key = array_shift($part_filtered);
			debug($first_key);
		}
		var_dump($data); exit;
	}
}
