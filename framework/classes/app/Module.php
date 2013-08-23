<?
class Module extends ExtendBaseClass { 

	public
		// Путь к преложению или путь к модулю. 
		$basePath,      
		// Добавочные пути к baseApp 
		$pathController = '/controllers',    
		$pathModule     = '/modules',
		// Имя модуля
		$name;          
	
	public function __construct($config = array()){
		$this->setBasePath($config['basePath']);
	}
	
	/*
	* Функция иницелизации для модуля, вызывается при первом подключении к модулю. 
	*/
	public function init(){
	
	}
	
	/**
	 * Получить basePath
	 * basePath используется для запуска контролеров и модулей
	 **/
	public function getBasePath(){
		$path = $this->basePath;
		if(empty($path)) 
			throw new ExceptionError('В конфигурации переложения не указан параметр basePath');
		return $path;
	}
	
	/**
	 * Установить basePath
	 **/
	public function setBasePath($path){
		$this->basePath = $path;
	}
	
	/**
	 * Запустить указанный контролер 
	 * @param $name    Имя контролера
	 * @param $action  Имя запускаемого метода 
	 * @param $params  Параметры для контролера
	 * @see 
	 * Можно запустить контроллер из корня для этого в имени контроллера, нужно указать //
	 * Каждый / - это уровень директории назад 
	**/
	public function runController($name , $action = 'index' , $params = null ){	
		$result = false;
		$oldBasePath     = $this->getBasePath();
		$name_controller = trim($name,'/');
		$sub_path = str_replace($name_controller , '',  implode('../', explode('/', $name)));
		$baseAppPath  = $this->getBasePath() ."/" . $sub_path ;
		$this->setBasePath($baseAppPath);
		$path  =  $baseAppPath . trim( $this->pathController ,'/' )
			. "/". $name_controller .".php";
			
		if(is_readable($path)){

			include_once $path;			
			$class = $name_controller."_controller";
			if(class_exists($class,false)){			
				$controller = new $class;
				$operation =  (!empty($this->name) ? $this->name . '::' : '')
				              . implode('->',array( $name , $action ));
							  

				$controller->param = $params;

				if(method_exists( $controller  , 'action'.$action)){
                    $controller->setOperation( $operation );
                    // Выполнили до
                    $controller->before();
					$controller->{'action'.$action}();

				}else $result = false;
				// Выполнили после	
				$controller->after();	
				$result = true;	
			}
		}
		$this->setBasePath($oldBasePath);
		return $result;
	}
	
	/*
	 * Запуск модуля
	 * @param  $name  имя модуля
     * @param  $controller  Контроллер который запускать
	 * @param  $action      Метод который нужно запустить
     * @param  $params      Параметры 	 
	 **/
	public function runModule($name , $controller = 'default_main' , $action = 'index', $params = array() ){
		$baseAppPath     = $this->getBasePath();
		// Путь к модулю
		$path = $baseAppPath."/".trim( $this->pathModule ,'/' )."/".$name."/".$name.".php";
		// Список модулей в конфиге 
		$arrModules = Config::get('modules');
		if(is_readable($path) && in_array($name, $arrModules)){
			include_once $path;
			$class = $name."_module";	
			if(class_exists($class,false)){
				/*
				$ref = new ReflectionClass($class);	
				$path = pathinfo( $ref->getFileName(), PATHINFO_DIRNAME);
				*/
                $path = pathinfo( $path , PATHINFO_DIRNAME);

				$module = new $class( array( 
					'basePath'=> $path,
				));
				
				$module->init();
				// Регестрируем путь для моделей
				Autoload::addPath($path . "/" . "models" );
				// Запуск контролера в модуле
				if(!empty($controller)){
					$module->name = $name;
					return $module->runController($controller , $action, $params);
				}		
				return true;
			}	
		}
		return false;
	}
	
	/**
	 * Получить уникальный ID на основе сроки
	 * @param  $path дополнительный путь
	 **/
	public function getId($path =''){
		return sprintf('%x',crc32($this->getBasePath() . $path ));
	}
	
}