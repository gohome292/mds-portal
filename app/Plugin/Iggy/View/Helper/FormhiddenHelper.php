<?php
class FormhiddenHelper extends AppHelper {
	var $helpers = array('Form');
	function hiddenVars() {
		$ret = "";
		foreach ($this->request->data as $key1 => $val1){
			foreach ($val1 as $key2 => $val2) {
				if(is_array($val2)){
					foreach( $val2 as $key3 => $val3 ){
						$ret .= $this->Form->hidden("$key1.$key2.$key3")."\n";
					}
				}else{
					$ret .= $this->Form->hidden("$key1.$key2")."\n";
				}
			}
		}
		return $ret;
	}
}
