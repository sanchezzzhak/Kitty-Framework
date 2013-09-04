<?
use \kitty\app\router;

// Запуск модуля контроллера и нужного нам действия 
router::set('module+controller+action', '/admin/:module/:contoller/:action', array() )
->setController(':contoller', ':action')->setModule(':module'); 

// Запуск модуля выбраного контролера и метода
router::set('module+controller', '/admin/:module/:controller', array() )
->setController(':controller', null)->setModule(':module'); 

// Запуск модуля и контролера по умолчанию 
router::set('module', '/admin/:module', array() )
->setController('default_main', null)->setModule(':module'); 

// Запуск контролера и действия
router::set('controller+action' , '/admin/:controller/:action', array() )
->setController(':controller', ':action' );

// Запуск контролера и действия по умолчанию
router::set('controller' , '/admin/:controller', array() )
->setController(':controller', null);	

// Запуск контролера по умолчанию
router::set('default' , '^/admin/?$', array())->setController('default_main');

return array(
	// путь к приложению по умолчанию 
	'page404'  => '/admin',
	'name'     => 'Панель управления сайтом',
	'basePath' => dirname(__FILE__)."/..",

	// список модулей
	'modules'   => array(
        'content',
		'crud',
	),
	
);