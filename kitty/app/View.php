<?
namespace kitty\app;

class view {
	
	protected $view_path_default;
	protected $_file;
	protected $_data = array();
	
	/**
	* фабричный метод конструктора
	**/
	public static function make($file = null, array $data = null){
		return new view($file, $data);
	}
	
	/**
	* Конструктор 
	* $file файл представления
	* $data массив параметров
	*/
	public function __construct($file = NULL, array $data = NULL){
		if ($file !== NULL)	$this->set_filename($file);
		if ($data !== NULL)	$this->_data = $data + $this->_data;
	}
	
	/**
	 * Указываем какой файл подключить
	 * @return $this 
	 **/
	public function set_filename($file){
		$this->_file = $this->get_file_path($file);
		return $this;
	}
	
	
	public function get_file_path($file){
		
		if(file_exists($file)){
			return $file;
		}else{		
			$appBasePath = config::get('basePath');
			$file = $appBasePath . "/views/" . trim( $file,'/' ) . '.php';
		}
		
		if(!file_exists($file)){
			$path = pathinfo($file);
			throw new ExceptionError('Файл '.$path['basename'].' представления не найден');
		}	
		return $file;
	}
	
	
	public function & __get($key){
		if (array_key_exists($key, $this->_data)){
			return $this->_data[$key];
		}
	}
	
	/**
	* меджик метод задать слою значение как если бы мы обрасчались к обьекту методом $object->var1 = 'значение';
	*/
	public function __set($key, $value){
		$this->set($key, $value);
	}
	
	/**
	* меджик метод проверить у слоя значения
	*/
	public function __isset($key){
		return (isset($this->_data[$key]));
	}
	
	/**
	* меджик метод удалить у слоя значения
	*/
	public function __unset($key){
		unset($this->_data[$key]);
	}
	
	/**
	* бинд переменых значения
	* @return $this
	*/
	public function bind($key, & $value){
		$this->_data[$key] =& $value;
		return $this;
	}
	
	/**
	* Установка переменых значений и передача их непосредственно слою
	* @return $this
	*/
	public function set($key, $value = NULL){
		if (is_array($key))	{
			foreach ($key as $name => $value){ 
				$this->_data[$name] = $value;
			}
		}else{
			$this->_data[$key] = $value;
		}
		return $this;
	}
	
	
	/**
	* Загрузка вида из файла и выполнение
	* @return string
	*/
	protected function load_view($cv_view_filename, array $cv_view_data){
		ob_start() and extract($cv_view_data, EXTR_SKIP);
	
		try{
			include $cv_view_filename;
		}
		catch (ExceptionError $e){
			ob_end_clean();
			throw $e;
		}

		return ob_get_clean();
	}
	
	/**
	* запуск рендеринга шаблона
	* a) Установка файла
	* b) Загрузка вида из файла и выполнение
	* @return string 
	*/
	public function render($file = NULL){
		if ($file !== NULL){
			$this->set_filename($file);
		}
		if (empty($this->_file)){
			throw new ExceptionError('Не указан файл view для render');
		}

		return $this->load_view($this->_file, $this->_data);
	}
	
	
	
	/**
	* Меджик метод 
	* Вывод render если к методу обратились как свойству
	**/
	public function __toString(){
		return $this->render();	
	}
	
	
	
	
	
	
}
?>