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

}