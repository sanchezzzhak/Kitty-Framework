<?

	/**
	 * Контроллер по умолчанию
	 * Прификс _controller Обезателен в каждом классе контролера
	 */
	
	class default_main_controller extends controller {
		
		public $layout = '//main'; 		
				
		public function actionIndex(){

			$this->render('index');
		}	
	}
	
	
	

	
	
	
	
?>