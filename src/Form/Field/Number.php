<?php
namespace Form\Field;

class Number extends Text {
	public static function get_selector() {
		return 'input[type="number"]';
	}
	
	public function validate() {
		$result = parent::validate();
		
		if($result) {
			if(!is_numeric($this->value()) && $this->value() != '0' ) {
				$result = false;
				$this->invalidate('Value is not a number');
			}
		}
		
		return $result;
	}
	
	
}