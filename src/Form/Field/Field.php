<?php

namespace Form\Field;

abstract class Field {
	
	/**
	 * 
	 * 
	 * @var \Wa72\HtmlPageDom\HtmlPageCrawler
	 */
	protected $node ,  $valid, $errors = [];
	public $key;

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
			$form = $c->saveHTML();
			$key = md5( $form . '--'.session_id( ) );
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
			
		}
		
	}
	
	/**
	 * Find the parent element with tag;
	 * 
	 * @param type $tag
	 * 
	 * @return \Wa72\HtmlPageDom\HtmlPageCrawler The Parent Node
	 */
	protected function parent_tag($tag, $node = false) {
		
		if($node === false) {
			$node = $this->node;
		}
		
		$parent = $node->parents();
		
		if($parent->nodeName() == '_root' ) {
			return false;
		}
		if( $parent->nodeName() == $tag ) {
			return $parent;
		} else {
			return $this->parent_tag($tag, $parent);
		}
		
	}
	
	
	public function get_post_value($name = false) {
		if(!$name)  {
			$name = $this->name;
		}
		
		$parts = explode('[', $name);

		if (isset($_POST)) {


			$values = $_POST;
			foreach ($parts as $part) {
				$part = rtrim($part, ']');
				if (isset($values[$part])) {
					$values = $values[$part];
				} else {
					return false;
				}
			}
		}

		return $values;
	}
	
}
