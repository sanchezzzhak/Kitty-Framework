<?

// «апуск модул€ контроллера и нужного нам действи€ 
router::set('module+controller+action', '/admin/:module/:controller/:action', array() )
->setController(':contoller', ':action')->setModule(':module'); 

// «апуск модул€ выбраного контролера и метода
router::set('module+controller', '/admin/:module/:controller', array() )
->setController(':controller', null)->setModule(':module'); 

// «апуск модул€ и контролера по умолчанию 
router::set('module', '/admin/:module', array() )
->setController('default_main', null)->setModule(':module'); 

// «апуск контролера и действи€
router::set('controller+action' , '/admin/:controller/:action', array() )
->setController(':controller', ':action' );

// «апуск контролера и действи€ по умолчанию
router::set('controller' , '/admin/:controller', array() )
->setController(':controller', null);	

// «апуск контролера по умолчанию
router::set('default' , '^/admin/?$', array())->setController('default_main');

return array(
	// путь к приложению по умолчанию 
	'page404'  => '/admin',
	'name'     => 'ѕанель управлени€ сайтом',
	'basePath' => dirname(__FILE__)."/..",

	// список модулей
	'modules'   => array(
		'crud'
	),
	
);