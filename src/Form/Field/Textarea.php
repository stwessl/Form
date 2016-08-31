<?php

namespace Form\Field;

class Textarea extends Text {

	public function process_post() {
		//Determine this inputs name
		$name = $this->node->attr('name');

		if (!empty($_POST) && $this->get_post_value($name)) {
			$this->value = $this->get_post_value($name);
			$this->node->text($this->value);
		}
	}

	public function value($value = false) {
		if ($value !== false) {
			$this->value = $value;
			$this->node->text($value);
//				echo $this->node->attr('value');exit;
		}

		if ($this->value) {
			return $this->value;
		} else if ($this->node->text()) {
			$this->node->text();
		} else
			return false;
	}

	public static function get_selector() {
		return 'textarea';
	}

}
