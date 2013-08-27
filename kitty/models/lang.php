<?

/**
* Class Model lang
* Модель список языков
* 
*/

class lang extends \kitty\db\model {

	static public function model(){
		return parent::make(__CLASS__);
	}
	
	public $_table = 'lang';	
	public $_pk = 'id';
	public $_db = 'default';
	
	public $_attr = array(
		'id' => array('name'=>'id'),
		'code' => array('name'=>'varchar(2)'),
		'name' => array('name'=>'varchar(62)'),
		'default' => array('name'=>'tinyint(1)'),
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