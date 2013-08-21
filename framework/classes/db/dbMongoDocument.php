<?
if (!defined('doc_root')) exit('No direct script access allowed');
	
/* 
 * Сущность документа  
 */
class dbMongoDocument {
	
	protected 
		$_collection, 
		$_data;
	
	public function __construct($data = null  , $collection  = null){	
		if(is_array($data)) $this->setRawData($data);
		$this->collection($collection);		
	}

	/**
	 * Задать документ в виде массива
	 */
	public function setRawData($data = array() ){
		return $this->_data = $data;
	}

	/**
	 * Получить документ в виде массива
	 */
	public function getRawData(){
		return $this->_data;
	}
	
	/** 
	 * Получить ID 
	 */
	public function getId() {
		if (!isset($this->_data['_id'])) return false;
		return dbMongoId($this->_data['_id']);
	}
	
	/**
	 * Обновить запись
	 * @param  $fields записи которые нужно обновить
	 * @param  $upsert добавить к документу поля.
	 */
	public function update($fields, $upsert = true ) {
		$this->isInstanceCollection();	
		$id = $this->getId();
		if(!$id) return false;
		
		$options = array('upsert'=> (bool)$upsert);

		return $this->_collection->update(array(
			'_id' => $id->getId()
		), $fields, $options );
	}
	
	/**
	 * Сохранить документ в указанную коллекцию или текущею
	 * @param  $collection  dbMongoCollection  задать коллекцию
	 */
	public function save($collection = null){
		$this->collection($collection);	
		$this->isInstanceCollection();
		return $this->_collection->save($this);	
	}
	
	/**
	 * Выкидывает исключения если dbMongoCollection не задан
	 */
	private function isInstanceCollection(){
		if (!$this->_collection instanceOf dbMongoCollection )
			throw new CException('dbMongoCollection не задан');
	}
	
	/**
	 * Задать документу коллекцию
	 * @param $collection dbMongoCollection
	 */
	public function collection($collection = null) {
		if ($collection instanceOf dbMongoCollection ) $this->_collection = $collection;
		return $this->_collection;
	}
	
	
	
	/**
	 * Удалить документ из коллекции
	 */
	public function remove(){
		$this->isInstanceCollection();	
		return $this->_collection->remove($this);
	}
	
	
}
