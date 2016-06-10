<?php
namespace Form\Field;

class Email extends Text {
	public static function get_selector() {
		return 'input[type="email"]';
	}
	
	public function validate() {
		$result = parent::validate();
		
		if($result){
			$result = filter_var($this->value(), FILTER_VALIDATE_EMAIL);
			$this->invalidate('Value must be a valid email address `youremail@yourdomain.com` ');
		}
		
		return $result;
	}
}