<?
return array(


    'dir_path' => realpath( __DIR__ . "/../" ),
	'locale' => array('en'), // setLocale 
	'lang'   => 'ru',  

	
	// DataBase config;
	
	'db'=>array( 
	
		'default' =>array(
			'driver'=>'mysql',
			'host'=>'localhost',
			'name'=>'cms',
			'user'=>'root',
			'pass'=>'',
		),
		
		'db2' => array(
			'driver'=>'sqlite',
			'name'=>'cms.db',
		),
		
		'mongo' => array(
			'driver'=>'mongo',
			'host' => '',
			'persistent'=>false,
			'name' => 'test',
		),
		
	),

);