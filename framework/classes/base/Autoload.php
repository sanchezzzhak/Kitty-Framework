<?

if (!defined('doc_root')) exit('No direct script access allowed');


	
	
	
/** 
 * Автозагрузка классов
 */ 
 
 

 
 
class autoload {
	
	public static 
		$_path_class = array(),
		$_map  = array(),
		$_path = array();
  
  
  
	/*
	 * Добавить путь в set_include_path
	 * 
	 **/
	public static function addIncludePath($path){
		$_paths =  array_unique(explode(PATH_SEPARATOR,get_include_path()));
		set_include_path(
			get_include_path() . PATH_SEPARATOR . implode( PATH_SEPARATOR, $_paths)
		);	
	}


  
  
	/**
	* Регистрация в spl_autoload_register
	**/
	public static function register() {
		if (!spl_autoload_register('autoload::load')) {
			throw new Exception('Не могу зарегистрировать в spl_autoload_register класс autoload::load');
		}
	}
  
  /**
   * Добавление в карту прохода новый путь
   * @param string $path - путь от корня сайта
   * @param string $pfix - прификс файлов .class.php 
   **/
  public static function addPath($path){
      $base_path = dirname(__FILE__)."/../../../";
      if(!is_dir($path) ){
          if(is_dir($base_path . trim($path,'/') )){
              $path = $base_path . trim($path,'/');
          }else{
              return;
          }
      }

      self::$_path_class[$path]  = '';
  }
  
  /**
   * Убрать регистрацию в spl_autoload_unregister
   **/
  public static function unregister() {
    if (!spl_autoload_unregister('autoload::load')) {
    	throw new Exception('Не могу убрать регистрацию spl_autoload_unregister c класса autoload::load');
    }
  }

  /*
   * Загрузить карту соотвецтвия путей => классам
   * @param array $maps
   * */
	public static function loadmap($maps){
		self::$_map = array_merge_recursive(self::$_map, $maps);
	}
	
	
	/*
	 * Поиск файла без учета регистра через glob, медленная реализация
	 * [ данный меттод может быть убран ]
	 * @param  $path  Путь
	 * @param  $file  Имя файла
	 **/
	public static function findFile( $path , $file = '' ){
		$str = '';
		foreach(str_split($file) as $s){
			if(in_array($s, array('.','*','[',']','/','_'))){
				$str.= $s;	
			}else{ 
				$str.='['.strtolower($s).strtoupper($s).']';
			}
		}
		$arr =  glob(  rtrim($path,'/') . '/' . $str );
		return  (count($arr)) ? $arr : false;
	}
	

	




  
  /**
   * Загрузка класса 
   * @param  string $class;
   **/
   
   
   
   public static function load($class) {	

	$is_namespaced = ($pos = strripos($class, '\\')) !== false; 
	
	$class = ltrim($class, '\\');
	$class = str_replace(array('/', '\\',), DS , $class);

	$lower_class = strtolower($class);
	foreach(self::$_map as $item){
		if(isset($item[$lower_class])){
			include_once $item[$lower_class];
			return true;	
		}
	}
	
	//pre($is_namespaced,$lower_class,$class);
	
	// папка с классами по умолчанию. 
	if(($path = stream_resolve_include_path(  ucfirst($class) .'.php' )) !== false){
		 if(is_file($path)){
			include_once $path;
			return true;
		} 
	}
	
	// проходим карту добавленых путей
	if(count(self::$_path_class)){
		foreach(self::$_path_class as $path =>$pfix){

			$path1 = trim($path ,'/') .  '/'  . ucfirst($class) .'.php';
			$path2 = trim($path ,'/') .  '/'  . $class .'.php';

			if(is_file( $path1  ) ){
				include_once $path1;
				return true;
			}elseif(is_file($path2)){
				include_once $path;
				return true;
			}
			
		}	
	}
	

	
	
	return false;
  }
  
}
