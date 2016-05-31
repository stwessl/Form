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
		
		
		if(!empty($_POST) && isset($_POST[$name])) {
			$this->value = $_POST[$name];
			$this->node->filter('option[value="' . $this->value . '"]')->attr('selected','selected');
		}
	}

	public function secure_input($key) {
		$this->name = $this->node->attr('name');
		$this->node->attr('name',$key . '-' . $this->node->attr('name'));
	}
	
	public function validate() {
		return true;
	}

	public function value($value = false) {
		if($value !== false) {
			$this->value = $value;
			$this->node->filter('option[value="'.$value.'"]')->attr('selected','selected');
		}
		
		return $this->value ? $this->value : $this->node->filter('option:first-child')->attr('value');
	}

	public static function get_selector() {
		return 'select';
	}

}