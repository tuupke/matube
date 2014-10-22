<?php

class Form {
	private $html;
	
	public function __construct($id){

	}

	public function addInput($textName, $inputName, $value = ""){
		$this->html.="<div class='form_row'><div>$textName<div></div><input name='$inputName' value='$value' /></div></div>";
		return $this;
	}

	public function addSelect($textName, $inputName, $vals = array(), $default = false){
		$v = implode('', array_map(function ($v, $k) {global $default; return "<option value='$k' ".($default==$k?"selected":"").">$v</option>";}, $vals, array_keys($vals)));
		if(!empty($default)){
			$v = str_replace(">$default"," selected>$default", $v);
		}
		$this->html.="<div class='form_row'><div class='form_row_name'>$textName</div><div><select name='$inputName'>$v</select></div></div>";
		return $this;
	}

	public function addCheckbox($textName, $inputName, $value = false){
		$this->html.="<div class='form_row'><div class='form_row_name'>$textName</div><div><input type='checkbox' name='$inputName' ".(($value)?"checked":"")."/></div></div>";
		return $this;
	}

	public function addTextArea($textName, $inputName, $value = "", $rows = 10, $cols = 30){
		$this->html.="<div class='form_row'><div class='form_row_name'>$textName</div><div><textarea name='$inputName' rows=$rows cols=$cols id='$inputName'>$value</textarea></div></div>";
		return $this;
	}

	public function addRadioGroup($textName, $inputName, $vals = array(), $default = false){
		$v = implode('', array_map(function ($v, $k) { global $default,$inputName; return "<div><input type='radio' value='$k' name='$inputName' ".($default==$k?"checked":"")."/> $v</div>";}, $vals, array_keys($vals)));

		$this->html.="<div class='form_row'><div class='form_row_name'>$textName</div><div></div>$v</div>";
		return $this;
	}

	public function __toString(){
		return $this->html;
	}

}


?>