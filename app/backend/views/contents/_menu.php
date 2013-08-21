
<?
	$action = isset($this->param['action']) ? $this->param['action'] : ''; 
	// Меню
	$arrAction = array(
		'add' => 'Добавить страницу',
		'addonType' => 'Дополнительные типы полей'
	);
?>

<ul class="nav nav-pills">
	<li <?if($action==''||$action=='index'):?> class="active"<?endif;?>>
		<a href="/admin/contents/">Список страниц</a>
	</li>
<?foreach($arrAction as $key => $menu_item):?>
	<li <?if($action==$key):?>class="active"<?endif;?>>
		<a href="/admin/contents/<?=$key?>"><?=$menu_item?></a>
	</li>
<?endforeach;?>
</ul>