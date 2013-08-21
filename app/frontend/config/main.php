<?php
// site.dev/contoller/action
router::set('contoller+action' , '^/:contoller/:action', array() )->setController(':contoller', ':action' ); 
// site.dev/contoller
router::set('contoller' , '^/:contoller', array() )->setController(':contoller', null ); 
// site.dev
router::set('default' , '^/$', array())->setController('default_main');


return array(
	
	
	'name'     => 'Имя сайта...',

	// путь к приложению по умолчанию 
	'basePath' => dirname(__FILE__)."/..",

	'controller_path' => '/controllers',
	'moduls_path'     => '/moduls',
	'page404'  => '/',
	// список модулей
	'moduls'   => array(
	),

);