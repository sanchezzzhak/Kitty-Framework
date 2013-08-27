<?
namespace kitty\db;
use \kitty\web\arr;

/**
 * Поиск для модели 
 **/
	 
	 
	class dbFinder {
		
		
		
		private 
			$_db,
			$_class;
		
		
		
		
		public function __construct($_model = null ){ 
			$this->_class = $_model;
			$this->_db = db::make($this->_class->_db);
		}
 

		/**
		 * Задать условия в виде массива для поля.
		 * @param  $arrConds  массив условий для поля 
			array('='=>':id',	'>'=>0,		'<'=0, '>=:id'  ),		 
	     * @param  $column_name  имя поля
		 * @return  string sql условия для указанного поля 
		 **/
		private function setCondsArr($arrConds , $column_name, $and = false ){

			$sql = '';
			foreach($arrConds as $operator => $value){	
				if($and) $sql.= " AND ";
				if( in_array(strtoupper($value),array('AND','OR','(',')' ))){
					$sql.= " ".$value;
					$and = false;
				}elseif(is_array($value) && strtoupper($operator)=='IN'){
					if($and) $sql.= " {$column_name}";
					$sql.= " ".$operator."(".implode(',',$value) . ")";
					$and = true;
				}elseif(empty($operator)){
					if($and) $sql.= " {$column_name}";
					$sql.= " ".$value;
					$and = true;
				}else{
					if($and) $sql.= " {$column_name}";
					$sql.= " ".$operator." ".$value;
					$and = true;	
				}
			}
			return $sql;
		}
 
 
 
 
 
 
		/**
		 * Поиск по модели , основной поиск 
		 * 
		 * @param  $relations_set  массив, какие связи моделей использовать в поиске.
		 * array(
		 * 		'page'
		 * )
		 * @param  $options['where']  инструкция для поиска в стиле mongo , 
		 * @example  как заполнять поля:
		 * 	У нас есть основная модель content, со связью HAS_ONE, 
		 * 	моделью content_page связь названа page
		 * 
		 * 	<имя модели>.id заменятся автоматом на t0.id
		 * 	<имя связи>.id заменятся на t1.id
		 * 	Пример запроса 
		    array(
		  		'content.id' => array( '=' => ':id' , '>'=>0 , '<'='0' ), 
		  		 array('OR'),
		  		'page.id' => array( 'in' => array(1,2,4,6) ),		 
		  	)
		 * 
		 * @param  $options['order']    инструкция для сортировки результата
		 * @param  $options['offset']     
		 * @param  $options['limit']    лимит записей
		 * $param  $count  количество записей без учета лимита.
		 **/

		
		 
		 
		
		public function __find( $relations_set = array(), $options = array() , &$count = null ){

			$sql = array('SELECT'=>"");
			$aliasID = 			 # ID алиаса	
			$i = 0;   			 # инкремент для уникального имени колонки
			$alias_column        # Основная модель  имя колонки = имя алиаса 
			= $rel               # Список связей  = ID alias
			= $alias_rel_column  # Связи модели колонки = имя алиаса 
			= $params = array(); # Prepare PDO параметры 
			
			$table_alias = 't'.$aliasID;  # Алиас главной таблицы
			
			// Алиасы по названию relation|имя модели = табличный алиас.
			$tables_alias[get_class($this->_class)] = $table_alias;
			
			
			$sql['FROM'] = $this->nQuote($this->_class->_table) . " t".$aliasID;
			// Задаем алиасы колонок в массив, попутно прописываем их в SELECT 
			foreach($this->_class->_attr as $attr_name=>$option){
				$alias_name = 't'.$aliasID."_".$i;
				$alias_column[$alias_name] = $attr_name;
				$sql['SELECT'].="{$table_alias}.`{$attr_name}` as ".$alias_name.",";
				++$i;
			}
			
			// Получим список связей у модели.
			$relation = $this->_class->relation();
			
			foreach($relations_set as $name => $conds ){
				
				$r_name = is_array($conds)? $name : $conds; 
				
				
				$relation_type = $relation[$r_name][0];  # Тип Связи
				$model_name = $relation[$r_name][1];     # Имя relation модели 
				
				if(isset($relation[$r_name],$model_name) 
					&& class_exists($model_name) 
					&&  model::HAS_ONE ==$relation_type ){
						
					$i=0; 
					$aliasID++;
					$foreignKey = $relation[$r_name][2];
					
					$model = new $model_name;
					$tables_alias[$r_name] = "t".$aliasID; 
					$rel[$r_name] = $aliasID;
					
					$sql['JOIN'][] = "LEFT OUTER JOIN " 
					. $this->nQuote($model->_table) 
					. " t{$aliasID} ON ( t{$aliasID}.{$foreignKey} = {$table_alias}.{$this->_class->_pk} )";
					
					foreach($model->_attr as $attr_name=> $option ){
						$alias_name = 't'.$aliasID."_".$i;
						$alias_rel_column[$aliasID][$alias_name] = $attr_name; 
						$sql['SELECT'].=" t".$aliasID.".`".$attr_name."` as ".$alias_name.",";
						++$i;
					}	
				}	
			}	
			//pre($alias_rel_column);
			$arrID =   # список ID которые мы нашли 
			$_records_result = array();   
			
			if(!empty($sql['SELECT'])){				
				if(isset($options['params'])) $params = $options['params'];	
				/* WHERE */
				$where  = '';
				if(isset($options['where']) && is_array($options['where'])){
					foreach($options['where'] as $column => $conds){
						$column_name = '';
						if(($pos = strpos($column,"."))!==false 
						  && ($ta  = substr($column,0,$pos))!==false 
						  && isset($tables_alias[$ta]) ){
							
							$column_name = $tables_alias[$ta].".".$this->nQuote( str_replace($ta.".","",$column));
						}else{
							$column_name = $column;
						}
						$where.= $column_name;
						
						// Условия в виде массива
						if(is_array($conds)){
							$where.= $this->setCondsArr($conds,$column_name);
						// Условия в виде строки 
						}elseif(is_string($conds)){
							$where.=" ".$conds;
						}
					}
				}elseif(isset($options['where']) && is_string($options['where'])){
					$where = trim($options['where']);
				} 
				
				/* WHERE - Склеиваем запрос  */
				$sql_query = "SELECT "
				. (!is_null($count) ? ' SQL_CALC_FOUND_ROWS  ':'')
				. trim($sql['SELECT'],',') . " FROM "
				. $sql['FROM']." ".( isset($sql['JOIN']) ? implode(' ',$sql['JOIN']) : "" );
				
				if(!empty($where)) $sql_query.=" WHERE ".$where;
				
				if(isset($options['limit']) && $options['limit']>0){
					$sql_query .= " LIMIT " . $options['limit'];
					if(isset($options['offset']))
						$sql_query .= " OFFSET " . $options['offset'];
				}
				// Выполняем запрос
				// pre($sql_query);
				$arrData = $this->_db->prepare($sql_query)->execute($params)->fetchAll();
				if($count!==null){
					$row =  $this->_db->prepare('SELECT FOUND_ROWS() as `count`')->execute()->fetch();
					$count = $row['count'];
					
				}
				// pre($arrData);
				// Заполняем данными
				if($arrData){
					
					$rel_flip = array_flip($rel);
					$alias_column_flip = array_flip($alias_column);
					foreach($arrData as $itemArr){
						// Получаем алиас колонки 
						$pk_alias_name = $alias_column_flip[$this->_class->_pk];
						
						$arrID[$pk_alias_id] =      # Cписок PK ID
						$pk_alias_id   =            # Имя PK
						$itemArr[$pk_alias_name];   # Значение 
						
						// Создаем базовую модель и заполняем  
						$_records_result[ $pk_alias_id ] = new $this->_class; 
						
						foreach($itemArr as $key =>$row){
							
							$index = explode('_',$key); // Алиас 
							$id_rel = trim($index[0],'t'); // ID Aлиаса
							if($id_rel==0 && isset($alias_column[$key])){
								$_records_result[ $pk_alias_id ]
								->setAttrValue($alias_column[$key],$row);	
							// Заполняем связи HAS_ONE
							}elseif(isset($rel_flip[$id_rel], $alias_rel_column[$id_rel][$key])){
								$relation_name =  $rel_flip[$id_rel];
								$model_name = $relation[$relation_name][1];	
								if(!isset($_records_result[$pk_alias_id]->_relation[$relation_name])){
									$_records_result[$pk_alias_id]->_relation[$relation_name] = new $model_name;
								}
								$_records_result[$pk_alias_id]->_relation[$relation_name]->setAttrValue($alias_rel_column[$id_rel][$key],$row);
							}
						}
					}
					// for HAS_MANY, MANY_MANY, Отдельный запрос к БД
					
					foreach($relations_set as $name => $conds ){
					
						$r_name = is_array($conds) ? $name : $conds;
					
						$relation_type = $relation[$r_name][0];  # Тип Связи
						$model_name = $relation[$r_name][1];     # Имя relation модели 
						
						if(isset($relation[$r_name],$model_name ) 
						&& count($arrID)>0 
						&& class_exists($model_name))
						if(model::HAS_MANY==$relation_type  ){
							
							$model = new $model_name;
							$relation_fk_name = $relation[$r_name][2];
							$sql_many = 'SELECT * FROM '. $this->nQuote($model->_table).
							" WHERE {$relation_fk_name} IN(".implode(',',$arrID).") ";
							
							$params = array();
							if(isset($conds['conditions']) && is_array($conds['conditions'])){
								foreach($conds['conditions'] as $column_name => $item_conds){
								
									// Условия в виде массива
									if(is_array($item_conds)){
										$sql_many.= $this->setCondsArr(
											$item_conds, 
											$this->nQuote($column_name),
											true
										);
										// Условия в виде строки 
									}elseif(is_string($item_conds)){
										$sql_many.=" ".$item_conds;
									}
										
								}
								$params = $conds['params'];
								
							}
							//pre($sql_many);
							$arrDataMany = $this->_db->prepare($sql_many)->execute($params)->fetchAll();
							foreach($arrDataMany as $row){	
								$pid = $row[$relation_fk_name];
								$pk_id = $row[$model->_pk]; 
								if(!isset($_records_result[$pid]->_relation[$r_name]))
									$_records_result[$pid]->_relation[$r_name] = array();	
								$_records_result[$pid]->_relation[$r_name][$pk_id] = clone $model;
								$_records_result[$pid]->_relation[$r_name][$pk_id]->wrape($row);
								
							}
						}
						
						
						
					}
						
				}
			}
			
			return $_records_result;
		}
		
		
		/**
		 * Найти по первичному ключу запись и все связаные указанные модели в $relations_set
		 **/
		public function findByPk( $id , $relations_set = array() ){	
			$data = $this->__find( $relations_set , array(
				'where' => array(
					't0.id' => array('=' => ':id'),	
				),
				'params'=> array( ':id'=>$id ) ,
				'limit' => 1,
				'offset'=> 0,
			));
			return ($data) ? reset($data) : false;
		}
		
		
		
		/**
		 * Найти по условию модель
		 * @param  $this->_class   класс модель.
		 * @param  $where    условия поиска
		 * @param  $params   плайхост параметры  
         * @param  $relation список связей которые нужны 		 
		 **/
		public function find( 
			$where = array() , 
			$params = array(), 
			$order = array(), 
			$relations_set = array()
		){
			return $this->__find( $relations_set, array(
				'params'=> $params,
				'where' => $where,
				'order' => $order,
				'limit' => 1,
				'offset'=> 0,
			));
			
		}
		
		/**
		 * Найти все модели по условию
		**/
		public function findAll(
			$where , 
			$params = array(),  
			$order = array() , 
			$relations_set = array(),
			$offset = 0,
			$limit  = 0,
			&$count = null 
		){
			$data = $this->__find( $relations_set, array(
				'params'=> $params,
				'where' => $where,
				'order' => $order,
				'limit' => $limit,
				'offset'=> $offset
				),$count
			);
			
			return ($data) ? $data : false;	
		}
		
		/**
		 * Обвернуть таблицы, столбцы в ковычки
		 * @param  string $name 
		 **/
		public function nQuote($name){
			return "`".trim($name,'`')."`";
		}
		
		
		
		
		
		
		
		
		
}

	
?>