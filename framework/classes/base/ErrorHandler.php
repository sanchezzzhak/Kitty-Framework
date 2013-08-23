<?


/**
* Class CErrorHandle
* Обрабочик ошибок
*/
class ErrorHandler {
	
	private $httpCodes = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		118 => 'Connection timed out',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		210 => 'Content Different',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		310 => 'Too many Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Time-out',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested range unsatisfiable',
		417 => 'Expectation failed',
		418 => 'I’m a teapot',
		422 => 'Unprocessable entity',
		423 => 'Locked',
		424 => 'Method failure',
		425 => 'Unordered Collection',
		426 => 'Upgrade Required',
		449 => 'Retry With',
		450 => 'Blocked by Windows Parental Controls',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway ou Proxy Error',
		503 => 'Service Unavailable',
		504 => 'Gateway Time-out',
		505 => 'HTTP Version not supported',
		507 => 'Insufficient storage',
		509 => 'Bandwidth Limit Exceeded',
	);

	
	/**
	 * Обработка перехваченных исключений
	 * @param Exception $e перехваченное исключение
	**/
	public function handleException($e){
		restore_error_handler();
		restore_exception_handler();
		try
		{
			$this->displayException($e);

		} catch (Exception $e){
			ob_get_level() && ob_clean();
			$this->displayException($e);
			app::end(1);
		}
		return true;
	}
	
	
	protected function getTypeError($code){
		$type = 'PHP Unknown error code ' .$code;
		switch($code){
			case E_STRICT:            $type = 'Strict error';		break;
			case E_WARNING:           $type = 'PHP warning';		break;
			case E_NOTICE:	          $type = 'PHP notice';			break;
			case E_USER_ERROR:	      $type = 'User error';			break;
			case E_USER_WARNING:      $type = 'User warning';		break;
			case E_USER_NOTICE:       $type = 'User notice';		break;
			case E_RECOVERABLE_ERROR: $type = 'Recoverable error';	break;
			default:   $type = 'PHP error';
		}
		return $type;
	}
	
	
	
	public function handleError($code,$message,$file,$line){
		if($code & error_reporting()){
			restore_error_handler();
			restore_exception_handler();
			
			$type = $this->getTypeError($code);

			$log="$message ($file:$line)\nStack trace1:\n";
			$trace=debug_backtrace();
			try
			{
				foreach($trace as $i=>$t){
					if(!isset($t['file'])) $t['file']='unknown';
					if(!isset($t['line'])) $t['line']=0;
					if(!isset($t['function'])) $t['function']='unknown';
					
					$log.="#$i {$t['file']}({$t['line']}): ";
					if(isset($t['object']) && is_object($t['object']))
						$log.=get_class($t['object']).'->'."{$t['function']}()\n";
				}
				//if(isset($_SERVER['REQUEST_URI']))

				$data = array(
					'type'    => $type,
					'trace'	  => $trace,
					'code'    => $code,
					'message' => $message,
					'file'    => $file,
					'line'    => $line,
				);	
				if(!headers_sent())
					header("HTTP/1.0 500 Internal Server Error");
				
				// Запись в лог
				//$log;
				//
			}catch(Exception $e){
				$this->displayException($e);
			}	
			
			try	{
				if(DEBUG){
					echo $this->render('error_handle',array('data' => $data ));	
				}
				app::end(1);
			}
			catch(ExceptionError $e){
				$this->displayException($e);
			}
			
			
		}
		return true;	
	}

	/**
	 * Перехват фатальных ошибок
	 * 
	 **/
	public function shutdownHandler(){
		$error = error_get_last();
		if(!Empty($error)
		  && $error['type'] == E_ERROR
		  || $error['type'] == E_PARSE
		  || $error['type'] == E_COMPILE_ERROR
		  || $error['type'] == E_CORE_ERROR){
			
			echo ob_get_clean();
				
		}else ob_end_flush();
		
		
		
	}
	
	/**
	 * Вывод исключений
	**/
    public function displayException(&$exception){
		if(DEBUG){
			echo $this->render( "error_handle", array( 'data' => 
				array(
					'type'    => get_class($exception),
					'trace'	  => $exception->getTrace(),
					'code'    => $exception->getCode(),
					'message' => $exception->getMessage(),
					'file'    => $exception->getFile(),
					'line'    => $exception->getLine(),
				)
			));
			exit(1);
		}
	}
	
	/**
	 * Вывод ошибки
	 **/
	public function render( $view = 'error_handle' , $param = array() ){
		ob_start() && extract($param, EXTR_SKIP);
		try{
			include dirname(__FILE__) . "/../../views/" . trim($view,'/') . ".php";			
		}catch (ExceptionError $e){
			ob_end_clean();
			throw $e;
		}	
		return ob_get_clean();
	}
	
	/* Yii render source code 
	* Добавлена частичная подсветка кода
	*/
	public function renderSourceCode($file , $errorLine, $maxLines = 25 , $highlight = true){
		$errorLine--;
		if($errorLine<0 || ($lines=@file($file))===false || ($lineCount=count($lines))<=$errorLine)
		return '';
		
		$halfLines =(int)($maxLines/2);
		$beginLine = $errorLine-$halfLines>0 ? $errorLine-$halfLines:0;
		$endLine = $errorLine+$halfLines<$lineCount?$errorLine+$halfLines:$lineCount-1;
		$lineNumberWidth = strlen($endLine+1);
		
		$find = array(
			'#(\sclass|abstract|public|private|protected|final|function|return|true|false|extends|include_once|require|'.'echo|array|print|foreach|endforeach|if|else|endif|switch|endswitch|'.'case|new|instanceof|throw|isset|empty)#i',		
			
			'@&#039;(.*)&#039;|&quot;(.*)&quot;@isU',
			'@(\/\/)(.*)\n@isU',
		);
		$replace = array(
			'<span class="blue">\\0</span>', 
			'<span class="quote">\\0</span>',
			'<span class="comm">\\0</span>',
		);
		
		
		
		$output='';
		for($i=$beginLine;$i<=$endLine;++$i){
			$isErrorLine = $i===$errorLine;
			$code = htmlspecialchars(str_replace("\t",'    ',$lines[$i]),ENT_QUOTES);
	
			if($highlight)
				$code = '<span class="color">' . preg_replace($find , $replace, $code) . '</span>';
			
			$code=sprintf( "<span class=\"ln".($isErrorLine?' error-ln':'')."\">%0{$lineNumberWidth}d</span><span class=\"color\">%s</span>",$i+1,$code);
			$output.= !$isErrorLine ? $code : '<span class="error">'.$code.'</span>';
		}
		return '<div class="code"><pre>'.$output.'</pre></div>';
	}
	
}
?>