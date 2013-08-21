<?

	/* # -== kitty Freamwork ==- #
	 * 
	 **/

	error_reporting(E_ALL | E_STRICT);
	define('DEBUG',true);

	$dirname = dirname(__FILE__);
	// Подключение фреймворка + базовые надстройки
	include_once  $dirname  . "/framework/init.php";

	// Общий конфиг для backend и frontend
	config::load( require( $dirname . "/app/config.php" ));

	// Если backend-config
	if(preg_match('#^/admin/?([^/.,;?\n]+)?#i', $_SERVER['REQUEST_URI'] )){
		$config = require( $dirname . "/app/backend/config/main.php" );
	}else{
	// Иначе frontend-config
		$config = require( $dirname . "/app/frontend/config/main.php" );
	}
	// Создаем приложение 

	$app = app::make('WebApplication', $config );
	$app->run();    

	/*
	$db = db::make('db2');
	pre($db->show_tables() );
	*/

