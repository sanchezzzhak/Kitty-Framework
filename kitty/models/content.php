<?

/**
* Class Model content
* Модель для работы со страницами
* 
*/

class content extends model {

	static public function model(){
		return parent::make(__CLASS__);
	}
	
	public $_table = 'contents';	
	public $_pk = 'id';
	public $_db = 'default';
	
	public $_attr = array(
		'id'         => array('type'=>'int(11)'),
		'code'       => array('type'=>'varchar(255)'),
		'parent_id'  => array('type'=>'int(11)'),
	
	);
	
	public function relation(){
		return array(
			'page' => array(self::HAS_MANY  , 'content_page', 'content_id'),
			
		);
	}
	
	
	
	public function rules(){
		return array(
			array('code', 'required'),
			array('code', 'match', 'pattern'=> '/^([a-z0-9]+)$/i'),                        
		);
	}

	
}




?>