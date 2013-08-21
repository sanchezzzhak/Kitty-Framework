<?
	if (!defined('doc_root')) exit('No direct script access allowed');
	
/*
 * Конструктор запросов для Mongo 
 */
class dbMongoQuery implements IteratorAggregate {
	
	protected 
	$_collection,         # класс dbMongoCollection
	$_conds  = Array(),   # условия в виде массива
	$_sort   = Array(),   # сортировка 1 ASC -1 DESC
	$_offset = -1,
	$_limit  = 0,
	$_result = Array();   # какие поля нужны в результате 
	
	
	/**
	 * Skip Начало позиции 
	**/
	function offset($offset=0) {
		$this->_offset=(int)$offset;
		return $this;
	}
	
	/**
	 * Лимит записей 
	 * @param $size
	 * @return dbMongoQuery
	 */
	function limit($size) {
		$this->_limit=(int)$size;
		return $this;
	}
	
	
	
	
	// Карта соответствия sql операторов => mongo операторам
	private $_operator = array(
		'>'       	  => '$gt',
		'>='      	  => '$gte',
		'<'       	  => '$lt',
		'<='      	  => '$lte',
		'!='      	  => '$ne',
		'size'	  	  => '$size',
		'exists'  	  => '$exists', // true
		'not exists'  => '$exists', // false
		'not in'  	  => '$nin',    // array
		'in'      	  => '$in',		// array
		'all'     	  => '$all',	// array
		'%'       	  => '$mod',	// array(0,1)
		'where'       => '$where',  // this.field1==this.field2
	);
	
	// $elemMatch
	// Logical Query Operators
	# array('$and','$nor','$not','$or');
	
	/**
	 * Добавить условие в выборку
	**/
	public function conds($name , $operator = null , $value = '' ){
		if(!isset($this->_conds[$name])) $this->_conds[$name] = null;
		// Если строка
		if(empty($operator) || $operator=='=' || $operator=='=='){
			$this->_conds[$name] = $value;
			// если другие операторы
			}elseif(isset($this->_operator[$operator])){
				$this->_conds[$name] = arr::merge( 
				$this->_conds[$name], 
			array( $operator => $value) 
			);
		}
		return $this;
	}
	
	
	/**
	 * Запрос 
	 * @param  	$key  array( 'field' => array('$gl' => lt ))
	 * 
	**/
	public function query($key , $value = null ){
		if(is_array($key)){
				foreach($key as $k=>$val) {
					if(!isset($this->_conds[$k])) 
						$this->_conds[$k] = null;
					if ( is_array($val) && is_array($this->_conds[$k]) )
						$this->_conds[$k] += $val;
					else
						$this->_conds[$k] = $val;
				}
			}elseif($value){
				$this->_conds[$key] = $value;
		}
		
		return $this;
	}
	
	
	
	// Генерирует представление запроса... 
	public function getQueryCode(){
		$collection = (string)$this->_collection;
		$str = 'db.'.$collection .'.find('.json_encode($this->_conds).',{})';
		return $str;
	}
	
	// Сортирует указаное поле по ASC в Cursor
	public function asc($name = '_id'){
		$this->_sort[$name] = 1;
		return $this;
	}
	// Сортирует указаное поле по DESC в Cursor
	public function desc($name = '_id'){
		$this->_sort[$name] = -1;
		return $this;
	}
	
	/*
	 * Контруктор
	**/
	public function __construct($collection){	
		if($collection instanceof dbMongoCollection){
			$this->_collection = $collection;
			return;
		}
		throw new CException('Переданный аргумент в ' 
			. get_class($this) 
			.'::__construct не является dbMongoCollection');
	}
	
	/**
	 * Выполнить запрос и получить dbMongoCursor
	**/
	public function cursor(){
		$cursor = new dbMongoCursor(
			$this->_collection->find($this->_conds , $this->_result),
			$this->_collection
		);
		
		if ($this->_offset>=0)  $cursor->skip($this->_offset);
		if ($this->_limit>0)    $cursor->limit($this->_limit);
		if(count($this->_sort))	$cursor->sort($this->_sort);
		return $cursor;
	}
	
	/**
	 * Возвращаем внешний итератор для обхода результата в цикле 
	**/
	public function getIterator() {
		return $this->cursor();
	}
	
	
	
}
