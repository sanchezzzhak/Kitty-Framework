<?
namespace kitty\base;
/**
* Class ExceptionError - Обрабочик ошибок
*/

class ExceptionError extends \Exception {
	
	public function __construct($string = '', $params = array() ){
		if(is_array($params))
		    $string = str_replace(array_keys($params), array_values($params),$string);

		parent::__construct($string);
	}
	
}
?>