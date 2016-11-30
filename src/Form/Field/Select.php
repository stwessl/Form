<?php

namespace Form\Field;

class Select extends Field {

	var $name;

	public function name() {
		return $this->name;
	}

	public function process_post() {
		//Determine this inputs name
		$name = $this->node->attr('name');


		if (!empty($_POST) && $this->get_post_value($name)) {
			$this->value = $this->get_post_value($name);
			$this->node->filter('option[value="' . $this->value . '"]')->attr('selected', 'selected');
		}
	}

	public function secure_input($key) {
		$this->name = $this->node->attr('name');
		$this->node->attr('name', $key . '-' . $this->node->attr('name'));
	}

	public function validate() {
		return true;
	}

	public function invalidate($message) {
		parent::invalidate($message);

		$this->node->attr('title', implode(' | ', $this->errors()));
//		$error_node = $this->node->parents()->find('.error[data-field="'.$this->name().'"]');
		$field_id = $this->name();
		$error = $this->node->parents()->filter('span[data-field="' . $field_id . '"]');
		$error->remove();
		$this->node->after('<span class="error" data-field="' . $field_id . '">' . implode(' | ', $this->errors()) . '</span>');
	}

	public function value($value = false) {
		if ($value !== false) {
			$this->value = $value;


			if ($this->node->children()) {
				$this->node->filter('option[value="' . $value . '"]')->attr('selected', 'selected');
			} else {
				$this->node->html('<option value="'.$value.'">'.$value.'</option>');
			}
		}
		
		//check if there is children in this list
		if ($this->node->children()) {
			return $this->value ? $this->value : $this->node->filter('option:first-child')->attr('value');
		} else {
			return $this->value ? $this->value : false;
		}
		
	}

	public static function get_selector() {
		return 'select';
	}

}
