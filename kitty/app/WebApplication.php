<?
namespace kitty\app;



class WebApplication extends \kitty\app\Module {
	
	public function __construct($config = null){

		if(is_array($config)){ 	config::load($config); 	}
		
		$locale = config::get('locale');
		
		if($locale!==null){ setlocale(LC_ALL, $locale); }
		
		// обработчик ошибок...
		$this->initErrorHandlers();
		$this->setBasePath( config::get('basePath') );
		$this->getBasePath();
		$this->init();
	}
	
	/* Метод вызывается по умолчанию */
	public function init(){


    }

	/*
	 * Запус приложения 
	 **/ 
    public function run(){
		if(($flag = router::forUrl())===true){
			return;
			// Если контроллер не найден
		}elseif(!$flag){			
			// Запускаем контроллер обычные страницы
			$flag =	app::runController('page', 'id' , array( ) );
			if($flag) return;
		}
		// Если все провалилось используем редерект на указаную страницу конфига
		if(!$flag){
			//$page404 = config::get('page404');
			//$controller = new Controller();
			//$controller->redirect($page404);
		}
		
    }
	
	/**
	 * Завершение работы приложения
	 * @param int $status Статусы выхода должны быть в диапазоне от 0 до 254
	 * http://php.net/manual/ru/function.exit.php
	 * @param bool $exit использовать функцию exit
	 * */
	public function end($status=0, $exit=true){
		if($exit) exit($status);
	}
	
	/**
	 * Инициализирует обработчик исключений и ошибок
	 **/
	public function initErrorHandlers(){
		$class = new \kitty\base\ErrorHandler;
		set_exception_handler(array( $class ,'handleException'));
		set_error_handler(array(  $class ,'handleError'),error_reporting());
		//register_shutdown_function(array($class ,'shutdownHandler'));	
	}
	

	
   
   
   
}
