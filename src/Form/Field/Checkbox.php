<?php

namespace Form\Field;

class Checkbox extends Input {
	
	public function process_post() {
		//Determine this inputs name
		$name = $this->node->attr('name');
		
		if(isset($_POST[$name])) {
			$this->value(true);
		} else {
			$this->value(false);
		}
	}
	
	public function validate() {
		if($this->node->attr('required') !== NULL && $this->value() == false) {
			$this->invalidate('Please check this checkbox');
			return false;
		}
		
		return true;
	}

	public function value($value = 'none') {
		if($value !== 'none') {
			$this->value = $value;
			if($this->value){
				$this->node->attr('checked', 'checked');
			} else {
				$this->node->removeAttr('checked');
			}
		}
		
		return $this->value;
	}

	public static function get_selector() {
		return 'input[type="checkbox"]';
	}

}