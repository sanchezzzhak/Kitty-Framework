<?
namespace kitty\base;

/* 
 Многие методы повзаимствованы из Yii 
*/

class ExtendBaseClass {

	
	protected
		$__extend_set = false,	// Флаг для вставки свойства в класс
		$_events,     			// События 
		$_behaviors;  			// Поведения
	
	
	public function __construct(){
		$this->initBehaviors();
	}
	
	/* 
	 * Дефольнтные парамеры 
	 * */
	public function __clone(){
		$this->_events = $this->_behaviors = null;
	}
	
	/**
	 *  
	 * Задает новое свойство в классе
	 * @param  $name  имя свойства
	 * @param  $value значение
	 * @example  
	    $this->extend('prop_session' , new Session() );
	    $this->prop_session->Id();
	**/
	public function extend($name , $value){
		if(!method_exists($this,$name) && !$this->property_exists($this,$name)){
			$this->__extend_set = true;
			return $this->__set($name , $value);
		}
		return false;
	}
	/**
	 * Установить свойтсва у класса.
	 * Выполнить setter метод если есть у класса
	 **/
	public function __set($name , $value){
		// Если это новое свойство заданное через wrapObject
		$setter = 'set'.$name;
		if($this->__extend_set){ 
			$this->{$name} = $value; 
			$this->__extend_set = false;
			return; 
		}elseif(method_exists($this,$setter)){
			return $setter($value);
		}elseif(property_exists($this,$name)){
			$this->{$name} = $value; 
			return;
		}else{
			$this->initBehaviors();
			foreach($this->_behaviors as $behavior ){
				if ($behavior->canSetProperty($name)) {
					$behavior->{$name} = $value;
					return;
				}
			}
		}
		
		throw new ExceptionError('Запрашиваемое свойство {class}::{name} не найдено', array(
			'{class}'=> get_class($this), 
			'{name}' => $name,
		));
	}
	
	/* 
	 *	Вызвать getter метод, если он есть
	 *  Получить свойство класса
	 */
	public function __get($name){
		$getter = 'get'.$name;
		if(method_exists($this,$getter)){
			return $this->$getter();
		}elseif(property_exists($this,$name)){
			return $this->{$name};
		}else{
			$this->initBehaviors();
			foreach ($this->_behaviors as $behavior) {
				if ($behavior->canGetProperty($name)) {
					return $behavior->$name;
				}
			}
		}
		
		
		throw new ExceptionError('Запрашиваемое свойство {class}::{name} не найдено', array(
			'{class}'=> get_class($this), 
			'{name}' => $name,
		));
	}
	
	
	public function __unset($name){
		if(property_exists($name)){
			unset($this->{$name});
		}
		return;
	}
	
	
	public function __isset($name){
		if(property_exists($name)){
			return true;
		}
		return false;
	}
	
	public function __call($name, $params){
		return;
	}

	/*
	 * Прикрепить событие
	 * 
	**/
	public function on($name, $callback , $data = null ){
		$this->_events[$name][] = array($callback,$data);
	}
	
	/*
	 * Открепить событие 
	**/
	public function off($name , $callback = null ){
		$return = false;
		if(isset($this->_events[$name]))
			if(null === $callback){
				$this->_events[$name] = array();
				$return = true;
			}else{
				foreach ($this->_events[$name] as $i => $event) {
					if ($event[0] === $handler) {
						unset($this->_events[$name][$i]);
						$return = true;
					}
				}
			}
		return $return;
		
	}
	
	/*
	 * Вызвать событие  
	 * @param  $name имя события
	 * @param  $event class Event
	**/
	public function trigger($name , $event = null ){
		if (!empty($this->_events[$name])) {
			if(null === $event){
				$event = new Event;
			}
			$event->handled = false;
			$event->name = $name;
			foreach($this->_events as $handle){
				$event->data = $handle[1];				
				call_user_func($handle[0], $event );
			}
		}
		return false;
	}
	
	
	

	public function behaviors(){
		return array();
	}

	
	
	/**
	 * Инициализация поведений исходя из функции 
	 **/
	public function initBehaviors(){
		if ($this->_behaviors === null) {
			$this->_behaviors = array();
			$arr = $this->behaviors();
			if(!is_array($arr))
				throw new ExceptionError('Метод {class}::behaviors() должен возвращать массив', array(
					'{class}'=> get_class($this),
				));
			foreach ($arr as $name => $behavior) {
				$this->attachBehaviorInternal($name, $behavior);
			}
			
		}
		
	}
	
	/**
	 * Установить поведение 
	**/
	public function attachBehavior($name , $config = array() ){
		return $this->attachBehaviorInternal($name, $config);
	}
	
	/**
	 * Удалить поведение 
	**/
	public function detachBehavior($name){
		if (isset($this->_behaviors[$name])) {
			$behavior = $this->_behaviors[$name];
			unset($this->_behaviors[$name]);
			$behavior->detach();
			return $behavior;
		}
		return;
	}
	
	/**
	 * Установить поведение
	 **/
	public function attachBehaviorInternal($name , $behavior ){
		
	}
	
	
	
	
	public function canGetProperty($name){
		if (method_exists($this, 'get' . $name) || property_exists($this, $name)) {
			return true;
		}
		$this->initBehaviors();
		foreach ($this->_behaviors as $behavior) {
			if ($behavior->canGetProperty($name)) {
				return true;
			}
		}
		return false;
	}
		
	public function canSetProperty($name){
		if (method_exists($this, 'get' . $name) || property_exists($this, $name)) {
			return true;
		}
		$this->initBehaviors();
		foreach ($this->_behaviors as $behavior) {
			if ($behavior->canSetProperty($name)) {
				return true;
			}
		}
		return false;
	}
	 
	
	
}
