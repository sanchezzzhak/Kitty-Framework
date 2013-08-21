<?php

if (!defined('doc_root')) exit('No direct script access allowed');
 
 
class profiler {

    protected $profiles = array() , 
			  $active = false;
	
	/**
	 * Добавление в лог 
	 **/
	public static function add($text, array $data = null, $trace = null ){
        $this->profiles[] =(object) array(
			'text' => $text,
			'time' => time(),
			'data' => $data,
			'trace' => $trace
        );
	}
	
	
}
