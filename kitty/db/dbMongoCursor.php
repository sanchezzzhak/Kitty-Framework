<?
namespace kitty\db;

/* 
* Mongo Курсор для итерации в циклах
* 
**/
class dbMongoCursor implements \Iterator {
	
	protected $_cursor , $_collection;
	
	/**
	 * Устанавливаем указатель на курсор
	 */
	public function __construct( \MongoCursor $cursor , $collection  = null) {
		$this->_cursor = $cursor;
		$this->_collection = $collection;
		
	}
	
	/**
	 * Количество записей
	 */
	public function count() {
		return $this->_cursor->count();
	}
	
	/**
	 * Сушность документа
	 */
	public function current() {
		return new dbMongoDocument( $this->_cursor->current() , $this->_collection );
	}
	
	public function key() {
		return $this->_cursor->key();
	}
	
	public function next() {
		return $this->_cursor->next();
	}
	
	
	public function rewind() {
		return $this->_cursor->rewind();
	}
	
	public function valid() {
		return $this->_cursor->valid();
	}
}


