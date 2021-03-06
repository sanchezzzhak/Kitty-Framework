	<?
	
	class crud_controller extends controller {
		public  $layout = 'main';
		
		
		
		/** 
		 * Для генерации UI-форм
		 * 
		 **/
		// Список компонентов в системе....
		public $components = array(
		
		'input' => array(
			'position'=>'absolute',
			'top' =>0, 
			'left'=>0,
			'float'  => 'none',
			'parent' =>  0,
			'height'  =>  '70px',
			'width' => '180px',
			'value' => '',
			'name'  => '',
			'caption' => 'Text...',
		),
		'textarea'     => array(
			'position'=>'absolute',
			'float'  => 'none',
			'caption' => 'Text...',
			'parent' =>  0,
			'text_height' => '80px',
			'text_max_height'=>'0',
			'height'  =>  '90px',
			'width' => '180px',
			'value' => '',
		),
		
		'textarea-list'     => array(
			'position'=>'absolute',
			'float'  => 'none',
			'caption' => 'Text...',
			'parent' =>  0,
			'text_height' => '80px',
			'text_max_height'=>'0',
			'height'  =>  '90px',
			'width' => '180px',
			'value' => '',
			'scroll'=> true,
		),
		
		'ckeditor'     => array(
			'position'=>'absolute',
			'top' =>0, 
			'left'=>0,
			'float'  => 'none',
			'parent' =>  0,
			'height'  =>  '410px',
			'width' => '600px',
			'value' => '',
			'name'  => '',
			'caption' => 'Text...',
		),
		'select'       => array(
			'position'=>'absolute',
			'top' =>0, 
			'left'=>0,
			'float'  => 'none',
			'parent' =>  0,
			'height'  =>  '210px',
			'width' => '300px',
			'value' => '',
			'items' => array(),
			'name'  => '',
			'caption' => 'Text...',	
		),
		'checkbox' => array(
			'position'=>'absolute',
			'top' =>0, 
			'left'=>0,
			'float'  => 'none',
			'parent' =>  0,
			'height'  =>  '40px',
			'width' => '180px',
			'value' => '',
			'name'  => '',
			'caption' => 'Text...',
		),
		'checkbox-items' => array(
			'position'=>'absolute',
			'top' =>0, 
			'left'=>0,
			'float'  => 'none',
			'parent' =>  0,
			'height'  =>  '180px',
			'width' => '180px',
			'value' => '',
			'name'  => '',
			'items' => array(),
			'caption' => 'Text...',
		),
		'upload-file'  => array(
			'position' =>'absolute',
			'top' =>0, 
			'left'=>0,
			'float'  => 'none',
			'caption'=> 'Text...',
			'parent'=> 0,
			'height'=>'310px',
			'width'=>'350px',	
		),
		'upload-image' => array(
			'position' =>'absolute',
			'top' =>0, 
			'left'=>0,
			'float'  => 'none',
			'caption'=> 'Text...',
			'parent'=> 0,
			'height'=>'310px',
			'width'=>'350px',	
			'image-size'=> '322',
		),
		
		'date-time' => array(
			'position'=>'absolute',
			'top' =>0, 
			'left'=>0,
			'float'  => 'none',
			'parent' =>  0,
			'height'  =>  '100px',
			'width' => '250px',
			'value' => '',
		),
		
		'text-box'     => array(
			'position' =>'absolute',
			'top' =>0, 
			'left'=>0,
			'float'  => 'none',
			'caption'=> 'Text...',
			'parent'=> 0,
			'height'=>'70px',
			'width'=>'180px',
		),
		
		'head-panel'=> array(
			'position'=>'absolute',
			'top' 	 => 0, 
			'left'	 => 0,
			'float'  => 'none',
			'name'   => '',
			'width'  => '350px',
			'height' => '250px',
			'caption'=> 'Text...',
			'parent' =>  0,
			'scroll' => true,
		),
		'panel'        => array(
			'position'=>'absolute',
			'top' =>0, 
			'left'=>0,
			'float'  => 'none',
			'name'   => '',
			'width'  => '350px',
			'height' => '250px',
			'parent' =>  0,
			'scroll' => true,
		),
		
		);
		
		/**
		 * Форма создания грида
		**/
		public function actionGrid(){
			$this->pageTitle = 'Grid';
			$this->render('/crud/grid', array(
			
			));
		}
	
		/**
		 * Создать контроллер
		**/
		public function actionController(){
			$this->pageTitle = 'Controller';
			$this->render('/crud/controller', array(
			
			));
		}
	
	
	
		/**
		 * Список форм
		**/
		public function actionUiForms(){
			$this->pageTitle = 'Список UI Форм';
			$this->render('/crud/ui-list-forms', array(
		
			));
		}
		 
		public function actionUiSave(){
			$this->layout = false;
			$db = db::make();
			$result = array('success'=>false);
			
			$components = $this->post('components',array()); # список компонентов
			$id_group = $this->post('id_group',0);
			
			
			uksort($components, function($comp) use($components){
				if($components[$comp]['parent']==0) return -1; 
				elseif($components[$comp]['parent'] >  0 ) return 1;
			});
			
			$result['replaceId'] =  array();
			foreach($components as $comp){
				$parent = isset($result['replaceId'][$comp['parent']]) && $comp['parent'] > 0
				? $result['replaceId'][$comp['parent']]['id'] : 0; 
				if($comp['isnew']==true){
				
					$db->insert('block_type_field', array(
						'id_group' => $id_group,
						'type'     => $comp['type'],
						'parent'   => $parent,
						'options'  => $comp['options'],
					));
					$result['replaceId'][ $comp['id'] ] = array(
						'id'=> $db->lastInsertId(),
						'parent'=> $parent
					);
						
				}else{
					
					$db->update('block_type_field', array(
						'id_group' => $id_group,
						'type'     => $comp['type'],
						'parent'   => $parent ,
						'options'  => $comp['options'],
					),"id=:id", array(':id'=> $comp['id'] ));
					
					$result['updateId'][ $comp['id'] ] = array(
						'id'=> $comp['id'],
						'parent'=> $parent
					);
				}
				
					
			}
			
			echo json_encode($result);
			exit(1);
		}
		
		
		 
		/**
		 * Главная форма создания формы.
		**/
		public function actionUI(){
			
			$this->pageTitle = 'UI Форма';

			$this->render('/crud/ui-constructor', array(
				'components' => $this->components
			));
		}
		
		/**
		 * Свойство компонента
		**/
		public function actionProperty(){
			$this->layout = false;
			if(isAjax()){
				$params = $this->post('jsondata','');
				$type = $this->post('type','');
				if(!empty($params) && !empty($type)){
					$params = json_decode($params, true);
					$params = arr::merge($this->components[$type], $params );
					ksort($params);
				}
				$this->render('crud/ui-property',  array( 'properts' => $params) );
			}
		}
		
		
		/**
		 * Отдать вид компонента
		 */
		public function actionInsertComponent(){
			$this->layout = false;
			if(isAjax()){
				$component = $this->post('component',false);				
				$params    = $this->components[$component];
				asort($params);
				print view::make('crud/ui-components/' . $component , array(  
					'params'=>  $params,
				));
			}			
		}
		
		
		
		
		
		public function before(){
			
		}
		
		/**
		 * Получение полей у таблицы
		 **/
		public function actionGetTableInfo(){
			$result = array();
			$this->layout = false;
			$db_name = $this->post('db');
			$table_name = $this->post('table');
			if(isAjax() && !empty($db_name) && !empty($table_name)){
				$db = db::make($db_name);
				$result = $db->show_columns($table_name);
			}
			print json_encode($result);
		}
		
		
		
		/*
		 * Список таблиц указаной DB 
		 **/
		public function actionGetDbTableList(){
			$result = array();
			$this->layout = false;
			$db_name = $this->post('db',null);
			if(isAjax() && !empty($db_name)){
				$db = db::make($db_name);
				$result = array();
				$result['tables'] = $db->show_tables();
				
			}
			print json_encode($result);
		}
		
		
		
		/*
		 * Форма генерации моделей
		 **/
		public function actionModel(){
			$arrDb = array_keys(config::get('db')); 
			$this->pageTitle = 'Конструктор модели';
			
			$this->render('/crud/model',array(
				'arrDb'=>$arrDb,
			));
		}
		

		
		public function actionIndex(){
			$this->render('/crud/index');
		}
		
		public function after(){	

		}
		
		
		public function actionMongo(){
			$this->render('/crud/index');
		}
		
		
		
		
	}
	?>