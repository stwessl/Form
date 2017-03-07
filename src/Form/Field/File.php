<?php

namespace Form\Field;

use Form\File\Upload;

class File extends Input {
	var $file = null;

	public function process_post() {
		//Determine this inputs name
		$name = $this->node->attr('name');
		
		$file = new Upload($name);
		
		//Add the enctype to the parent form
		$form = $this->parent_tag('form');
		if($form->attr('enctype') == false) {
			$form->attr('enctype','multipart/form-data');
		}
		
		$this->file = $file;
		
	}

	
	public function validate() {
		$valid = true;
		if($this->node->attr('required') && !$this->file->uploaded()) {
//			Set the error code into the message
			$this->errors[] = "Code for upload issue: " . $this->file->error;
			
			$valid = false;
		}
		
		if(  $this->file->uploaded()  && $this->node->attr('filetype') ) { // Check if the filetype is matching the file
			$types = explode(';', $this->node->attr('filetype'));
			
			if(!in_array($this->file->type, $types)) {
				$this->errors[] = "File not of correct type. Please upload (".  implode(', ', $types). ")";
				$valid  = false;
			}
		}
		
		
		$this->errors = array_unique($this->errors);
		$this->node->attr('title', implode(' | ', $this->errors()));
		
		return $valid;
	}
	
	/**
	 * 
	 * @param type $value
	 * @return Form\File\Upload
	 */
	public function value($value = false) {
		if($value) {
			$this->file->src($value);
			$this->node->after('<div class="form-group"><img src="'. $this->file->src() .'" style="max-height: 100px"/></div>');
//			echo '<img src="'. $this->file->src() .'" />';exit;
		}
		return $this->file;
	}

	public static function get_selector() {
		return 'input[type="file"]';
	}

}