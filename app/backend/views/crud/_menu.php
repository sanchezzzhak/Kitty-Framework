
<?
	$action = isset($this->param['action']) ? $this->param['action'] : ''; 
	// Меню
	$arrAction = array(
		'model' => 'Модель',
		'controller' => 'Контроллер',
		'grid' => 'Грид',
		'mongo' => 'Mongo',
	);
?> 
<ul class="nav nav-pills">
<?foreach($arrAction as $key => $menu_item):?>
	<li <?if($action==$key):?>class="active"<?endif;?>>
		<a href="/admin/crud/<?=$key?>"><?=$menu_item?></a>
	</li>
<?endforeach;?>
	<li class="dropdown <?if($action=='ui' || $action=='uiforms'):?>active<?endif;?>">
		<a class="fl" href="/admin/crud/ui"> UI форма</a>
		<a href="#" class="dropdown-toggle fl" data-toggle="dropdown" style="margin-left: -10px; padding: 10px;"><b class="caret"></b></a>
		<ul class="dropdown-menu">
			<li <?if($action=='uiforms'):?>class="active"<?endif;?>>
				<a href="/admin/crud/uiforms"> Список форм</a>	
			</li>
		</ul>
	</li>
	
</ul>

<div class="alert alert-block">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <h4>Модуль в разработке!</h4>
</div>