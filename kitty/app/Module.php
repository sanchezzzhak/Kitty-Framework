<?
namespace kitty\app;
use kitty\app\config;
use kitty\base\autoload;


class Module extends \kitty\base\ExtendBaseClass { 

	public
        // Имя модуля
        $name,
		// Путь к преложению или путь к модулю. 
		$basePath,      
		// Добавочные пути к baseApp 
		$pathController = '/controllers',    
		$pathModule     = '/modules',
        // Namespace модуля или базового контроллера
        $controllerNamespace  = null,


        $controllerMap = array();
	
	public function __construct($config = array()){
		$this->setBasePath($config['basePath']);
	}
	
	/*
	* Функция иницелизации
	*/
	public function init(){
        if($this->controllerNamespace===null){

            if( $this instanceof WebApplication ){
                $this->controllerNamespace = '\\app\\controllers';
            }else{
                $class = get_class($this);
                if (($pos = strrpos($class, '\\')) !== false) {
                    $this->controllerNamespace = substr($class, 0, $pos) . '\\controllers';
                }

            }
        }
	}
	
	/**
	 * Получить basePath
	 * basePath используется для запуска контролеров и модулей
	 **/
	public function getBasePath(){
		$path = $this->basePath;
		if(empty($path)) 
			throw new \kitty\base\ExceptionError('В конфигурации переложения не указан параметр basePath');
		return $path;
	}
	
	/**
	 * Установить basePath
     * @param $path базовой путь.
     * @throw Если каталог не существует.
	 **/
	public function setBasePath($path){
        if(($path = realpath($path))!==false && is_dir($path)){
            $this->basePath = $path;
        }else{
            throw new \kitty\base\ExceptionError("Каталог не существует :class->setBasePath(':path')", array(
                ':class' => get_class($this),
                ':path'  => $path,
            ));
        }
	}
	
	/**
	 * Запустить указанный контролер 
	 * @param $name    Имя контролера
	 * @param $action  Имя запускаемого метода 
	 * @param $params  Параметры для контроллера
	**/
	public function runController($name , $action = 'index' , $params = null ){	

        $result = false;
		$oldBasePath     = $this->getBasePath();
		$name_controller = trim($name,'/');

		//$sub_path = str_replace($name_controller , '',  implode('../', explode('/', $name)));
		$baseAppPath  = $this->getBasePath();


		$this->setBasePath($baseAppPath);
        $path  =  $baseAppPath . "/" . trim( $this->pathController ,'/' )   . "/". $name_controller .".php";

		if(is_readable($path)){

			include_once $path;

			$class = $this->controllerNamespace . '\\' .$name_controller."_controller";
            //$namespace_class = ltrim($this->controllerNamespace . '\\' . trim( $this->pathController ,'/' ) . $class, '\\');
            pre($class);


			if(($result = class_exists($class,false))!==false){
				$controller = new $class;
				$operation =  (!empty($this->name) ? $this->name . '::' : '') . implode('->',array( $name , $action ));

				$controller->param = $params;

				if(method_exists( $controller  , 'action'.$action)){

                    // header('Kitty.path.controller: ' . realpath($path));

                    $controller->setOperation( $operation );
                    // Выполнили до
                    $controller->before();
					$controller->{'action'.$action}();
                    // Выполнили после
                    $controller->after();

                }

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
		$path = $baseAppPath . "/" . trim( $this->pathModule ,'/' ) . "/" . $name . "/" . $name . ".php";
		// Список модулей в конфиге



		$arrModules = config::get('modules');


		if(is_readable($path) && in_array($name, $arrModules)){
			include_once $path;

			$class = trim("app\\modules\\".$name."\\".$name."_module");

            pre($class);
			if(class_exists($class,false)){

				/*
                    $ref = new ReflectionClass($this);
                    $path = pathinfo( $ref->getFileName(), PATHINFO_DIRNAME);
				*/
                $path = pathinfo( $path , PATHINFO_DIRNAME);
				$module = new $class( array(
					'basePath'=> $path,
				));
                $module->name = $name;
				$module->init();
				// Регестрируем путь для моделей
				// autoload::addPath( $path .  "/models" );
				// Запуск контролера в модуле
				if(!empty($controller)){

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