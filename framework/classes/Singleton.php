<?php
if (!defined('doc_root')) exit('No direct script access allowed');

/**
 * Синглетон коллекция классов  
**/
class singleton {
	
	static private $i = array();
	
	
	/**
	 * Вызвать и поместить в массив классов копию класса для дальнейшего повторного использования 
	 * @param  $class Имя класса который вы хотите поместить
	 * @return  Возращяет копию класса из массива
	 **/
	public static function make($class){  
        if (!array_key_exists($class, self::$i)) { self::$i[$class] = new $class; }
        $i = self::$i[$class];
        return $i; 
    }
	
}
?>