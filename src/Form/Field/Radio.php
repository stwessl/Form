<?php

namespace Form\Field;

class Radio extends Select {

	public static function get_selector() {
		return 'input[type=radio]';
	}

	public function process_post() {
		//Determine this inputs name
		$name = $this->node->attr('name');


		if (!empty($_POST) &&  $this->get_post_value($name)) {
			$this->value =  $this->get_post_value($name);

			$this->node->each(function($node) {
				if ($node->attr('value') == $this->value()) {
					$node->attr('checked', 'checked');
				}
			});
		}
	}

	public function value($value = false) {
		if ($value !== false) {
			$this->value = $value;

			$this->node->each(function($node) {
				if ($node->attr('value') == $this->value()) {
					$node->attr('checked', 'checked');
				}
			});
		}

		return $this->value ? $this->value : false;
	}

	public function validate() {
		$_required = false;
		$this->node->each(function($node) use(&$_required) {
			if($this->node->attr('required') !== NULL) {
				$_required = true;
			}
		});
		
		if($_required && $this->value() === false) {
			$this->invalidate('Please select an option');
			return false;
		}
		
		return true;
	}

}
