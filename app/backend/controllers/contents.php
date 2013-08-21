	<?
	
	class contents_controller extends controller {
		public  $layout = 'main';
		
		public function before(){
			
		}
		
		/**
		 * Создать страницу 
		 **/
		public function actionAdd(){
			$parent = $this->get('parent',0);
			$arrLang = array();
			
			$arrLangModel = lang::model()->findAll();
			foreach($arrLangModel as $lang){
				$arrLang[ $lang->id ] = $lang->_data;
			}
				
			$this->render('/contents/add',array(
				'id'         => 0,
				'parent'     => $parent,
				'code'       => '',
				'arrLang'    => $arrLang,
				'arrContens' => array(),
			));
		}
		
		/*
		 * Редактировать страницу 
		 **/
		public function actionEdit(){
			$id = $this->get('id',0);
			
			$arrLang = array();
			$arrLangModel = lang::model()->findAll();
		
			if($id>0)
				$contens = content::model()->findByPk($id, array(
					'page'				
				));
			
			
			
			if(!$contens){
				$this->render('/contents/not_found');
				exit(1);
			}
			
			
			
			
			$arrContens = array();
			foreach($arrLangModel as $lang){
				$arrLang[ $lang->id ] = $lang->_data;
				if(is_array($contens->page)){
					foreach($contens->page as $page_id => $page){
						if($page->lang_id == $lang->id ){
							$arrContens[ $lang->code ] = $page->_data;
						}
					}
				}
			}
			
			$this->render('/contents/add',array(
				'id'         => $id,
				'parent'     => $contens->parent_id,
				'code'       => $contens->code,
				'arrLang'    => $arrLang,
				'arrContens' => $arrContens,
			));
		}
		
		/*
		 * Сохранить страницу 
		 **/
		public function actionSave(){
			$this->layout = false;
			$return = array();
			$return['success'] = false;
			if(isAjax()){
				
				
				$arrLang = lang::model()->findAll();				
				$id    = $this->post('id',0);
				
				$arrPages = array();
				
				if($id>0){
					$content  = content::model()->findByPk($id, array(
						'page'				
					));
					$arrPages = $content->page;
				}else{
					$content = new content; 
				}
				
				$content->code       = $this->post('code','');
				$content->parent_id  = $this->post('parent',0);
				
				
				foreach($arrLang as $lang){
									
					$create = true;
					$n = $lang->code;
					$lang_id = $lang->id;
					// edit
					if(is_array($arrPages)){
						foreach($arrPages as $model){
							if($model->lang_id == $lang_id){
								$create = false;
								$model->title       = $this->post($n.'_title','');
								$model->keywords    = $this->post($n.'_keywords','');
								$model->description = $this->post($n.'_description','');
								$model->content     = $this->post($n.'_content','');
							}
						}
					}
					// new 
					if($create){
						$model = new content_page;
						$model->lang_id     = $lang_id;
						$model->title       = $this->post($n.'_title','');
						$model->keywords    = $this->post($n.'_keywords','');
						$model->description = $this->post($n.'_description','');
						$model->content     = $this->post($n.'_content','');
						$arrPages[]         = $model;
					}	
				}
				$content->page = $arrPages;	
					
				if(!$content->save(true, array('page') )){
					foreach($content->getErrors() as $error){
						$return['error'][] = $error;
					}
				}else{
					$return['success'] = true;
				}
				
			}
			/*
			pre($content);
			pre($content->page);*/
			print json_encode($return);
		}
		
		
		
		
		/**
		 * Дерево разделов
		 **/
		public function actionIndex(){
			$db = db::make();
			$parent = $this->post('parent',0);
			
			// Язык по умолчанию. 
			$lang =  lang::model()->find( 't0.default=1' );
			$lang =  array_shift($lang);
			
			
			// Дерево страниц
			$sql = "SELECT c.* , cs.rcount, cs.title, cs.lang_id
					FROM contents c 
					LEFT JOIN content_pages cs ON cs.content_id = c.id 
					WHERE c.parent_id=:parent and cs.lang_id=:lang_id";
			
			$arrData  = $db->prepare($sql)->execute(array(
				':parent'  => $parent,
				':lang_id' => $lang->id
			))->fetchAll();
			
			$treeData = array();
			foreach($arrData as $data){
				$treeData[ $data['parent_id'] ][ $data['id'] ] = $data;
			}
			unset($arrData);
			
			$ajax = isAjax();
			if($ajax) $this->layout = false;
			
			$this->render('/contents/structure_tree',array(
				'ajax'    => $ajax,
				'treeData'=> $treeData,
				'parent'  => $parent,
			));
		}
		
		/** 
		 * Удалить страницу и подразделы
		*/
		public function actionDelete(){
			
			$this->layout = false;
			$id = $this->post('id',0);
			if(is_numeric($id) && $id > 0){
				
				// content_pages
				$db = db::make();
				$sql = "SELECT id, parent_id FROM `contents` WHERE id=:id";
				$arr = $db->prepare($sql)->execute( array(':id'=>$id) )->fetchAll();
				
				pre($arr);
				/*
				$sql = "DELETE FROM `content_pages` WHERE content_id=:id";
				$db->prepare($sql)->execute( array(':id'=>$id) );
				*/
				
				
				
			}
			
		}		
		
		/*
		 * 
		 * Дополнительные типы полей для страниц
		 **/
		public function actionAddonType(){
			$parent = $this->get('parent',0);
			
						
			
			$this->render('/contents/addon_type', array(
				
				'parent'  => $parent,
			));
			
			
		}
		
		
		
		/**
		 * Переместить страницу в другой раздел...
		 **/
		public function actionMove(){
			
			
		}
		
		
		/**
		 * Установсить позицию у страницы
		 **/
		public function actionSetSort(){
			
			
		}
	
	
	
	
		public function after(){	

		}
		
		
		
		
		
		
		
	}
	?>