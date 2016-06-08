<?php
namespace Form\Field;

class Text extends Input  {
	var $value = null;
	
	public function process_post() {
		//Determine this inputs name
		$name = $this->node->attr('name');
		
		
		if(!empty($_POST) && isset($_POST[$name])) {
			$this->value = $_POST[$name];
			$this->node->attr('value', $this->value);
		}
	}

	public function validate() {
//		echo var_dump( $this->node->attr('required') !== false && $this->value() );
		if($this->node->attr('required') !== false && !$this->value() || $this->valid === false ) {
			
			$this->invalidate('This field is required');
			
			return false;
		} else return true;
	}
	
	public function invalidate($message) {
		parent::invalidate($message);
		
		$this->node->attr('title', implode(' | ', $this->errors()));
		
	}
	
	


	public function value($value = false) {
		if($value !== false) {
			$this->value = $value;
			$this->node->attr('value', $this->value);
//				echo $this->node->attr('value');exit;
		}
		
		if($this->value) {
			return $this->value;
		} else if($this->node->attr('value')) {
			$this->node->attr('value');
		} else return false;
		
		
	}

	public static function get_selector() {
		return 'input[type="text"]';
	}

	
}
