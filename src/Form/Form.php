<?php

namespace Form;

use \Wa72\HtmlPageDom\HtmlPageCrawler;

class Form {

	private $string, $c, $fields, $s_key;
	private $types = [
		'text', 'select', 'multifield', 'file', 'number', 'email', 'textarea', 'radio', 'checkbox', 'password', 'submit'
	];
	var $captcha_selector = '', $secret;

	public function __construct($form, $s_key = false, $selector = false) {
		$this->string = $form;


		//Load html in html parser
		$this->c = HtmlPageCrawler::create($this->string);

		if ($s_key) {
			$this->s_key = $s_key;
		} else {
			$this->s_key = md5($this->c->saveHTML());
		}

		// Look for all standerd inputs and special input divs
		$this->assemble_fields();
	}

	/**
	 * Adds google captcha to form with key
	 * @param type $key
	 */
	function addRecaptcha($site_key, $secret_key) {

		//Add script to form to load the source code for file
		$result = $this->c->prepend("<script src='https://www.google.com/recaptcha/api.js'></script>");

		//Add button to form to show on display
		if (empty($this->captcha_selector)) {
			$this->c->filter('form')->first()->append('<div class="g-recaptcha" data-sitekey="'
					. $site_key
					. '"></div>');
		} else {
			$this->c->filter($this->captcha_selector)->first()->append('<div class="g-recaptcha" data-sitekey="'
					. $site_key
					. '"></div>');
		}
		//Register in class so that it can be used to validate posts
		$this->secret = $secret_key;
	}

	public function get_fields() {
		$fields = [];
		foreach ($this->fields as $field)
			$fields[] = $field->name();
		$fields = array_unique($fields);
		return $fields;
	}

	public function validate() {
		$valid = true;
		if ($this->secret) { // Google Recaptcha activated. Check that post is legitmate
			$captcha = $_POST['g-recaptcha-response'];
			$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$this->secret&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
			$obj = json_decode($response);
			if ($obj->success == false) {
				return false;
			}
		}
		
		
		foreach ($this->fields as &$field) {
			if ($valid & !$field->validate() || $field->errors() != []) {
				$valid = false;
			}
		}

		return $valid;
	}

	public function errors() {
		$errors = [];
		foreach ($this->fields as $field) {
			$error = $field->errors();
			if (!empty($error)) {
				$errors[$field->name()] = $field->errors();
			}
		}

		return $errors;
	}

	public function invalidate($name, $message) {
		foreach ($this->fields as $field) { /* @var $field Form\Field\Field */

			if ($field->name() == $name) {
				$field->invalidate($message);
			}
		}
	}

	public function is_posted() { //@todo implement form id for to see submissions better
		//Get the key for this form
		$key = $this->s_key;

		$ALL_FIELDS = array_merge($_POST, $_FILES);


		foreach (array_keys($ALL_FIELDS) as $post_value) {
			if (strpos($post_value, $key) !== false) {
				return true;
			}
		}

		return false;
	}

//	public function is_posted() { //@todo implement form id for to see submissions better
//		if (!empty($_POST)) {
//			return true;
//		} else {
//			return false;
//		}
//	}

	function flatten($array, $prefix = '') {
		$result = array();
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$result = $result + $this->flatten($value, $prefix . $key . '|');
			} else {
				$result[$prefix . $key] = $value;
			}
		}

		return $result;
	}

	public function data($data = []) {
//		$data = [];


		if ($data !== false) { // Setter function
			$fdata = [];
			foreach ($this->flatten($data) as $key => $value) {
				$key = explode('|', $key);
				$first = array_shift($key);

				if (count($key) > 0) {
					$first .= '[' . implode('][', $key) . ']';
				}

				$name = $first;

				foreach ($this->fields as $field) { /* @var $field Form\Field\Field */

					if ($field->name() == $name) {

						$field->value($value);
					}
				}
			}
		}


		foreach ($this->fields as $field) { /* @var $field Form\Field\Field */ // Getter Function
			$parts = explode('[', $field->name());


			if (count($parts) > 1) {

				$mdata = [];
				$ref = &$data;
				$i = 0;

				foreach ($parts as $key => $part) {

					$part = rtrim($part, ']');

					if ($key + 1 == count($parts)) { // last element
						$ref[$part] = $field->value();
					} else {
						if (!isset($ref[$part])) {
							$ref[$part] = [];
						}
						$ref = &$data[$part];
					}
				}

				$data = array_merge($data, $mdata);

				continue;
			} else {
				if ($field->value() !== false) {
					$data[$field->name()] = $field->value();
				}
			}
		}

		return $data;
	}
	
	/**
	 * Removed unique id from fieldnames;
	 * @param type $post
	 */
	function clean_post($post = false) {
		if($post === false) {
			$post = $_POST;
		}
		
		foreach($post as $field => $value) {
			if( is_numeric($field) || strpos($field, $this->s_key) !== false) {
				$key = str_replace($this->s_key.'-', '', $field);
				
				if(is_array($value)) {
					$post[$key] = $value;
				} else {
					$post[$key] = $value; 
				}
				
				
				unset($post[$field]);
			}
			
			
		}
		
		return $post;
	}

	public function set_data($data) {
		return $this->data($data);
	}

	public function parse() {
		return $this->c->saveHTML();
	}

	public function assemble_fields() {
		foreach ($this->types as $type) {
			//Build the classname
			$class = "Form\Field\\" . ucfirst($type); /* @var $class Field\Field */
			//Exception if a declared class type has no matching class
			if (!class_exists($class))
				throw new Exception('Field Type Declared in class that does not exist');

			$type_selector = $class::get_selector();

			//Find nodes with this selector and contruct an field instance for that node
			$inputs = $this->c->filter($type_selector);

			$in_fields = &$this->fields;
			$key = $this->s_key;

			$inputs->each(function($node) use (&$in_fields, $class, $key) { /* @var $node \Wa72\HtmlPageDom\HtmlPageCrawler */

				//check that this field is not in a exluded parent

				if ($this->parent_check($node->parents())) {

					$field = new $class($node, $key);

					$in_fields[] = $field;
				}
			});
		}
	}

	public function parent_check(\Wa72\HtmlPageDom\HtmlPageCrawler $node, $count = 0) {

		if ($node->nodeName() == '_root') {
			return true;
		}

		$exclude = $node->attr('exclude');

		if (!$exclude) {
			$count++;
			return $this->parent_check($node->parents(), $count);
		} else {
			$node->attr('found-ex', 'FOUND');
			return false;
		}
		return true;
	}

	function disable($name, $disable = true) {
		foreach ($this->fields as $field) { /* @var $field Form\Field\Field */

			if ($field->name() == $name) {
				$field->disable();
			}
		}
	}

}
