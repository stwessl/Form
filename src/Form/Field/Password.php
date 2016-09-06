<?php
namespace Form\Field;


class Password extends Form\Field\Text {
	
	public static function get_selector() {
		return 'input[type="password"]';
	}
	
	
}