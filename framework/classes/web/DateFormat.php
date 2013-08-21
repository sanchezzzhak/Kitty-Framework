<?php
if (!defined('doc_root')) exit('No direct script access allowed');




class dateFormat extends DateTime
{
	public $_format;

	/**
	 * @param  $format    ������ �������  
	 * @param  $time      ����� 
	 * @param  $timezone  DateTimeZone �������� ����
	 **/
	public function __construct($format = 'd.m.Y H:i' , $time = 'NOW'  ,  DateTimeZone $timezone = NULL ){
		$this->_format = $format;
		if(is_numeric($time)){
			$time = '@'.$time;
		}	
		parent::__construct($time);
	}
	
	
	/*
	 *  
	 **/
	public function formatDiff( $format = null ){
		
		
		
	}
	
	/*
	 * ����� ����
	 * @param  $format  ������ 	 
	 **/
	public function format($format = null ){
		$format = is_null($format) ?  $this->_format : $format;
		$date = parent::format($format);


		
		return $date;
	}
	
	public function __toString(){
        return $this->format($this->_format);
    }
		
	/*
	 * ������ daysInterval( strtotime(date('d.m.Y')) , strtotime('+14 Day'))
	 */	
	public function daysInterval($low_date, $high_date){
		$days = array();    
        $day_step = 86400;
        for ($d=$low_date; $d<=$high_date; $d=$d+$day_step){
            $unixtime = mktime(0,0,0, date('m', $d), date('d', $d), date('Y', $d));
			$days[$unixtime] = date('d', $d);
        }
        return $days;
    }
   
}
