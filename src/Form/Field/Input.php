<?php
namespace Form\Field;

abstract class Input extends Field {
	var $name = null;
	public function name() {
		return $this->name;
	}

	public function process_post() {
		$this->name = $this->node->attr('name');
	}
	
	public function secure_input($key) {
		$this->name = $this->node->attr('name');
		$this->node->attr('name',$key . '-' . $this->node->attr('name'));
	}
}