<?php


	/* # -== kitty Freamwork ==- # 
	 * Этот файл отвечает за конфигурацию ядра.
	 * 
	 **/

	if(!defined('DS')) 	define('DS', "/" /*DIRECTORY_SEPARATOR*/ );
	define('root' , dirname(__FILE__) );
	define('doc_root' , root . "/.." );

	require root . "/function.php";	
	require root . "/classes/base/Autoload.php";
	
	
	
	// Загрузка карты классов 
	$arrClassesMap = array( 
	// freamwork
	'@base'=> array_change_key_case( array(
		// database PDO
		'db'               => root . '/classes/db/db.php',
		'dbFinder' 		   => root . '/classes/db/dbFinder.php',
		'Model'            => root . '/classes/db/Model.php',
		'dbColumn'         => root . '/classes/db/dbColumn.php',
		'GridModel'        => root . '/classes/db/GridModel.php',
		
		// Mongo		
		'dbMongo'            => root . '/classes/db/dbMongo.php',
		'dbMongoCollection'  => root . '/classes/db/dbMongoCollection.php',
		'dbMongoCursor'      => root . '/classes/db/dbMongoCursor.php',
		'dbMongoDocument'    => root . '/classes/db/dbMongoDocument.php',
		'dbMongoQuery'       => root . '/classes/db/dbMongoQuery.php',
		'dbMongoID'       	 => root . '/classes/db/dbMongoID.php',
		
		// App  
		'App'  			   => root . '/classes/app/App.php',
		'Controller'	   => root . '/classes/app/Controller.php',
		'Module'	   	   => root . '/classes/app/Module.php',
		'Router'  		   => root . '/classes/app/Router.php',
		'Request' 		   => root . '/classes/app/Request.php',
		'WebApplication'   => root . '/classes/app/WebApplication.php',
		
		
		// Base classes
		'Config' 			=> root . '/classes/base/Config.php',
		'ExtendBaseClass'   => root . '/classes/base/ExtendBaseClass.php',	
		'ExceptionError'        => root . '/classes/base/ExceptionError.php',
		'ErrorHandler'      => root . '/classes/base/ErrorHandler.php',
		
		// Другие классы 
		
		'Arr'    		   => root . '/classes/web/Arr.php',
		'ArrData'    	   => root . '/classes/web/ArrData.php',
		'Base62'		   => root . '/classes/Base62.php',
		// Web
		'Session' => root . '/classes/web/Session.php'
		
		
	))
	);
	autoload::loadmap($arrClassesMap);

	// Регестрируем пути в которых искать класс
	autoload::addPath('/framework/models');  // Базовые модели
	autoload::addPath('/framework/vendors'); 
	autoload::register();
	// Старт сессиий, только для авторизованых пользователей 
	
	
	session::instance();


	
	
	