<?php
namespace kitty\app;
use kitty\app\app;
use kitty\web\arr;

/**
 * Класс роутер 
 * @usage 
 * 
 * // site.dev/controller/action
	router::set('controller+action' , '/:controller/:action', array() )
		->setController(':controller', ':action' );
 * 
 **/

class router {
		
	const REGEX_KEY     = '#:([\w]+)\+?#u';
	const REGEX_SEGMENT = '([^/.,;?\n]+)';
	const REGEX_ESCAPE  = '[.\\+*?[^\\]${}=!|]';
	
	protected 
		$lang = 'ru',
		$module    = null,
		$controller = null,                   // Обьявлен контролер
		$path = '',               			 // Путь патерен
		$regex = '',              			 // Полная регулярка
		$param = array(),			         // Параметры  которые распарсили из строки
		$prepare_key =  array(),  			 // Ключи, полученые из строки /:controler/:actrion
		$callback    = null,                 // Функция для выполнения
		$default_controller = 'default_main'; // Контроллер по умолчанию

	
	protected static $routes = array();      // список обьектов роутеров
	
	 /**
	 * Добавить роутер в коллекцию
	 */
	public static function set($name , $path = null , $regex = null , $callback = null){	
		return router::$routes[$name] = new router($path, $regex , $callback );
	}
	

	/**
	 * Задать контроллер
	 * @param	$name   string Имя контролера можно задать явно и по маске URL
	 * @param	$action string Экшан, который нужно запускать также можно задать явно по названию
	 * 	 или по маске /profile/:action 
	 * @param  $params  array  Указать какие параметры относятся к экшану для запуска /profile/:id
	 */
	public function setController($name , $action = 'index', $params = array() ){
		$this->controller = array('name'=> $name , 'action' => $action );
		return $this;
	} 
	
	
	public function setModule($name){
		$this->module = array('name' => $name );
		return $this;
	}
	
	
	
	/**
	 * Задать язык  
	 */
	public function setLang($lang = 'ru'){
		$this->lang = $lang;
		return $this;
	}
	
	
	
	/**
	 * Задать путь /:controller/:action (***)
	 */
	public function setPath($path){
		$this->path = $path;
		return $this;
	}
	

	public function __construct($url = null, $param = array() , $callback = null){
		if ($url === null) return;
		$this->path = $url;
		//if (!empty($regex)) $this->param = $param;
		
		$this->callback = $callback;
		$compile = self::compile( $url , $param );
		$this->prepare_key = $compile['prepare_key']; 
	    $this->regex = $compile['pattern'];	
		
	}
	
	/**
	 *  Получить роутер по имени из коллекции
	 */
	public static function get($name){
		if (!isset(router::$routes[$name])) return;
		return router::$routes[$name];
	}

	/**
	 * Компиляция 
	 * $url    /:controller/:action
	 * $params array(':controller'=>'[\w]+')
	 */
	public static function compile( $url, $params  = array() ){
		preg_match_all(self::REGEX_KEY, $url ,$match);
		$condition_key = array_values($match[0]);

		$search = array();
		foreach ( array_keys($params) as $key ) {
			$search[] = '#:' . ltrim($key,':') . '\+?(?!\w)#';
		}
		$pattern = preg_replace($search, $params, $url );
		preg_replace('#\(/?:.+\)|\(|\)#', '', $pattern);
		foreach($condition_key as $key){
			$pattern = preg_replace('#'.$key.'\+?(?!\w)#u', self::REGEX_SEGMENT , $pattern  , 1);
		}
		rtrim($pattern,'/');
		return array('pattern' => '#^'. $pattern . '/?#u' , 'prepare_key'=> $condition_key);
	}

	/**
	 * Очиска списка роутеров и их правил
	 **/
	public static function clear(){
		router::$routes = array();
	}
	
	/**
	 * Получить URL запрошенный пользователем
	 **/
	public static function getURL(){
		if(!empty($_SERVER['REDIRECT_URL'])) 
			$url = $_SERVER['REDIRECT_URL']; 
				elseif(!empty($_SERVER['REQUEST_URI'])) 
					$url = $_SERVER['REQUEST_URI'];
		return $url;
	}
	
	
	/**
	 * Запустить поиск верной инструкции в след. порядке: 
	 * функция , модуль, контроллер
	 */
	public static function forUrl(){
		
		$url = Router::getURL();

		foreach(router::$routes as $route => $rule){
			$params = array();	

			if( preg_match($rule->regex ,$url , $math)){
				$real_url = $math[0]; 
				arr::delete($math,0);
					
				if(count($rule->prepare_key)>0){
					$params = $param = array_combine($rule->prepare_key , $math );
				
					array_walk($param, function($val,$key) use(&$params){ 
						if (substr($key,0,1)==':'){ 
							$params[substr($key,1)] = $val;
							unset($params[$key]);
						}
					});
					
				}
				$rule->param = $params;
				
				
				$module_name = $controller_name = $controller_action = '';
				// Определяем модуль
				if(!is_null($rule->module) && !empty($rule->module['name'])){
					if( substr($rule->module['name'],0,1)==':' && isset($param[$rule->module['name']])){ 
						$module_name = $param[ $rule->module['name'] ];	
					}else{
						$module_name = $rule->module['name'];
					}
				}
				
				// Определяем контроллер
				if(!is_null($rule->controller) && !empty($rule->controller['name']) ){
					// Если имя задано по параметру
					if( substr($rule->controller['name'],0,1)==':' 
						&& isset($param[$rule->controller['name']]))
							$controller_name   = $param[ $rule->controller['name'] ];	
						else 
							$controller_name = $rule->controller['name'];
					
					// Если действие заданно по параметру...	
					if(!empty($rule->controller['action']) && substr($rule->controller['action'],0,1)==':'){
						$controller_action = $param[$rule->controller['action'] ];
					// Если аргумент не был передан
					}elseif(is_null($rule->controller['action'])	|| !empty($rule->controller['action'])){
						$controller_action = 'index';
					}
					// Определение языка если, что-то было задоно в setLang
				}	
				
				// Если это callback вызываем функцию.
				if(is_callable( $rule->callback )){
					$callback =	$rule->callback;
					call_user_func_array( $callback , array() );
					return true;
				}
				

				// Запуск модуля
				if(!empty($module_name) 
				&&  App::runModule( $module_name , $controller_name, $controller_action, $params ) ){
					return true;
				}
				// Запуск контроллера
				if(empty($module_name) 
				&& App::runController($controller_name,$controller_action,$params)){
					return true;		
				}
				
				
			}
		}
		return false;
	}
	
}
