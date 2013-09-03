<?
namespace kitty\base;




/** 
 * Автозагрузка классов
 */ 
 
class AutoLoad {
	
	public static 
		$_path_class = array(),
		$_map  = array(),
		$_path = array();
  
  
  
	/*
	 * Добавить путь в set_include_path
	 * @param $path путь который хотим добавить.
	 **/
	public static function setIncludePath($path){
        $_paths =  array_unique(explode(PATH_SEPARATOR,get_include_path()));
        if (array_search ( $path , $_paths ) === false) array_push ($_paths, $path);
        set_include_path ( implode ( PATH_SEPARATOR, $_paths ) );
	}


  
  
	/**
	* Регистрация в spl_autoload_register
     * @throw Если обрабочик не сможет зарегестрировать метод
	**/
	public static function register() {
		if (!spl_autoload_register('\kitty\base\autoload::load')) {
			throw new \Exception('Не могу зарегистрировать обрабочик в spl_autoload_register(\kitty\base\autoload::load)' );
		}
	}

    /**
     * Убрать регистрацию в spl_autoload_unregister
     * @throw Если обрабочик не сможет зарегестрировать метод
     **/
    public static function unregister() {
        if (!spl_autoload_unregister('\kitty\base\autoload::load')) {
            throw new \Exception('Не могу убрать обрабочик с spl_autoload_unregister(\kitty\base\autoload::load)' );
        }
    }



  /**
   * Добавление в карту прохода новый путь
   * @param string $path - путь от корня сайта
   * @param string $pfix - прификс файлов .class.php 
   **/
  public static function addPath($path){
      $dir_path = __DIR__."/../../" . trim($path,'/');
      if (array_search ( $path , self::$_path_class ) === false && is_dir( $dir_path)){
          self::$_path_class[]  =  "/" .trim($path,'/');
          return true;
      }
      return false;
  }
  


  /*
   * Загрузить карту соотвецтвия классам => путей
   * @param array $maps
   * */
	public static function loadMap($maps){
		self::$_map = array_merge_recursive(static::$_map, $maps);
	}
	
	
	/*
	 * Поиск файла без учета регистра через glob
	 * [ данный меттод может быть убран иза медленной реализации ]
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
   * @param  $class имя класса;
   * @return boolean;
   **/

   public static function load($class) {	
    $base_path = $path = __DIR__  . "/../../";

	$is_namespaced = ($pos = strripos($class, '\\')) !== false; 
	
	$class = ltrim($class, '\\');
	$class = str_replace(array('/', '\\',), '/' , $class);
	// Проверяем namespace на путь...
	if($is_namespaced){
        $path = $base_path . $class . ".php";
		if(is_file($path)){
			include_once $path;
			return class_exists($class,false);
		}
        // карта классов namespace
        foreach(self::$_map as $item){
            if(isset($item[$class])){
                include_once $item[$class];
                return true;
            }
        }

    // Если это !namespace а просто имя класса смотрим добавленые пути
	}else{

	    $lower_class   = strtolower($class);
        $ucfirst_class = ucfirst($class);

        /*if(($path = stream_resolve_include_path(  $ucfirst_class.'.php' )) !== false){
            if(is_file($path)){
                include_once $path;
                return true;
            }
        }*/
        // Карта добавленых путей
        foreach(self::$_path_class as $path){
            $path1 = rtrim($base_path ,'/') . $path . '/'  . $ucfirst_class  .'.php';
            $path2 = rtrim($base_path ,'/') . $path . '/'  . $lower_class .'.php';
            if(is_file( $path1) ){
                include_once $path1;
            }elseif(is_file($path2)){
                include_once $path2;
            }
        }
        return  (class_exists($class, false) || interface_exists($class, false));
    }
    return false;
  }




}
