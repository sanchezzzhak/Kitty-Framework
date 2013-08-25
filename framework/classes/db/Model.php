<?
/**
* Class Model
* Модель
* Fr None
*/

abstract class model {
		
	const
	 HAS_ONE    = 1,  // один-к-одному с другой моделью
	 HAS_MANY   = 2;  // один-ко-многим

	protected
	 $_edit_data   = array(),       # Список полей которые были отредактированы
    // $_load_column_schema = false,  #
    // $_is_load_schema = false,      # Означает что схема уже загружена
     $_new         = false;         # Если новая вставка true
    	 
	 
	public
	 $_db      = 'default',  # Какая БД сохранит модель.
	 $_table   = '',         # Название таблицы или коллекции 
	 $_errors  = array(),    # Ошибки текущей модели
	 $_attr    = array(),    # Атрибуты таблицы или колекции 
	 $_data    = array(),    # Текущие данные записи 
	 $_pk_id   = 0,          # PK  ID значение
	 $_pk      = '',         # Имя PK названия атребута
	 $_relation = array();   # Данные связанной модели


	
	
	
	/**
	 * Конструктор
 	 **/
	public function __construct(){ 
		$this->setRecordStatus(true);
	}
	
	/**
	 * создать указаную модель.
	 **/
	public static function make($className){
		$model = new $className(null);
		return $model;
	}
	
	/**
	 * Получить список атребутов
	 **/
	public function getAttr(){
		return $this->_attr;
	}
	
	/**
	 * Получить значение атрибута
	 **/
	public function getAttrValue($key){
		return isset($this->_data[$key]) ? $this->_data[$key]: null;
	}
	
	/**
	 * Получить статус модели что делать с моделью
	 * Новая запись true, обновляемая false
	 **/
	public function getRecordStatus(){
		return (bool)$this->_new;
	}
	
	/**
	 * Установить статус модели новая или редактируемая.
	 **/
	public function setRecordStatus($flag = false){
		return $this->_new = (bool)$flag;
	}
	
	/**
	 * Загрузка данных в модель в указанные атребуты
	 * @param mixed array $data
	 **/
	public function wrape($data){
		if(func_num_args()==0) return false;
			foreach(func_get_args() as $data){
				if(is_array($data))
					foreach($this->_attr as $key=> $attr){
						if(isset($data[$key])) $this->setAttrValue($key,$data[$key]);
					}
			}
		return false;
	}
	
	/**
	 * Прямое обращение к dbFinder 
	 * @param  $relations  список связанных моделей которые нужно подключить с результатом
	 * @param  $options    массив парамеров. array ( 'where' => ... , 'order'=> ...  )
	 **/
	public function finder($relations = array(), $options = array() ){;
		$f = new dbFinder($this);
		return $f->__find( $relations, $options);
	}
	
	
	/*
	 * Получить модель по условию
	 * @param  $where  array(
	 *          'id'   => array( 
	 * 				'='  => ':id',
  			    	'>=' => ':id',
					'=:id',
 				), array('and'), 
	 *        )
	 * @param  $params  плайхосты
	 * @param  $order   массив array('t0.id'=>'ASC|DESC')
	 * @param  $relations  список связанных моделей которые нужно подключить с результатом
	 **/
	public function find( 
		$where, 
		$params = array(), 
		$order = array(),
		$relations = array()
	){
		$f = new dbFinder($this);	
		return $f->find( $where, $params, $order, $relations );
		
	}
	
	/*
	 * Получить модель по PK
	 * @param  $id  ID модель
	 * @param  $relations  список связанных моделей которые нужно подключить с результатом
	 **/
	public function findByPk( $id , $relations = array() ){
		$f = new dbFinder($this);	
		return $f->findByPk($id , $relations);
	}
	
	/**
	 * Найти все модели по условию
	 * @param  $where  array(
	 *          array( 'id'   => array( '=' => ':id' , 'IN' => array(1,2,3) ),
	 * 			       'name' => array( 'LIKE'=>':like' )
	 *          ), 'AND|OR' , array( ... ) 
	 *        )
	 * @param  $params  плайхосты
	 * @param  $order   массив array('t0.id'=>'ASC|DESC')
	 * @param  $relations_set  список связанных моделей, которые нужно подключить с результатом
	 * @param  $offset  начало позиции 
     * @param  $limit   лимит результа 	 
	 **/
	public function findAll( 
		$where = '' , 
		$params = array(), 
		$order =array(), 
		$relations_set = array(),
		$offset=0, 
		$limit=0, 
		&$count=null
	){ 
		$f = new dbFinder($this);
		return $f->findAll($where,$params,$order,$relations_set, $offset, $limit, $count);
	}
	/**
	 * Для получения значения модели.
	 * @param  $key имя атребута или названия связи 
	 **/
	public function __get($key){ 
		if(isset($this->_relation[$key])){
			return $this->_relation[$key];
		}elseif(isset($this->_data[$key])){
			return $this->_data[$key];
		}	
	}
	
	/**
	 * Установить значение у модели
	 * @param  $key имя атребута
	 * @param  $value значение атребута
	 **/
	public function setAttrValue($key,$value){
		if(!isset($this->_attr[$key])){
			$this->addError($key,' Свойство: '. $key .' не найдено');
			//return false;
		}else{
			$this->_data[$key] = $value;
			if($key==$this->_pk){ 
				$this->_pk_id = $value;
				$this->setRecordStatus(false);		
			}
		}
	}
	
	/*
	 * Установить связь у модели можно даже пихать массив  
	 * @param  $key имя атребута
	 * @param  $value значение атребута
	 **/
	public function setRelationValue($key_name,$value){
		$relations = $this->relation();
		if(isset($relations[$key_name])){
			$this->_relation[$key_name] = $value;	
	    }
	}
	
	
	/**
	 * Установить значение у обьекта
	 * @param $key    Ключ
	 * @param $value  Значение 
	 **/
	public function __set($key , $value = null){
		if(!is_array($value) && !$this->setAttrValue($key,$value)){
			return false;
		}elseif(!$this->setRelationValue($key,$value)){
			return false;	
		}
		return true;		
	}
	
	/**
	 * В этом методе описываются связи моделей в виде массива.
     * [Название связи [ Тип связи, Модель, как происходит связь HAS_ONE, HAS_MANY]]	 
	**/
    public function relation(){
		return array();
	}
	
	/**
	 * В этом методе описываются правила валидации модели
	 * Первый параметр список полей через запятую, второй параметр тип фильтра
     * Не обязательный параметр message это кастомное сообщение ошибки.
	 *   :attr имя текущего атрибута 
	 * Ниже представлены примеры фильтров.
	 
		Обязательные поля + проверка что значение не пустое 
	 * array('список полей', 'required' , 'message'=> 'Свое сообщение :attr ' ),
		Фильтер по длине
	 * array('список полей', 'length', 'min'=>0, 'max'=> 255),
		Фильтер по регулярке
	 * array('список полей', 'match' , 'pattern'),               			 
		Проверка на тип
	 * array('список полей', 'type', 'is' => 'int|integer|string|float|array|boolean|bool'  ),            
	 	Проверка на email
	 * array('список полей', 'email'),                           			 
		Проверяет наличия значения в массиве
	 * array('список полей', 'in' , 'arr' => array() ),          
	 **/ 
	public function rules(){
		return array();
	}
	
	/**
	 * Удалить модель и если есть $relations
	 **/
	public function delete( $relation = true  ){
		if(!empty($this->_pk_id)){
			$db = db::make($this->_db);
			
			
		}
	}
	
	
	/**
	 * Сохранить модель
	 * @param  bool  $validation валидация перед сохранением 
	 * @param  array $relation какие связанные обьекты сохранить
	 */
	public function save($validation = false , $relation_save = array() ){
		if( $validation ) $this->validation();
		if(count($this->_errors)) return false;
			$db = db::make($this->_db);
			$return = false;
			$setData  = array();

		foreach($this->_attr as $attr_name =>$attr){
			$setData[$attr_name] = arr::get($this->_data,$attr_name,'null');
		}
		// insert
		if($this->getRecordStatus()){
			$id = $db->insert($this->_table,$setData);
			if($id>0){ 
				$this->{$this->_pk} =  $this->_pk_id = $id;
				$this->setRecordStatus(false);
				$return = true;
			}
		// update	
		}elseif(is_numeric($this->_pk_id) && $this->_pk_id > 0){
			$id = $db->update($this->_table, $setData , "`{$this->_pk}`=:_pk_id",array(
				":_pk_id"=> $this->_pk_id 
			));
			$return = true;
		}
		
		if( $return ){	
			$relation = $this->relation(); // список связей
			// Сохранить связанные модели
			foreach($this->_relation as $name_relation => &$model){
				
				$setData = array();	
				// Если это модель по типу HAS_ONE
				if(isset($relation[$name_relation]) 
				&& !is_array($model) 
				&& in_array($name_relation,$relation_save) ){
					
					if($relation[$name_relation][0]==model::HAS_ONE ){
						$relation_fk = $relation[$name_relation][2];
						$model->{$relation_fk} = $this->_pk_id;
					}
					
					foreach($model->_attr as $attr_name =>$attr){
						$setData[$attr_name] = arr::get($model->_data,$attr_name,'null');
					}
					
					// save
					if($model->getRecordStatus()){
						$id = $db->insert($model->_table,$setData);
						if($id>0){ 
							$model->{$model->_pk} =  $model->_pk_id = $id;
							$this->setRecordStatus(false);
							$return = true;
						}
					}elseif(is_numeric($model->_pk_id) && $model->_pk_id > 0){
						$id = $db->update($model->_table,$setData,"`{$model->_pk}`=:_pk_id", array(
							":_pk_id"=> $model->_pk_id 
						));
						$return = true;
					}
					
					// если эта модель по типу HAS_MANY тут происходит рекурсия...
					}elseif(is_array($model) && in_array($name_relation,$relation_save) ){
						$relation_fk = $relation[$name_relation][2];
					foreach($model as &$item_model){
						$item_model->{$relation_fk} = $this->_pk_id;
						$item_model->save(true);
					}
					
				}
			}
			
		}
		
	    return $return;
	}
	
	/**
	 * Проверяет модель на валидность заполненных данных
	 **/
	public function validation(){
		$validator = new Validator($this->_data, $this->rules() );
		$this->_errors = $validator->run();
	}
		
	/**
	 * Добавить в список ошибку.
	 * @param  $attr_name  имя атрибута
	 * @param  $error  текст ошибки
	 **/
	public function addError($attr_name , $error = ''){
		/*if(isset($this->_errors[$attr_name]) 
		&& array_search($error,$this->_errors[$attr_name])!==false ) return;
		*/
		$this->_errors[$attr_name][] = $error;
		
	}
	
	/**
	 * Получить список ошибок текущей модели
	 **/
	public function getErrors(){
		return $this->_errors;
	}
	
	/**
	 * Получить ошибки связанных моделей
	 * @todo  Можно потом будет сделать рекурсию на под связи.
	 **/
	public function getErrorsRelations(){
		$arrErrors   = array();
		foreach($this->_relation as $r_name=> $relation){
			if(!is_array($relation)){
				foreach($relation as $rel_id => $rel){
					$arrErrors[$r_name][$rel_id] = $rel->getErrors();
				}
			}else{
				$arrErrors[$r_name]= $relation->getErrors();
			}
		}
		return $arrErrors;
	}
	
}




?>