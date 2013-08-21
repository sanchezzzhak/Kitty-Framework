<?
if (!defined('doc_root')) exit('No direct script access allowed');
	


	
 /*
 * Монго коллекция 
 **/
 
class dbMongoCollection {
	
	protected
		$_collection, 
		$_db;
	
	public function __construct(  $collection , dbMongo $db){
		$this->_collection = $collection;
		$this->_db = $db;
	}
	
	
	public function collection(){
		return $this->_collection;
	}
	
	
	/*
	 * Количество записей в коллекции 
	 **/
	public function count() {
		return $this->_collection->count();
	}
	
	/* Имя коллекции */
	public function getName() {
		return $this->_collection->getName();
	}
	
	/*
	 * Удалить коллекцию
	 **/
	public function drop() {
		return $this->_collection->drop();
	}
	
	/*
	 * Удалить все индексы	
	 **/
	public function dropIndexes() {
		return $this->_collection->deleteIndexes();
	}
	
	/*
	 * Удалить указанный индекс
	 * @param  $field  названия поля по которому сделан индекс
	 **/
	public function dropIndex($field) {
		return $this->_collection->deleteIndex($field);
	}
	/*
	 * Очистить всю коллекцию документов
	**/
	public function clear() {
		return $this->_collection->remove(array());
	}
	
	/**
	 * Удалить документ
	 * @param  $query  может быть dbMongoDocument, dbMongoId, MongoId или массивом  по которому,
	 * удаляется значение 
	 * @return  bool|array
	 **/
	public function remove($query) {
		if ($query instanceOf dbMongoDocument ) 
			$query = $query->getId(); 
			
		if ($query instanceOf dbMongoId ){
			$query = array('_id' => $query->getId());
		}elseif($query instanceOf MongoId ){
			$query = array('_id' => $query );
		}	
		return $this->_collection->remove($query,true);
	}

	/*
	 * Выполнить запрос и получить MongoCursor
	 * @param  $query  Условие в виде массива
	 * @param  $fields Какие поля нас интересуют, по умолчанию все.
	 * @return MongoCursor
	**/
	
	public function find($query , $fields = array() ){
		return $this->_collection->find($query ,  $fields );
	}
	
	/**
	 * Найти документ 
	 * @param  $query  Запрос в виде массива
	 * @param  $fields Поля которые мы хотеле бы видить в результате 
	 * @param  $entity флаг указывает какой мы хотим получить результа
	 * 		   В виде модели (true) или курсора (false)
	 **/
	public function findOne( $query, $fields = array() , $entity = true){
		
		if( $query instanceOf dbMongoId )
			$query = array('_id' => $id->getID() );
			
			$values = $this->_collection->findOne($query , $fields);
		
			if (!$entity) return $values;
			
			return new dbMongoDocument($values, $this);	
	}
	
	
	public function save($data){
		if($data instanceOf dbMongoDocument){
			$data = $data->getRawData();
		}
		return $this->_collection->save($data);
	}
	
	public function insert(){
		
	}
	
	/*
	 * обновить запись(и) в коллекции 
	 * @param  array $query критерий поиска
	 * @param  array $values вставляемые или обновляемые записи
	 * @param  array $options: 
	 * 		upsert true заменить или добавить к текущему найденому документу поля  
	 *              
	 **/
	public function update($query, $values, $options = array() ) {
		return update($query, $values , $options);
	}
	
	
	/* 
	 * Начать строить запрос в dbMongoQuery 
	 * @param arg1 названия поля || запрос в виде field1 > 21, field2 < 25
	 * @param arg2 значение
	 * @return dbMongoQuery
	 * 
	 * @usage 
	 * ->query('foo','=',1)->cursor();
	**/
	public function query() {
		$query = new dbMongoQuery($this);
		if (func_num_args()) {
			$query = call_user_func_array(array($query, 'query'), func_get_args());
		}
		return $query;
	}
	
}


?>