<?
namespace kitty\db;
use \kitty\web\arr;
use \kitty\web\cache;

use \PDO;

	/*
	 * #r Имя проекта r#
	 * #r Лицензия r#
	 * 
	 * Класс фабрика+синглитон
	 * Какую базу или nosqsl хранилище использовать определяется конфигом
	 * 
	 * @usage da::make('<имя конфига'>); // по умолчанию параметр default
	 * 
	 * Подержка Субд: Sqlite, mysql, pgsql, sqlsrv через PDO
	 * 
	 * 
	 * Подержка noSql:
	 *   Mongo:
	 *   Создание запросов результат в виде CursorInterator => Entity( dbDocumentMongo )
	 * 
	 **/
	
	class db  {
		
		private static $_i; 
		
		private function __construct(){}
		private function __clone(){}
	
		/**
		 * Получить обьект для работы с БД  
		 * @param  $name - Указывается названия конфига вслучии одной или несколько паралейных БД
		 * */
		public static function make($name=null){
			if(is_null($name)) $name = 'default'; 
			$conf = \kitty\app\config::get('db'); // список конфигов БД
			
			if(!isset($conf[$name])){
				throw new \kitty\base\ExceptionError('Указанная конфиге, db.{name} не найден', array(
					'{name}'=> $name  )
				);		
			}
			// Тип драйвера
			$driver   =  arr::get($conf[$name],'driver','');

			if(!isset(self::$_i[ $name ]) || is_null(self::$_i[$name]) ) {	
				switch($driver){
			      case 'sqlsrv':
				  case 'pgsql':
				  case 'sqlite':	
				  case 'mysql':
					$db = new dbPDO();
					/* Использовать ли профайл */
					$db->profiler = arr::get($conf[$name],'profiler',false);					
					$db->driver   = $driver;
					$db->connect( 
						$conf[$name]['driver'],
						arr::get($conf[$name],'host',null),
						arr::get($conf[$name],'name' ,''),
						arr::get($conf[$name],'user',null),
						arr::get($conf[$name],'pass',null),
						arr::get($conf[$name],'charset','utf8')
					);	
				  break;

				  case 'mongo':
				  case 'mongodb':
					$db = new dbMongo( 
						arr::get($conf[$name],'host',null),
						arr::get($conf[$name],'persistent',false)
					);
					// Выбираем БД
					$db->selectDb(arr::get($conf[$name],'name',null));
				  break;
  
				}
				self::$_i[ $name ] = $db; // заносим данные в статик переменую	
			}
			
			return self::$_i[ $name ];
		}	
	}
	
	/**
	 * Класс для работы СУБД с поддержкой PDO
	 **/
	class dbPDO {
		
		protected
		  $_pdo = null, 
		  $_statement = null,
		  $_fetchMode = PDO::FETCH_ASSOC,
		  $_cahceAdapter = null,
		  $_tables_schema  = array();
		  
		public 
		  $profile  = false, 
		  $driver   = null;
		
		private 
		  $sql      = null,             // Sql prepare
		  $sql_text = null,             // SQl который обрабатывается в эмуляци
		  $data     = null,   			// Результат от кеша
		  $cache    = false,            // флаг указывает на использование кеширование запросо см метод ->cache(true)
		  $hasActiveTransaction = false,
		  $bound_params   = array();
		
		
		  
		/*
		* Использовать ли кеш?
		* @param  $falg  если больше 0 то указывает на использование кеша.
		*   Также флаг отвечает на сколько кэшировать данные по времени в секундах.
		* 	Внимание метод вызывается до методов ->prepare(...)->execute();
		* @example  
		  db::make()->cache(120)->prepare('Select * From users')->execute()->fetchAll()  		
		*/
		public function cache($flag = 0){
			$this->cache = $flag;
			if(is_null($this->_cahceAdapter) && $this->cache > 0 ){
				$this->_cahceAdapter = cache::make();
			}
			return $this;
		}
		
		

		
		/*
		* Установить режим отдачи результа, по умолчанию FETCH_ASSOC
		* @param  $mode   режим 
		*/
		public function setFetchMode($mode){
			$params = func_get_args();
			$this->_fetchMode = $params;
			return $this;
		}
		
		/* Начать транзакцию */
		public function beginTransaction(){
			if($this->hasActiveTransaction ) return false;	
			return  $this->hasActiveTransaction = $this->_pdo->beginTransaction();
		}
		
		/**
		 * Фиксирует транзакцию
		**/
		public function commit(){
			$this->_pdo->commit();
			$this->hasActiveTransaction = false;
		}
		/**
		 * Откат транзакции
		 **/
		public function rollback(){
			if(!$this->hasActiveTransaction) return false; 
			$this->_pdo->rollBack();
			$this->hasActiveTransaction = false;
		}
		
		/*
		* Получить ссылку на PDO
		*/
		public function getPdo(){
			return $this->_pdo;
		}
		
		/*
		* Получить ссылку на текущий Statement
		*/
		public function getPdoStatement(){
			return $this->_statement;
		}
		/*
		 * Получить тип драйвера который мы используем. 
		 */
		public function getTypeDriver(){
			return $this->driver;
		}
		
		/*
		* Установить и получить соединениие с БД
		* @return this		
		*/
		public function connect(
			$driver = null, 
			$host = null,
			$name = null,
			$user = null,
			$pass = null, 
			$charset = 'utf8'
		){		
			try {
				
				if(in_array($driver,array('pgsql','mysql'))){
					$DSN = $driver . ":host=". $host .";dbname=". $name ;
				}elseif($driver=='sqlite'){
					$DSN = $driver . ":". (empty($name)? ':memory:': $name);
				}elseif($driver=='sqlsrv'){
					$DSN = "sqlsrv:Server={$host};Database={$name}";
				}
			    $this->_pdo =  new PDO($DSN,$user,$pass,
                   array(
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                   ));  
				  
				$driver=strtolower($this->_pdo->getAttribute(PDO::ATTR_DRIVER_NAME));
				
				if(in_array($driver,array('pgsql','mysql')))
					$this->_pdo->exec('SET NAMES '. $this->_pdo->quote( $charset ));
										
			} catch (\PDOException $e) { 
			    throw new \kitty\base\ExceptionError("PDO CONNECTION ERROR: {error} " , array(
					'{error}' => $e->getMessage() )
				);
			}
			return $this; 	    	
		}
		
		/*
		 * Эмуляция плахостов
		 */
		public function emulatePrepare($params = array() ){
			$sql = $this->_statement->queryString;
			if (sizeof($params) > 0) {
				foreach ($params as $key => $value) {
					$sql = str_replace($key, $this->quote($value), $sql);
				}
			}
			if (sizeof($this->bound_params)) {
				foreach ($this->bound_params as $key => $param) {
					$value = $param['value'];
					if (!is_null($param['type'])){
						$value = self::castType($value, $param['type']);
					}
					if (isset($param['maxlen']) && $param['maxlen'] != -1) {
						$value = substr($value,0,$param['maxlen']);
					}
					$sql = str_replace($key, $this->quote($value), $sql);
				}
			}
			$this->bound_params = array();
			return $sql;
		}
		
		/**
		 * Указаное значения переводим в тип для self::emulatePrepare
		 **/
		protected static function castType($value, $type = PDO::PARAM_STR){
			switch ($type){
				case PDO::PARAM_BOOL:
					return (bool) $value;
				break;
				case PDO::PARAM_NULL:
					return null;
				break;
				case PDO::PARAM_INT:
					return (int) $value;
				case PDO::PARAM_STR:
				default:
					return $value;
			}
		}
		
		/*
		* Установка строки с плайхостами
		* @return this  
		*/
		public function prepare($statement, array $driver_options = array() ){
			$this->sql = $statement;
			$this->_statement = $this->_pdo->prepare($statement);
			return $this;
		}
		
		/**
		 * Очистить _statement
		 **/
		private function flush_statement(){
			$this->_statement = null;
		}
		
		
		/*
		* Выполнить запрос
		* @param  $params массив параметров плахостов
		* @return this 		
		*/
		public function execute($params=array()){
			try	{
				$is_execute = true;
				$this->sql_text = $this->emulatePrepare($params);
				$key = 'sql.query'. md5($this->sql_text);
				
				if($this->cache){
					$before = microtime(true);
					$is_execute = (false ==($this->data = $this->_cahceAdapter->get($key))) ? true : false; 
					$after  = microtime(true);
				}
				if($is_execute){
					$before = microtime(true);
						$this->_statement->execute($params);
					$after  = microtime(true);
			
					if($this->cache > 0 ){
						$this->_cahceAdapter->set($key , $this->fetchAll($this->_fetchMode) );
					}
				}
				
				
				/*
				if($this->profile){
					$e      = new Exception;
					$trace  = $e->getTraceAsString();
					profile::add('',$before - $after , $params , $trace);
				}*/
				
			}catch(\Exception $e){
				$errorInfo = $e instanceof \PDOException ? $e->errorInfo : null;
				$message = $e->getMessage();
			}
			
			return $this;
		}
		
		/**
		 * Экранирование данных
		 * PDO::PARAM_STR
		 * PDO::PARAM_INT
		 **/
		public function quote($value , $dataType=PDO::PARAM_STR){
			if(is_null($value) ) { 
				return "NULL"; 
			} 
			return $this->_pdo->quote($value, $dataType);	
		}
		
		
		
		
		/*
		* http://www.php.net/manual/ru/pdostatement.bindparam.php    
		*/
		public function bindParam($name,&$value, $dataType=PDO::PARAM_STR, $length=-1, $driverOptions=null){
			$this->bound_params[$name] = array(
				'value'=>$value,
				'data_type'=>$dataType,
				'maxlen'=>$length
			);
			
			$this->_statement->bindParam($name,$value,$dataType,$length,$driverOptions);
			return $this;
		}
		
		/*
		* http://www.php.net/manual/ru/pdostatement.bindvalue.php
		*/
		public function bindValue($name, $value, $dataType=PDO::PARAM_STR,$length=-1){
			$this->bound_params[$name] = array(
				'value'=>$value,
				'data_type'=>$dataType,
				'maxlen'=>$length
			);
			
			$this->_statement->bindValue($name,$value,$dataType);
			return $this;
		}
		
		/**
		 * Возвращает количество строк, которые были затронуты в ходе выполнения последнего запроса 
		 * DELETE, INSERT или UPDATE
		 * http://php.net/manual/ru/pdostatement.rowcount.php
		 * */
		public function rowCount(){
			return $this->_statement->rowCount();
		}
		
		/*
		* Получить весь результат запроса
		* @param $fetch_style - Стиль вывода результата по умолчанию ассоциативные массивы
		* @param $cursor_orientation - 
		* @param $cursor_offset -
		* @return array	
		*/
		public function fetchAll( $fetch_style = null , $fetch_argument = null, $ctor_args = null){
			if(is_null($fetch_style)) $fetch_style = $this->_fetchMode;
			if($this->cache){
				return $this->data;
			}
			return $this->_statement->fetchAll( $fetch_style);//, $fetch_argument, $ctor_args);
		}
		
		/**
		 * Получить только указанный номер колонки.
	     * @param  $num  номер колонки, нумерация начинается с 0
		 * @see Удобно использовать в запросах, где используется подсчет
		 * @example  
		    db::make()->prepare('SELECT count(id) From users')
		  		->execute()
		 		->fetchColumn(0); 		 
		 **/
		public function fetchColumn($num = 0){
			return $this->_statement->fetchColumn($num);	
		}
		
		/*
		 * Количество колонок в результате.
		 * @example 
		  	db::make()->prepare('SELECT count(id) From users')
		  		->execute()
		  		->columnCount();
		 **/
		public function columnCount(){
			return $this->_statement->columnCount();
		}
		
		
		/*
		 * Получить результат из запроса одну строку или c указаной позиции.
		 * http://php.net/manual/en/pdostatement.fetch.php
		 * @param $fetch_style - Стиль вывода результата
		 * @param $cursor_orientation - Порядок направления курсора
		 * @param $cursor_offset - Начало курсора 
		 * @return array
		 * @example
		 
			$result = db::make()->prepare('SELECT * From users limit 5')->execute();
			// Сдвиг по курсору
			while($row = $result->fetch()):
				pre($row);
			endwhile;
			//
		 	$arr = db::make()->prepare('SELECT * From users limit 5')->execute()->fetch()			 
		 **/
		public function fetch( $fetch_style = null, $cursor_orientation = PDO::FETCH_ORI_NEXT , $cursor_offset = 0){
			if(is_null($fetch_style)) $fetch_style = $this->_fetchMode;
			if($this->cache > 0 ){
				return $this->data;
			}
			return $this->_statement->fetch($fetch_style, $cursor_orientation, $cursor_offset);	
		}
		
		/**
		 * Выполнить запрос 
		 * 
		 **/
		public function query($query, $args = array() , $debug = false) {
			
			$tokens = explode(" ", $query);
			try{
				$this->prepare($query);
				$this->execute($args);
				
				$tokens = strtolower($tokens[0]);
				if($tokens == "select") {
					$mode=(array)$mode;
					$results=call_user_func_array(array( $this->_statement, 'fetchAll'), $mode);
					//$sth->rowCount()
					return $results;
				}
				if($tokens=='insert'){
					return $this->lastInsertId();
				}
			}catch(\PDOException $e) {
				if($debug) pre( 'Query failed: ' . $e->getMessage() );
				return 0;
			}
        return 1;
		}
		
		/**
		 * Последний вставленый в таблицу ID
		 * @return int 
		 */
		public function lastInsertId(){
		   return $this->_pdo->lastInsertId();
		}

	
		
	/*
	* Вставить В БД запись
	* @param $setArray - Массив значений ключи являются столбцами таблицы
	* @return int || bool - Возращяет ID вставки в таблицу иначе false;
    */
    public function insert($table, array $setArray){
		$result = $this->arrayPrepareSqlSet($setArray);
		$sql = "INSERT INTO `".$table."` SET ".$result['sql'];
		if(empty($result['sql'])) return false;
            return $this->query($sql , $result['execute']);
	}
	
	/**
	 * Обновить запись в БД 
	 * @param  string $table - Таблица где будет проводится обновление записи
	 * @param  array  $setArray - Массив значений ключи являются столбцами таблицы
	 * @param  string $where - условия запроса с плайхостами.
     * @param  string $wherePrepare - Массив значенией для параметра where лючи являются столбцами таблицы. 
	 **/
	public function update($table, array $setArray, $where = '', $wherePrepare = array() ){
		$result = $this->arrayPrepareSqlSet($setArray);
		if(empty($result['sql'])) return false;
		$sql = "UPDATE `".$table."` SET ".$result['sql'] . " WHERE ". $where;
		$execute = arr::merge($result['execute'], $wherePrepare);
		return $this->query($sql , $execute);
	}
	
	/* Удалить запись */
	public function delete($table , array $setArray, $where , $wherePrepare = array() ){
		
	}
	
	
	/**
	 * Создать на основе массива SET струку в SQL, ключи должны быть полями таблицы
	 * @param  array $setArray Массив значенией
	 * @return  array(sql,execute)
	 **/
	public function arrayPrepareSqlSet(array $setArray = array() ){
		$sql = "";
		$execute = array();
		foreach($setArray as $key=>$value){ 
			$column = trim($key,":");
			$sql.=" ".$column."=:".$column. ","; 
			$execute[":".$column] = $value;
		}
		$sql = rtrim($sql,",");
		return array('sql'=>$sql,'execute'=>$execute);
	}
	
	
	/**
	 * Список таблиц 
	 **/
	public function show_tables(){
		$arr = array();
		switch($this->driver){
			case 'mysql':
				$_result = $this->prepare("SHOW TABLES")->execute(array());
				while($column= $_result->fetchColumn(0)){
					$arr[] = $column;					
				}
			break;
			case 'sqlite':
				$_result = $this->prepare("SELECT DISTINCT tbl_name FROM sqlite_master WHERE tbl_name<>'sqlite_sequence'")->execute(array());
				while($column= $_result->fetchColumn(0)){
					$arr[] = $column;					
				}
			break;
		}
		return $arr;
	}
	
	
	
	/**
	 * Получить информацию о колонках таблицы
	 * @param  $table  имя таблицы
	 * @param  $reCacheClass:true  перезапись кеша
	 **/
	public function show_columns($table , $reCacheClass = false){
		$data = array();
		if(preg_match('#^([a-z0-9\_]+)$#i',$table)){
			if(!isset($this->_tables_schema[$table]) || $reCacheClass){
				
			switch($this->driver){
				case 'mysql': 
					$sql = " SHOW COLUMNS FROM `".$table."`";
					$result = $this->prepare($sql)->execute();
					while($row = $result->fetch(PDO::FETCH_ASSOC)){
						
						$data[$row['Field']] = new dbColumn($row , $this->driver );
					
					}
				break;
				case 'sqlite':
					$sql="PRAGMA table_info(".$table.")";
					$result = $this->prepare($sql)->execute();
					while($row = $result->fetch(PDO::FETCH_ASSOC)){
						$data[ $row['name']  ] = new dbColumn($row , $this->driver );
					}
				break;
			}
			
			
			
			
				$this->_tables_schema[$table] = $data;
			}else{
				$data = $this->_tables_schema[$table];
			}
		}
		return $data;
	}
	
	public function addColumn($table, $column, $type){
		$sql =  'ALTER TABLE `'.$table. '` ADD ' . $column . ' '.$type;
		$this->prepare($sql)->execute();
	}
	
	public function dropColumn($table , $column){
		$sql =  "ALTER TABLE `".$table."` DROP COLUMN ".$column;
		$this->prepare($sql)->execute();
	}
	
	/**
	 * Переименовать колонку
	 **/
	public function renameColumn($table, $name, $new_name){
		$sql = "SHOW CREATE TABLE `".$table."`";
		$row = $this->prepare($sql)->execute()->fetch(PDO::FETCH_ASSOC);
        $sqldata = $row['Create Table'];
		if(!empty($sqldata) && preg_match_all('/^\s*`(.*?)`\s+(.*?),?$/m',$sqldata,$matches)){
			foreach($matches[1] as $key=>$column){
				if($column===$name){
					$sql ="ALTER TABLE `".$table."` CHANGE `".$name.'` `'.$new_name.'` '
					.$matches[2][$key];
					$this->prepare($sql)->execute();
					return true;
				}
			}
		}else{
			$sql ="ALTER TABLE `".$table. "` CHANGE `".$name."` `".$new_name."`";
			$this->prepare($sql)->execute();
			return true;
		}
		return false;
	}
	
	public function dropForeignKey($table, $name){
		return;
	}
	public function addForeignKey(){
		return;
	}
}



/*
  
	class dbQueryBuilder {

	private $query_build = array();

	public function limit($limit = 30, $offset = null){
		$this->query_build['LIMIT'] = (int)$limit;
		if(!is_null($offset)) $this->offset($offset);
		return $this;
	}

	public function offset($offset = 0){
		$this->query_build['OFFSET'] = (int)$offset;
		return $this;
	}
}
*/
	





?>