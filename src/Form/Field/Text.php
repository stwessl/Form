<?php
namespace Form\Field;

class Text extends Field  {
	var $value = null, $name;
	
	public function process_post() {
		//Determine this inputs name
		$name = $this->node->attr('name');
		
		
		if(!empty($_POST) && isset($_POST[$name])) {
			$this->value = $_POST[$name];
			$this->node->attr('value', $this->value);
		}
	}

	public function secure_input($key) {
		$this->name = $this->node->attr('name');
		$this->node->attr('name',$key . '-' . $this->node->attr('name'));
	}

	
	public function name() {
		return $this->name;
	}
	
	public function validate() {
//		echo var_dump( $this->node->attr('required') !== false && $this->value() );
		if($this->node->attr('required') !== false && !$this->value() || $this->valid === false ) {
			$this->errors[] = "This field is required"; 
			
			//Mark the input with this error also
			$this->node->attr('title', implode(' | ', $this->errors()));
			
			
			return false;
		} else return true;
	}
	
	


	public function value($value = false) {
		
		if($value !== false) {
			$this->value = $value;
			$this->node->attr('value', $this->value);
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
