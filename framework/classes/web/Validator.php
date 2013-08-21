<?php
if (!defined('doc_root')) exit('No direct script access allowed');
 
 /*
  * Класс для проверки данных обычно это массив
  * 
  **/
 
class Validator {

	protected 
		$_errors = array(),
		$_data   = array(),
		$_rules  = array();
		
	
	/* 
	 * Конструктор 
	 * @param  $arrData  Данные в виде массива, которые нужно проверить 	 
	 * @param  $arrRules Правила в виде специального массива с инструкциями:
	array ( 
	   Обязательные поля + проверка что значение не пустое
	  array('список полей', 'required' , 'message'=> 'Свое сообщение :attr ' ),
	  	Фильтер по длине
	  array('список полей', 'length', 'min'=>0, 'max'=> 255 ),
	  	Фильтер по регулярке
	  array('список полей', 'match' , 'pattern'),               			 
        Проверка на тип
	  array('список полей', 'type', 'is' => 'int|integer|string|float|array|boolean|bool'  ),            
	 	Проверка на email
	  array('список полей', 'email'),                           			 
		Проверяет наличия значения в массиве
	  array('список полей', 'in' , 'arr' => array() ),          
    );

    ['message'] Можно задать в каждом правиле
	
	**/   
	public function __construct($arrData , $arrRules){
		$this->_data = $arrData;
		$this->_rules = $arrRules;
	}

	/*
	 * Возвращает ошибки, которые были найдены входе проверки 
	 * @return array
	 **/
	public function getErrors(){
		return $this->_errors;
	}
	
	/**
	 * Добавить в список ошибку.
	 * @param  $attr_name  имя атрибута
	 * @param  $error  текст ошибки
	**/
	public function addError($attr_name , $error = ''){
		/*if(isset($this->_errors[$attr_name]) 
			&& array_search($error,$this->_errors[$attr_name])!==false ) return;
		 */
		$this->_errors[$attr_name][] = $error;
	}	
	
	

	
	
	
	
	/**
	 * Получить значение атрибута
	**/
	public function getAttrValue($key){
		return isset($this->_data[$key]) ? $this->_data[$key]: null;
	}
	
	/**
	 * Запускает проверку по правелам 
	**/
	public function run(){
		foreach($this->_rules as $rule){
			$attr_list = explode(',',$rule[0]);
			switch($rule[1]){
				case 'required':
				$message = isset($rule['message']) ? $rule['message'] :'Поле :attr не заполнено';
				foreach($attr_list as $item){
					$value  = $this->getAttrValue($item);
					if( $this->required($value) ) $this->addError($item,$message);
				}
				break;
				case 'match':
				$message= isset($rule['message']) ? $rule['message'] :'Поле :attr заполнено неверно';
				foreach($attr_list as $item){
					$value  = $this->getAttrValue($item);
					if(isset($rule['pattern']) && !preg_match($rule['pattern'],$value)){
						$this->addError($item,$message);
					}
				}
				break;
				case 'length':
				foreach($attr_list as $item){
					$value  = $this->getAttrValue($item);
					if(isset($rule['min']) && mb_strlen($value) < (int)$rule['min'] ){
						$message = !isset($rule['message'])? ' Поле :attr содержит миленькое количество символов' : $rule['message']; 
						$this->addError($item, $message);
					}elseif(isset($rule['max']) && mb_strlen($value) > (int)$rule['max'] ){
						$message = !isset($rule['message'])? 'Поле :attr превышает максимальное значение': $rule['message'];
						$this->addError($item, $message);
					}
				}			
				break;
				case 'email':
				$message= isset($rule['message']) ? $rule['message'] :'Поле :attr содержит неверный формат email';
				$pattern='/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix';
				foreach($attr_list as $item){
					$value  = $this->getAttrValue($item);
					if(!preg_match($pattern,$value)){
						$this->addError($item,$message);
					}
				}
				break;
				case 'in':
				$message= isset($rule['message']) ? $rule['message'] :'Поле :attr заполнено неверно';
				foreach($attr_list as $item){
					$value  = $this->getAttrValue($item);
					if(isset($rule['arr']) && !in_array($value,$rule['arr']) ){
						$this->addError($item,$message);
					}
				}
				break;
				case 'type':
				$message= isset($rule['message']) ? $rule['message'] :'Поле :attr заполнено неверно';
				if(isset($rule['is']))
				foreach($attr_list as $item){
					$value  = $this->getAttrValue($item);
					$valid = true;
					switch($rule['is']){
						case 'int': case 'integer':
							$valid = preg_match('/^[-+]?[0-9]+$/',$value);
						break;
						case 'float':
							$valid = preg_match('/^[-+]?([0-9]*\.)?[0-9]+([eE][-+]?[0-9]+)?$/',$value) 
									 && is_float($value);
							
						break;
						case 'boolean': case 'bool':
							$valid = is_bool($value);
						break;
						
						case 'array':
							$valid = is_array($value);
						break;
					}	
					if(!$valid) $this->addError($item,$message);
				}				
				break;
			}
			
		}
		return $this->getErrors();
	}

	
	
	/**
	 * Правило required
	 **/ 
	public function required($value){
		return empty($value);
	}
	
	
	
	
}
