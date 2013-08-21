<?

/**
* Class Model content_page
* Модель для работы c сущностью страницы
* 
*/

class content_page extends model {

	static public function model(){
		return parent::make(__CLASS__);
	}
	
	public $_table = 'content_pages';	
	public $_pk = 'id';
	public $_db = 'default';
	
	public $_attr = array(
		'id' => array('type'=>'int(10)'),
		'content_id' => array('type'=>'int(10)'),
		'rcount' => array('type'=>'int(10)'),
		'lang_id'=> array('type'=>'tinyint(3)'),
		'title' => array('type'=>'varchar(255)'),
		'keywords' => array('type'=>'varchar(255)'),
		'description' => array('type'=>'varchar(255)'),
		'content'  => array('type'=>'text'),
	);
	
	public function relation(){
		
	}
	
	
	
	public function rules(){
		return array(
			array('content_id,lang_id', 'required'),                       
		);
	}

	
}




?>