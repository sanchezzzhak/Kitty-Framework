<?php
namespace kitty\app;
use \kitty\web\arr;

/**
 * Хранения настроек с классе
 */
 
class config {
	
	private static $_c = array();
	
	/**
	 * Установить у ключа значение
     *
     * @param $name ключ
     * @param $value значение
	 **/
	public static function set($name , $value){
		arr::set(self::$_c,$name,$value);	
	}
	
	/**
	 * Получить по ключу значение
     *
     * @param $name ключ
     * @return $value значение
     **/
	public static function get($name){
		return arr::get(self::$_c,$name,null);
	}
	
	/**
	 * Загрузить массив
	 **/
	public static function load($arr){
		self::$_c = arr::merge(self::$_c,$arr);
	}
		
}

	
	