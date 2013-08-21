<?
if (!defined('doc_root')) exit('No direct script access allowed');
	

/*
 * Класс коннекта к mongo серверу 
 **/
class dbMongo { 
	
	private
		$_mongo,             // MongoDB Class
		$_mongo_connection;  // Mongo   Class
			
	public
		$_server = 'localhost:27017',
		$_persistent = false;
	
	protected 
		$lastInsertId, 
		$_collections = array();
	
	public function __construct($host=null, $persistent = false){
		if(!class_exists("Mongo",false))
			throw new CException("php_mongo расширение не установлен");			
		try{	
			$host = !empty($host) ? $host : $this->_server;
			$this->_mongo_connection = new Mongo($host, true, $persistent);	
		}catch(CException $e){
			throw $e;
		}
	}
	
	/**
	 * Выбрать коллекцию
	 * @param  $name  имя коллекции
	 * @return  dbMongoCollection
	**/
	public function getCollection($name) {
		if (!isset($this->_collections[$name]))
			$this->_collections[$name] = new dbMongoCollection( $this->_mongo->selectCollection($name), $this);
		
		return $this->_collections[$name];
	}
	
	/**
	 * Выбрать коллекцию через magic метод, как в оригинале
	 * @param  $name  имя коллекции
	 * @return  dbMongoCollection
	**/
	public function __get($name){
		return $this->getCollection($name);
	}	
	
	/**
	 * Создать коллекцию
	 * @param  $name  имя коллекции
	 * @param  $capped  флаг который указывает на использование фиксации в коллекции
     * @param  $size  размером данных в коллекции
	 * @param  $max	  ограничить количеством документов в коллекции
	 * @return  dbMongoCollection
	**/
	public function createCollection($name , $capped = false, $size = 0, $max = 0){
		$this->_mongo->createCollection($name , $capped = false, $size = 0, $max = 0);
		return $this->getCollection($name);
	}
	
	
	
	/**
	 * Выбрать db если задан сеттер параметр $name
	 * Если db уже была выбрана и не задан параметр $name, то возвращается ссылка на MongoDB
	 **/
	public function selectDB($name = null){
		if(!is_null($this->_mongo) && is_null($name)) return $this;

		try{
			$this->_mongo = $this->_mongo_connection->selectDB($name);
			return $this;
		}catch(CException $e){
			throw $e;
		}
	}
	
	/** 
	 * Получить MongoDB
	 **/
	public function getDb(){
		$result  = ($this->_mongo instanceof MongoDB) ? $this->_mongo: false;
		if(!$result){
			throw new CException('Не выбрана MongoDB');
		}
		return $result;
		
	}
	
	/** 
	 * Выполнить запрос в виде JS кода.
	 * 		 
	 **/
	public function execute($code, $params = array() ){
		$result = $this->getDb()->execute($code, $params);
		if(!$result['ok']){
			throw new CException("Error: {error}\nCode: {js}", array(
				'{error}'=> $result['errmsg'],
				'{js}'=> $code,
			));		
		}
		return $result["retval"];
	}
	
	
	/**
	 * Получить список названий коллекции
	 **/
	public function listCollections(){
		$collectionNames = $this->execute('function(){ return db.getCollectionNames();}');
		$data = array();
		foreach ($collectionNames as $name) {
			if (!preg_match("/^system\./i", $name)) {
				$data[] = $name;
			}
		}
		
		return $data;
	}
	
	/**
	 * Получить имя выбраной БД
	 **/
	public function getName() {
		return (string)$this->_mongo;
	}
	
	private function setLastInsertId($id){
		$this->lastInsertId = $id;
	}

	public function lastInsertId(){
		return $this->lastInsertId;
	}
	
}

