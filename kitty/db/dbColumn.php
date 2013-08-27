<?
namespace kitty\db;
use \kitty\web\arr;	
/**
 *  Колонка таблицы
 *
 **/	
	

	
	
	
class dbColumn {
		
	public
		$name,                  // название поля
		$typePHP,               // тип поля в PHP
		$type,                  // тип поля int bool string
		$typeRaw,               // тип поля, как указано в схеме
		$size,                  // размер поля
		$allowNull,		        // указывает на NULL 
		$defaultValue,          // значение по умолчанию
		$isPrimaryKey,          // это поля первичный ключ.
		$autoIncrement,		    // это поля является автоинкрементным
		$comment,               // комментарий к полю	
		$isForeignKey = false;  // это форкейключ 
		


	public function __construct( $column  , $driver ){
		//pre($column);
		//$column = array_change_key_case($column);
		
		switch( strtolower($driver) ){
			case 'mysql':
				$this->name = $column['Field'];
				$this->allowNull = $column['Null'] === 'YES';
				$this->isPrimaryKey = strpos($column['Key'], 'PRI') !== false;
				$this->autoIncrement = stripos($column['Extra'], 'auto_increment') !== false;
				$this->comment = arr::get($column,'Comment',null);
				$this->defaultValue = $column['Default'];
				$this->typeRaw = $column['Type'];
				$this->parseTypeColumn($this->typeRaw);
			break;
			case 'sqlite':
				$this->name = $column['name'];
				$this->allowNull = (bool)$column['notnull'];
				$this->isPrimaryKey = (bool)$column['pk'];
				$this->defaultValue = $column['dflt_value'];
				$this->typeRaw = $column['type'];
				$this->parseTypeColumn($this->typeRaw);			
			break;
		}		
	}
	
	/* Получаем размер поля и тип поля */
	public function parseTypeColumn($str){
		if(preg_match('/^(?P<type>\w+)(?:\((?P<size>[^\)]+)\))?/', $str , $matches)){
			$this->type =  $matches['type'];
			$this->size =  arr::get($matches,'size',null);
		}
		return '';
	}
}
