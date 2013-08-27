<?php
namespace kitty\web; 
 
class profiler {

    protected $profiles = array() , 
			  $active = false;
	
	/**
	 * Добавление в лог 
	 **/
	public function log($text, array $data = null, $trace = null ){
        $this->profiles[] =(object) array(
			'text' => $text,
			'time' => time(),
			'data' => $data,
			'trace' => $trace
        );
	}
	
	
}
