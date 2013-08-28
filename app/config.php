<?php
/*
 Ïğèìåğû êîíôèãîâ ïîä ğàçíûå ÁÄ
*/





return array(

	'locale' => array('en'), // setLocale 
	'lang'   => 'ru',                  
	// DataBase
	
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