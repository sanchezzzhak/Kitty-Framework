<?

/**
* Class CException - Обрабочик ошибок
*/

class CException extends Exception {
	
	public function __construct($string = '', $params = array() ){
		if(is_array($params))
			$string = str_replace(array_keys($params), array_values($params),$string);
			//pre($this);
		parent::__construct($string);
	}
	
}
?>