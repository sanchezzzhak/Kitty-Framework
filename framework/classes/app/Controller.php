<?php
/*
 * #CodeName# 
 * @author  
 * @copyright  
 * @
 **/
 
if (!defined('doc_root')) exit('No direct script access allowed');

class Controller { 
	
	public 
		$param,                // Параметры переданые через Router
		$layout,               // Указатель на слой
		$pageTitle ,           // 
		$pageKeywords, 
		$pageDescription;
		
	
	protected $_operation;
	
	public function getOperation(){
		return  $this->_operation;
	}
	public function setOperation($string){
		$this->_operation = $string;
	}
	
	/*
	 * Метод вызывается до вызова контролера
	 * */
	public function before(){
		// $event = new Event();
		return true;
		
	}
	/*
	 * Метод вызывается после вызова контролера
	 * */
	public function after(){
		// $event = new Event();
		return true;
	}

	
	/**
	 * Получить пост
	 * @param  $name имя ключа в массиве $_POST если ключ не указан возращяем полный массив _post
	 * @param  $default значение по умолчанию
	 * @return ?
	 **/
	public function post($name=null, $default = null, $type = null){
		return  is_null($name) ? $_POST :  arr::get($_POST,$name,$default,$type);
	}
	
	/**
	 * Получить куку
	 * @param  $name имя ключа в массиве $_COOKIE если ключ не указан возращяем полный массив _cookie
	 * @param  $default значение по умолчанию
	 * @return ?
	 **/
	public function cookie($name =null, $default = null){
		return  is_null($name) ? $_COOKIE :  arr::get($_COOKIE,$name,$default,$type);
	}
	
	
	/**
	 * Полкчит гет
	 * @param  $name имя ключа в массиве $_GET если ключ не указан возращяем полный массив _get
	 * @param  $default значение по умолчанию
	 * @return ?
	**/
	public function get($name=null, $default = null,$type = null){
		return  is_null($name) ? $_GET : arr::get($_GET,$name,$default,$type);
	}
	
	/*
	 * Редерект 
	 * @param  $url  куда перенаправить 
	 * @param  $http_code  Http статус
	 **/
	public function redirect($url , $http_code = 301){
		header('HTTP/1.1 301 Moved Permanently');
		header('Location: '.$url , true, $http_code);
		exit(1);
	}
	
	/**
	 * Подключить указаный view
	 * @param  $view  путь/название вида для подключения
	 * @param  $data  параметры которые, нужно передать во view
	 **/
	protected function view($view, $data = array() ){
		
		$ref = new ReflectionClass($this);	
		$basePath = pathinfo($ref->getFileName(), PATHINFO_DIRNAME ) ."/..";
		$file = $basePath . "/views/" . trim( $view,'/' ) . '.php';
		
		if(!file_exists($file)){
			$path = pathinfo($file);
			throw new CException('Файл {view} представление не найдено ', array(
				'{view}' => $path['basename'],
				'{path}' => $path['dirname'],
			));
		}
		ob_start() and extract($data, EXTR_SKIP);
		try{
			include $file;
		}catch (CException $e){
			ob_end_clean();
			throw $e;
		}	
		return ob_get_clean();
	}
	
	
	/**
	 * Рендер вьюшки и слоя 
	 * @param  $view  путь+имя вьюшки.
	 * @param  $data  Массив параметров, которые нужно передать вьюшки
	 * @param  $layoutData  Массив параметров, которые можно передать указаному layout
     * @param  $return  Флаг переключения вывода в переменую(true) или на экран(false). 	 
	 * 	 
	 **/
	protected function renderGlobal($view , $data  = null, $layoutData = null , $return = false ){
		$this->beforeRender($view);
		// Вьюшка
		$viewtext = $this->view( $view, (array)$data );
		// Слой
		if($this->layout){
			$layout_name = trim($this->layout,'/');
			$sub_path = str_replace($layout_name , '',  implode('../', explode('/', $this->layout)));
			$path  = $sub_path . "../layouts/" . $this->layout;

			$viewtext = $this->view( $path , arr::merge( array(
				'content'  => $viewtext,
			), (array)$layoutData ));	
		}
		$viewtext = $this->afterRender($view , $viewtext);
		
		if($return) return $viewtext; 
		else echo $viewtext;
	}
	
	
	
	
	/**
	 * Рендер вьюшки и слоя и передача вьюшке параметров
	 * @param  $view  путь+имя вьюшки.
	 * @param  $data  Массив параметров которые нужно передать вьюшки
     * @param  $return  Флаг переключения вывода результата как текста 	 
	 **/
	protected function render($view,$data=null,$return = false){
		return $this->renderGlobal($view,$data,null,$return);
	}
	
	/**
	 * Метод вызывается до рендера
	 **/
	protected function beforeRender($view){
		return true;
	}
	
	/**
	 * Метод вызывается после рендера вьюшек и результа
	 **/
	protected function afterRender($view, $output = null){
		return $output;
	}

	
	
}
