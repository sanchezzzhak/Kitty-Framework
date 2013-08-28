<?
	namespace kitty;
	/* # -== kitty Freamwork ==- #
	 * 
	 **/
	 
	error_reporting(E_ALL | E_STRICT);
	

	$dirname = dirname(__FILE__);
	// Подключение фреймворка + базовые надстройки
	include_once  $dirname  . "/kitty/init.php";

	// Общий конфиг для backend и frontend
	\kitty\app\config::load( require( $dirname . "/app/config.php" ));

	// Если backend-config
	if(preg_match('#^/admin/?([^/.,;?\n]+)?#i', $_SERVER['REQUEST_URI'] )){
		$config = require( $dirname . "/app/backend/config/main.php" );
	}else{
	// Иначе frontend-config
		$config = require( $dirname . "/app/frontend/config/main.php" );
	}
	// Создаем приложение 

	$app = \kitty\app\app::make('\kitty\app\WebApplication', $config );
	$app->run();    



