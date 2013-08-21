<?
	/**
	 * Контроллер по умолчанию
	 * Прификс _controller Обезателен в каждом классе контролера
	 */

class default_main_controller extends controller {
	
	public $layout = 'main'; // Слой
	
	public function before(){} // вызов до
	
	public function actionIndex(){	
	
		/*
		$mongo  = db::make('mongo');
		
		$collection = $mongo->getCollection('items');
		
		$query = $collection->query(array(
			'dddd.222'=>array( '$gt'=>111, '$lt'=>2555 )
		));

		foreach($query->cursor() as $item){
			$item->update(array(
				'break' => time()
			
			) );
			break;
		}*/
		
		//$car +1;
		
		
			
		

	
		
		
		
		
		
		
		$this->render('index');
	}
	
	public function after(){} // вызов после
	
}

?>