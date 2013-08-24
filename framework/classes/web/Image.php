<?php
if (!defined('doc_root')) exit('No direct script access allowed');
 
 /*
  * Класс для работы с изображением через GD 
  * 
  **/
 
class image {

	/**
	 * Цвет из hex в rgb
     * @param $color  цвет в Hex
     * @return array
	 **/
	public static function getColorRGB($color = '#000000') {
        $r = sscanf($color, "#%2x%2x%2x");
        $red   = (array_key_exists(0, $r) && is_numeric($r[0]) ? $r[0] : 0);
        $green = (array_key_exists(1, $r) && is_numeric($r[1]) ? $r[1] : 0);
        $blue  = (array_key_exists(2, $r) && is_numeric($r[2]) ? $r[2] : 0);
        return array($red, $green, $blue);
    }







}
