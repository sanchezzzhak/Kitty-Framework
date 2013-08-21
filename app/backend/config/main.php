<?

// ������ ������ ����������� � ������� ��� �������� 
router::set('module+controller+action', '/admin/:module/:controller/:action', array() )
->setController(':contoller', ':action')->setModule(':module'); 

// ������ ������ ��������� ���������� � ������
router::set('module+controller', '/admin/:module/:controller', array() )
->setController(':controller', null)->setModule(':module'); 

// ������ ������ � ���������� �� ��������� 
router::set('module', '/admin/:module', array() )
->setController('default_main', null)->setModule(':module'); 

// ������ ���������� � ��������
router::set('controller+action' , '/admin/:controller/:action', array() )
->setController(':controller', ':action' );

// ������ ���������� � �������� �� ���������
router::set('controller' , '/admin/:controller', array() )
->setController(':controller', null);	

// ������ ���������� �� ���������
router::set('default' , '^/admin/?$', array())->setController('default_main');

return array(
	// ���� � ���������� �� ��������� 
	'page404'  => '/admin',
	'name'     => '������ ���������� ������',
	'basePath' => dirname(__FILE__)."/..",

	// ������ �������
	'modules'   => array(
		'crud'
	),
	
);