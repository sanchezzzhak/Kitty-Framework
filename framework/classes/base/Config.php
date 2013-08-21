<?php


if (!defined('doc_root')) exit('No direct script access allowed');

/**
 * Хранения настроек с классе
 */
class config {
	
	private static $_c = array();
	
	/**
	 * Установить у ключа значение 
	 **/
	public static function set($name , $value){
		arr::set(self::$_c,$name,$value);	
	}
	
	/**
	 * Получить по ключу настройку
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

	
	