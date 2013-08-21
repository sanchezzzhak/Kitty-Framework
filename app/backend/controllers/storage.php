	<?
	
	class storage_controller extends controller {
		public  $layout = 'main';
		
		public function before(){
			
		}
		
		
		public function actionUploadImage(){
			$this->layout = false;
			$result = array('success'=>false);
			$save_path = root . '/../storage/tmp/';
			$allowedExtensions = array("jpg", "jpeg", "gif", "png", "bmp");
			$sizeLimit = 3 * 1024 * 1024; // 3ьс
			
			$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
			$result = $uploader->handleUpload($save_path);
			if($result['success']==true && is_file($save_path . $result['filename']) ){
				list($width, $height) = getimagesize($save_path . $result['filename']);
				$result['width'] = $width;
				$result['height'] = $height;
			}
			
			echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
			
		}
		
		public function actionUploadFiles(){
			$this->layout = false;
			$result = array('success'=>false);
			$save_path = root . '/../storage/tmp/';
			$allowedExtensions = array(
				"jpg", "jpeg", "gif", "png", "bmp", 
				"zip", "rar", "7z",
				"doc", "pdf", "exl"
			);
			$sizeLimit = 3 * 1024 * 1024; // 3ьс
			$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
			$result = $uploader->handleUpload($save_path);
			echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
			
		}
						
		public function actionIndex(){
			
		}
		
		public function after(){	

		}
		
		
		
		
		
		
		
	}
	?>