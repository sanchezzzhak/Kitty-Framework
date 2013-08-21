<?php

if (!defined('doc_root')) exit('No direct script access allowed');
 
/**
 * Работа с массивами
**/ 

class arr
{
	
 
	
	
	
	
	/**
	 * Получить только нужные нам ключи из массива
	 * @param $array где искать 
	 * @param $keys нужные ключи.
	 * @usage 
	 * 
	 *   arr::extract(array(
	 *   	0=>array('id'=>1,'name'=>'apple'),
	 *   	'name'=>array('id'=>2)
	 *   ), array('id','name'),'samsung');
	 **/
    public static function extract(&$array, array $keys, $default = null){
        $arr = array();
        foreach ($keys as $key) $arr[$key] = isset($array[$key]) ? $array[$key] : $default;
        return $arr;
    }
	
	/*
	 * Извлечь из массива указаные ключи быстрее первой
	 * @param $array массив для поиска где искать
	 * @param  $kays указаные ключи в виде массива.  	 
	 **/
	public static function intersect(&$array, $keys = array()) {
		return array_intersect_key($array, array_flip($keys) );		
	}
	
	
	
	/**
	 * Почти тоже самое, что и первая функция за исключением того, что создается числовые ключи
	 * @param $array где искать 
	 * @param $key какой ключ нам нужен.
	 **/
	public static function pluck(&$array, $key){
		$values = array();
		foreach ($array as $row){
			if (isset($row[$key])){
				$values[] = $row[$key];
			}
		}
		return $values;
	}
	
	/**
	 * Проверка на assoc массив
	 * @param  $array массив для проверки
	 **/
    public static function is_assoc(array &$array){
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }
	
	/**
	 * Получить значение из массива иначе отдать значение по умолчанию.
     * Пример if($id = arr::get($_POST,'id',null,'int') && $id > 0){ ... } ;
	 * @param  $array    ссылка на переменую где искать ключ
	 * @param  $key      имя ключа
	 * @param  $default  значение по умолчанию.
	 * @param  $type     проверяет значение по типу, если  тип не верен возвращаем false
	 *                   
	 **/
    public static function get(&$array, $key, $default = null , $type = null)    {
        $return =  isset($array[$key]) ? $array[$key] : $default;
		if(!is_null($type)){
			switch($type){
				case 'array':    return self::is_array($return) ?  $return : false;
				case 'boolean':
				case 'bool':    return is_bool($return);	
				case '!empty':	return !empty($return) ?  $return : false;
				case 'empty':	return empty($return) ?   $return : false;	
				case 'int': 
				case 'integer':	return is_int($return) ?  $return : false;	
				case 'isnull':	return is_null($return) ? $return : false;
				case 'numeric':	return is_numeric($return) ? $return : false;	
				case 'str': 
				case 'string':  return is_string($return) ? $return : false;
			}
		}
		return $return;
    }
	
	
	/**
	 * Установить значение 
	 * @param  $array    ссылка на переменую
	 * @param  $key      имя ключа
	 * @param  $default  заданое значение 
	 **/
	public static function set(&$array, $key, $value = null)    {
        $array[$key] = $value;
    }
	
	/*
	 * Удалить значение из массива по ссылке
	 **/
	public static function delete(&$array){
		if(is_array($array))
			foreach (array_slice(func_get_args(), 1) as $key){
				unset($array[$key]);
			}
	}
	
	
	/**
	 * Проверить  массив или обьект, который способен проходить итерацию в цикле
	 **/
    public static function is_array(&$value){
        if (is_array($value))  return true;  else return (is_object($value) && $value instanceof Traversable);
    }
	
	/**
	 * Проверить значение в массиве без учета регистра
	 * @param  $needle  что ишим
	 * @param  $haystack  где искать
	 **/
	public static function in($needle, &$haystack){
		return in_array(strtolower($needle), array_map('strtolower', $haystack));
	}
	
	
	
	/**
	 * Аналог array_merge_recursive 5.3
	 * */
	public static function merge($array1,$array2){
        foreach($array2 as $k=>$v){
			if(is_integer($k))	$array1[]=$v;
			elseif(is_array($v) && isset($array1[$k]) && is_array($array1[$k]))
				$array1[$k]=self::merge($array1[$k],$v);
			else $array1[$k]=$v;
        }
        return $array1;
	}
	
	/**
	 * Принудительная перезапись значений массива $array1 по ключу у входных массивов
	 * Если у массива $array1 отсуствует ключ, такойже как и $array2 то новые значения
	 * 	из массивов не добовляются.
	 * @param array $array1 исходный массив
	 * @param array $array2 массив значений которые нужно перезаписать у массива 1
	 **/
	public static function overwrite($array1, $array2){
		foreach (array_intersect_key($array2, $array1) as $key => $value){
			$array1[$key] = $value;
		}
		if (func_num_args() > 2){
			foreach (array_slice(func_get_args(), 2) as $array2){
				foreach (array_intersect_key($array2, $array1) as $key => $value){
					$array1[$key] = $value;
				}
			}
		}
		return $array1;
	}
	
	/** 
	 * Удалить слешы в массиве
	 * @param  $array указатель на массив или строка
	 * @return array
	 */
    public static function stripslashes(&$array) {
        return is_array($array) ?   array_map('arr::stripslashes', $array ) : stripslashes($array);
    }
	
	
	
	/**
	 * Сортировка 2х мерного массива по ключу или по не скольким ключам
	 * $param $array массив который нужно отсортировать
	 * $param $key ключ по которому происходит сортировка.
	 * */
	public function sort(&$array,$key, $isdesc = false){
	
		
	}
	

	
	
}
