<?

/**
* Class Model lang
* Модель список языков
* 
*/

class lang extends model {

	static public function model(){
		return parent::make(__CLASS__);
	}
	
	public $_table = 'lang';	
	public $_pk = 'id';
	public $_db = 'default';
	
	public $_attr = array(
		'id' => array('type'=>'int(2)'),
		'code' => array('type'=>'varchar(2)'),
		'name' => array('type'=>'varchar(62)'),
		'default' => array('type'=>'tinyint(1)'),
	);
	
	public function relation(){
		return array();
	}
	
	
	
	public function rules(){
		return array(
			array('code', 'required'),                        
		);
	}

	
}




?>