<?php

namespace Form;

use \Wa72\HtmlPageDom\HtmlPageCrawler;

class Form {

	private $string, $c, $fields, $s_key;
	private $types = [
		'text', 'select', 'multifield', 'file', 'number', 'email', 'textarea', 'radio', 'checkbox', 'password'
	];

	public function __construct( $form, $s_key = false) {
		$this->string = $form;
		$this->s_key = $s_key;
		//Load html in html parser
		$this->c = HtmlPageCrawler::create($this->string);

		// Look for all standerd inputs and special input divs
		$this->assemble_fields();
	}

	public function validate() {
		$valid = true;
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
			$errors[$field->name()] = $field->errors();
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
		if (!empty($_POST)) {
			return true;
		} else {
			return false;
		}
	}

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
			$fieldname = $name;

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
						if( !isset( $ref[$part] ) ) {
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

}
