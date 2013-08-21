<?
	/**
	 * Контроллер просмотор страниц
	 * Прификс _controller Обезателен в каждом классе контролера
	 */

class page_controller extends controller {
	
	public $layout = 'page';   // Слой для простых страниц 
	
	/*
	 * Показать страницу 
	 **/
	public function actionId(){
		$url = Router::getUrl();
		if(preg_match('#^/(?P<lang>([a-z]){2})/(?P<code>[^.,;?\n]+)#u',$url,$page)){
			$code      = ltrim($page['code'],'/');
			$lang = ltrim($page['lang'],'/');
		}elseif(preg_match('#^/(?P<code>[^.,;?\n]+)#u',$url,$page)){
			$code = ltrim($page['code'],'/');
		}
		$lang_id = 1;
		//$lang = lang::model()->findAll();
		
		$content = content::model()->find(
			// Условия поиска
			array('content.code'=> array('='=>':code')	),  
		    // prepare param
			array(':code'=>$code ),
			// сортировка основной модели по полям
			array(),
			 // relation ONE_MANY
			array(
			// использовать связь page
			 'page'=>array(
				// условия для связи page
				'conditions'=>array('lang_id'=> array('=' => ':lang_id' )),
				// prepare param для связи page
				'params' => array(':lang_id' => $lang_id) 
			 ))
			
		);
		
		//pre($content);
		
		$this->render('page/id' );
	}
	

	
}

?>