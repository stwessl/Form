<?php
namespace Form\Field;

class Number extends Text {
	public static function get_selector() {
		return 'input[type="number"]';
	}
}