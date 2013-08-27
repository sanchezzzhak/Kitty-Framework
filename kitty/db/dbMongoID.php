<?
namespace kitty\db;

/*
 * Монго ID 
 **/
class dbMongoID {
	
	protected $_mongoID;
	
	/**
	 * Получить указанный MongoId в строчном варианте
	**/
	public function __toString() {
		return (string)$this->_mongoID;
	}
	
	/**
	 * Получить MongoId
	**/
	public function getID() {
		return $this->_mongoID;
	}
	
	/**
	 * Задать MongoId
	 **/
	public function setID( \MongoID $id) {
		return $this->_mongoID = $id;
	}
	
	/*
	 * Задать MongoID через конструктор
	 * @param $base Идентификатор длина 24 в случаи,
	 * если идентификатор неверен, Mongo создаст его сам
	 **/
	public function __construct($base = null) {
		if ($base instanceOf \MongoID) {
			$this->setID($base);
		} else {
			$this->setID(new \MongoID($base));
		}
	}
}
