<?

	/**
	 * Контроллер по умолчанию
	 * Прификс _controller Обезателен в каждом классе контролера
	 */
	
	class default_main_controller extends \kitty\app\controller {
		
		public $layout = 'main'; // Слой
				
		public function before(){} // вызов до актиона
		
		public function actionIndex(){	// страница admin/
			$this->render('index');
		}
		
		public function after(){} // после актиона
		
	}
	
	
	

	
	
	
	
?>