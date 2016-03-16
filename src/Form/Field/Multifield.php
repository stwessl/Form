<?php

namespace Form\Field;

class Multifield extends Field {

	var $value, $name;

	public function name() {
		return $this->name;
	}

	public function process_post() {
		$this->name = $this->node->attr('multifield');


		//Value will be equal to all data posted to the key that starts with first word in inputs name
		// Find inputs

		$op_val = $this->node->filter('input')->attr('name');
		list($name) = explode('[', $op_val);

		if (!empty($_POST) && isset($_POST[$name])) {

			$this->value = $_POST[$name];
			
			$this->set_data($name, $this->value);
		}
	}

	public function set_data($name, $data) {
		$node = null;
		$first_key = current(array_keys($data));
		$count = 0;
		foreach ($data as $key => $item) {


			if ($count < 1) {
				$nodes = $this->node->filter('[multifield-item]');
				$node = clone $nodes;
				$nodes->remove();
				$nodes = $node;
			} else {
				$nodes = clone $node;
			}

			foreach ($item as $sub_key => $value) {
				if ($node == null) {
					$input = $nodes->filter("[name='{$name}[$key][$sub_key]']");
				} else {
					$input = $nodes->filter("[name='{$name}[$first_key][$sub_key]']");
				}
				$input->attr('name', "{$name}[$count][$sub_key]");
				$input->attr('value', $value);
			}

			$nodes->appendTo($this->node);
			$count++;
		}
	}

	public function secure_input($key) {
		
	}

	public function validate() {
		return true;
	}

	public function value($value = false) {
		if ($value !== false) {
			
			$this->set_data($this->name(), $value);
		}

		return $this->value;
	}

	public static function get_selector() {
		return '[multifield]';
	}

}
