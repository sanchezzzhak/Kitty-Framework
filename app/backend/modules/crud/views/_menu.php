
<?
    $operation = $this->getOperation(true);
	$action_menu = $operation['controller'];

	// Меню
	$arrAction = array(
		'model' => 'Модель',
        'module' => 'Модуль',
		'controller' => 'Контроллер',
		'grid' => 'Грид',
		'mongo' => 'Mongo документ',
        'form' => 'Формы',
	);
?>

 <ul class="nav nav-pills">
    <?foreach($arrAction as $key => $menu_item):?>
        <li <?if($action_menu==$key):?>class="active"<?endif;?>>
            <a href="/admin/crud/<?=$key?>"><?=$menu_item?></a>
        </li>
    <?endforeach;?>

    <li class="dropdown <?if($action_menu=='ui'):?>active<?endif;?>">
		<a class="fl" href="/admin/crud/ui"> UI форма</a>
		<a href="#" class="dropdown-toggle fl" data-toggle="dropdown" style="margin-left: -10px; padding: 10px;"><b class="caret"></b></a>
		<ul class="dropdown-menu">
			<li <?if($operation['action']=='uiforms'):?>class="active"<?endif;?>>
				<a href="/admin/crud/uiforms"> Список форм</a>	
			</li>
		</ul>
	</li>
	
</ul>