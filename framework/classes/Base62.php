<?php 

	if (!defined('doc_root')) exit('No direct script access allowed');


	class Base62 {	
		private static $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		
		public static function encode($id){	
			$hash='';
			while($id > 0){
				$hash=self::$chars[ fmod($id,62) ] . $hash;
				$id=floor($id/62);  
			}
			return $hash;
		}
		public static function decode($code){
			$id=0;  
			$arr = array_flip(str_split(self::$chars));
			for($i=0,$len = strlen($code); $i < $len; ++$i) {
				$id += $arr[$code[$i]] * pow(62, $len-$i-1);
			}
			return (string)$id;
		}
	}
