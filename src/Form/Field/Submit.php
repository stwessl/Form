<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Form\Field;

class Submit extends Text {
	
	public function process_post() {
		//Determine this inputs name
		$name = $this->node->attr('name');


		$this->get_post_value($name);

		if (!empty($_POST) && $this->get_post_value($name)) {
			$this->value = $this->get_post_value($name);
		}
	}
	
	public static function get_selector() {
		return '*[name][type="submit"]';
	}
	
	public function disable($disable = true) {
		
	}
	
	public function value($value = false) {


		if ($value !== false) {
			$this->value = $value;
//			$this->node->attr('value', $this->value);
//				echo $this->node->attr('value');exit;
		}

		if ($this->value) {
			return $this->value;
		} else if ($this->node->attr('value')) {
			return $this->node->attr('value');
		} else
			return false;
	}
}