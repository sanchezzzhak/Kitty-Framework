<?	
	/**
	 * Экземпляр класса приложения
	 **/
	function app(){
		return app::make();
	}

    /** 
     * Вывод подсказок в админке 
	 */
    function nodeError($text , $icon = 's'){
        $arrStatus  = array(
            'i'=>'nInformation', 'm'=>'nMessage', 'w'=>'nWarning',	's'=>'nSuccess', 'f'=>'nFailure', 'l'=>'nLightbulb'
        );
        print '<div class="nNote '.	( (isset($arrStatus[$icon]))? $arrStatus[$icon] : 'nMessage' ).	' hideclick"><p>'. $text .	'</p></div>';
    }

    /**
     * это Ajax запрос?
	 * @return true / false 
     */
    function isAjax (){
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest";
    }

	/**
     * это Url?
	 * @return true / false 
     */
    function isUrl($url=false) {
        if (!preg_match_all('/^(http|https|ftp):\/\/((?:[a-zA-Z0-9_-\#]+\.?)+):?(\d*)/', $url, $m)) return false;  else return true;
    }

	function redirect($url,$terminate=true,$statusCode=302){
		header('Location: '.$url, true, $statusCode);
		if($terminate) exit(1);
	}
	
	



	/** (***)
     * Подключить шаблон из папки /app/templates/
     * @param $comand string Команда служит путем до файла
     * @param $arrParams array параметры которые передаются шаблону  * 3 и более аргументы функции доступны для личных нужд шаблонов * $args[0] 1 Аргумент($comand), $args[2] 2 Аргумент( $arrParams )  $args[3] ваш агрумент
     * return true / false
	 */
	function initAppTmpl($comand = null , $arrParams = array() ){
        if(is_null($comand)) return false;
        $args = func_get_args();

        $include_file = "/app/templates/". $comand . ".php";
        if(file_exists( root  . $include_file )){
            include root . $include_file;
            return true;
        }
		return false;
	}

	/**
	 * Подключить компонент и выполнить
	 * @param string $name   Пример Имя комопнента 'cblock.filter'
	 * @param array  $params это параметры компонента  
	 */
	function initComponent( $name  ,  $params = array() ){
		$c_class  = str_replace( array('.') , '_' , $name);
		$path = root . '/components/'.$name."/component.php";
		if( is_readable($path) ) include_once $path;
		//Лямбда класс
		$c_class = new $c_class();
		
		$arr = debug_backtrace();
		$file = str_replace($_SERVER['DOCUMENT_ROOT'],'',$arr[0]['file']);
	
		if(method_exists($c_class, 'run')) $c_class->run();
	}
	
	
	/**
	 * Функция перевода строки
	 * @param  $dictionary указатель какой словарь использовать
	 * @param  $message ключ перевода
	 **/
	function t($dictionary , $message , $format = null ){
		
		return $massage;
	}
	

	
    /**
     * Функция для распечатки переменных и прочей отладки
     */
	function pre(){
		$arg = func_get_args();
		foreach($arg as $a){
			echo '<pre style="border:1px solid red;"=>';
			if(is_array($a)) print_r($a); else var_dump($a);
				//echo '<span style="word-wrap: break-word;">'; debug_print_backtrace(); echo '</span>';
			echo '</pre>';
		}
	}
	
    /** 
	 * Эквивалент htmlspecialchars в укроченом виде
	 */
    function ff($s , $style = null , $charset = 'UTF-8', $double_encode = false){
        return htmlspecialchars($s, $style, $charset , $double_encode);
    }
	
	/**
	* Улучшеная функция Формат даты  на 3 языка en kk ru планируется языковые файлы вынести отдельно.
	* u - День недели корот.
	* m - Переводит в языковое название если язык не указан, выводит номер месяца
	* t - Сегодня , Вчера вслкчии -2 дня выводит  день месяц
	* n - день недели с соотвецвие с языком полное.
	*/
	function dateFormat($dateFormat = 'd.m.Y' , $time=null ,  $cLang = 'ru'){
		$time  = ($time > 0  and !is_null($time) ) ? $time: time();
		$month_lang= array(
			'ru'=> array(
				'01'=>'Января' ,'02'=>'Февраля' , 	'03'=>'Марта' , '04'=>'Апреля' ,
				'05'=>'Мая' ,'06'=>'Июня' ,'07'=>'Июля' ,
				'08'=>'Августа' , '09'=>'Сентября' ,	'10'=>'Октября' ,'11'=>'Ноября' ,'12'=>'Декабря'
			),
			'kk' => array(
				'01'=>'Қаңтар','02'=>'Ақпан' , '03'=>'Наурыз' , '04'=>'Сәуір' ,
				'05'=>'Мамыр',
				'06'=>'Маусым','07'=>'Шілде' ,
				'08'=>'Тамыз' , '09'=>'Қыркүйек' , 
				'10'=>'Қазан' ,	'11'=>'Қараша' ,'12'=>'Желтоқсан'
			),	
			'en' => array(
				'01'=>'January' ,'02'=>'February' , '03'=>'March' , '04'=>'April' ,
				'05'=>'May' , '06'=>'June' , '07'=>'July' ,
				'08'=>'August' ,'09'=>'September' ,	'10'=>'October' ,'11'=>'November' ,'12'=>'December'	
			)
		);
		$days_name_lang = array (
			'ru'=> array('1'=>'Понедельник',	'2'=>'Вторник','3'=>'Среда', '4'=>'Четверг', '5'=>'Пятница', '6'=>'Суббота','7'=>'Воскресенье'),
			'kk'=> array('1'=>'Дүйсенбі',	'2'=>'Сейсенбі','3'=>'Сәрсенбі', '4'=>'Бейсенбі', '5'=>'Жұма', '6'=>'Сенбі','7'=>'Жексенбі'),
			'en'=> array('1'=>'Monday',	'2'=>'Tuesday','3'=>'Wednesday', '4'=>'Thursday', '5'=>'Friday', '6'=>'Saturday','7'=>'Sunday')
		);
		$days_lang = array(	
			'ru'=> array('1'=>'ПН',	'2'=>'ВТ','3'=>'СР', '4'=>'ЧТ', '5'=>'ПТ', '6'=>'СБ','7'=>'ВС'),
			'kk'=> array('1'=>'Дү',	'2'=>'Се','3'=>'Сә', '4'=>'Бе', '5'=>'Жұ', '6'=>'Сен','7'=>'Же'),
			'en'=> array('1'=>'Мо',	'2'=>'Tu','3'=>'We', '4'=>'Th', '5'=>'Fr', '6'=>'Sa','7'=>'Su')
		);
		
		$tmpd  = explode('.',date('j.N.m' , $time)); // Вычесляем дни
		$d  = $tmpd[0]; // день без ведущего нуля
		$N  = $tmpd[1]; // номер недели без ведущего нуля
		$m  = $tmpd[2]; // месяц
		
		$t='';	
		$diff = date("z") - date("z", $time);
		switch ($diff){
			case '0': $t = 'Сегодня'; break;
			case '1': $t = 'Вчера'; break;
			default:  $t = '%d ' . $month_lang[$cLang][$m]; break;
		}
		
		// Форматы 
		$format = array( 
			// Дни - no strf eq : S 
			't' => $t ,
			'd' => '%d', 'D' => '%a', 'j' => $d , 'l' => '%A', 'N' => '%u', 'w' => '%w', 'z' => '%j', 
			// Неделя - no date eq : %U, %W 
			'W' => '%V',  'u'=> (($cLang!='' and isset($month_lang[$cLang]))?  $days_lang[$cLang][$N]  : $N ),
			// Месяц - no strf eq : n, t 
			'F' => '%B', 
			'm' => (($cLang!='' and isset($month_lang[$cLang]))? $month_lang[$cLang][$m] :'%m'), 
			'M' => '%b', 
			'n' =>  (($cLang!='' and isset($days_name_lang[$cLang]))? $days_name_lang[$cLang][$N] :'%u') , 
			// Год - no strf eq : L; no date eq : %C, %g 
			'o' => '%G', 'Y' => '%Y', 'y' => '%y', 
			// Время - no strf eq : B, G, u; no date eq : %r, %R, %T, %X 
			'a' => '%P', 'A' => '%p', 'g' => '%l', 'h' => '%I', 'H' => '%H', 'i' => '%M', 's' => '%S', 
			// Временая зона - no strf eq : e, I, P, Z 
			'O' => '%z', 'T' => '%Z', 
			// Полная дата / Время - no strf eq : c, r; no date eq : %c, %D, %F, %x  
			'U' => '%s',
		); 
		$format = strtr((string)$dateFormat, $format);
		return strftime ( $format , $time) ; 
	}
	
	/**
	* Определение IP посетителя
	*/
	function getRealIP(){
		if(!empty($_SERVER['HTTP_X_REAL_IP'])){
			$ip= $_SERVER['HTTP_X_REAL_IP'];	
		}elseif(!empty($_SERVER['HTTP_CLIENT_IP'])){
		  $ip=$_SERVER['HTTP_CLIENT_IP'];
		}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
		  $ip=$_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	/** 
	 * Создать случайный пароль
	**/
	function randomPwd($limit = 10) {
		if($limit >=32) $limit = 32;
		return substr(md5(  substr(md5(uniqid(rand(),1)),0,8).uniqid(rand(),1)),0, $limit);
	}

	/**
	* Преобразование найденного текста ввиде ссылок в ссылки
	* $str - искомая строка
	* $attr - массив атребутов для сылки, пример array('class'=>'link')
	*/
	function autolink($str , $attr = array() ) {
		$s=''; foreach($attr as $key => $a) $s.=' '.$key.'="'. $a .'" '; 
		return preg_replace('#(?:www\.)?(\w+)\.([a-z]+)#', '<a'.$s.'href="http://www.$1.$2">http://www.$1.$2</a>', $str);
	}   
	
	/** 
	* Обрезка текста до конца слова
	*/
	function textlimit($text, $limit=200 , $addtext = '...'){
		$text=mb_substr($text,0,$limit);

		if(mb_substr($text,mb_strlen($text)-1,1) && mb_strlen($text)==$limit){
			$textret=mb_substr($text,0,mb_strlen($text)-mb_strlen(strrchr($text,' ')));
			if(!empty($textret)){
				return $textret . $addtext;
			}
		}
		return $text;
	}
	
	/** 
	 * Количество страниц
	 * $count - количество записей 
	 * $topage - по сколько выводить на страницу
	**/
	function countPage($count , $topage ){
		return ceil($count / $topage);
	}

	/** 
	 * Получить старт выборки из бд.
	 * $page - номер страницы 
	 * $topage - посколько выводить на страницу
	**/
	function startPage($page ,  $topage){
		return ($topage*($page-1));
	}

	/** 
	 * Постраница в виде массива
	 * $page - номер страницы на которой мы находимся
	 * $countPage - Общее количество страниц
	 * $leftright - Количество страниц с каждой стороны
	**/
	function pageArr($page = 1 , $countPage = 50, $leftright=6){
		$arrPage  = array();
		$startpage= ($page-$leftright);
		if($startpage<1)$startpage=1;
			$endpage = $page+$leftright;
		if ($endpage>$countPage) $endpage=$countPage;
		for ($i=$startpage;$i<=$endpage;$i++) :
			$arrPage[$i] = $i;
		endfor;
		return $arrPage;
	}

	/**
	 * Получить редактор CKEditor 
	 * @param  $name  имя
	 * @param  $value значение 	 
	 **/
	function CKEditor($name , $value = '' ){
		include_once  doc_root . "/assets/backend/js/ckeditor/ckeditor.php";
		$CKEditor = new CKEditor('/assets/backend/js/ckeditor/');
		$CKEditor->textareaAttributes['id'] =  $name;
		$CKEditor->editor($name, $value);
	}
	
	function _unset(&$arg1 , &$arg2 = null, &$arg3 = null , &$arg4 = null){
		$arg1 = $arg2 = $arg3 = $arg4 = null;
		gc_collect_cycles();
	}
	
	
?>