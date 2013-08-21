<?php
/**
 * Рекурсионая функция для вывода дерева
 **/
function __print_item_recursive(&$arrData, $parent = 0, $level = 0){	
	if(!isset($arrData[$parent])) return;
		++$level;
		foreach($arrData[$parent] as $item):?>
			<li class="row" data-id="<?=$item['id']?>">
				<span class="actShowHide fl plus"></span>
				<span class="fl title">
					<a href="/admin/contents/edit/?id=<?=$item['id']?>"><?=!Empty($item['title']) ? $item['title'] : '- - - -' ?></a>
				</span>
				<table class="fr">
					<tr>
						<td width="80px"></td>
						<td width="80px">0</td>
						<td width="120px">
							<a title="Редактировать страницу" href="/admin/contents/edit/?id=<?=$item['id']?>"><i class="icon-edit"></i></a>
							<a title="Добавить страницу" href="/admin/contents/add/?parent=<?=$item['id']?>"><i class="icon-plus-sign"></i></a>
							
							<a href="#" title="Удалить страницу"><i class="icon-remove-sign"></i></a>
							<a href="#" title="Переместить страницу"><i class="icon-move"></i></a>
							
							
							<a href="#" title="Переместить страницу"><i class="icon-wrench"></i></a>
							 
							
						</td>
						<td width="200px"><span class="code"><?=$item['code']?></span></td>
					</tr>
				</table>
				<ul class="hide" data-id="<?=$item['id']?>"><?__print_item_recursive($arrData, $item['id'], $level)?></ul>
			</li>
		<?endforeach;
		--$level;
}

if(!$ajax):?>

<? include dirname(__FILE__)."/_menu.php";?>

<div class="error"></div>

<ul class="structure_tree"><li class="row">
	<strong class="fl">Заголовок страницы</strong>
	<table class="fr">
		<tr>
			<td width="80px">
				<strong>Шаблон</strong>
			</td>
			<td width="80px">
				<strong>Просмотров</strong>
			</td>
			<td width="120px">
				<strong>Действия</strong>
			</td>
			<td width="200px">
				<strong>Код страницы</strong>
			</td>
		</tr>
	</table>
	
</li><?endif;
	__print_item_recursive($treeData, $parent );
if(!$ajax):?></ul>
<script>
$(function(){
	
	function __loadAjaxTree(parent , ul , _this){
		$.ajax({
			url:'/admin/contents', type:'post', data:{parent:parent}, 
			success:function(m){
				ul.append(m);
				_this.removeClass('plus').addClass('minus');
				ul.removeClass('hide').addClass('show');
				_this.removeClass('ajaxMini');
			},
			error:function(){
				_this.removeClass('ajaxMini');
			}
		});
	}
	
	// Удалить страницы и подстраницы 
	$('.structure_tree').on('click','li.row i.icon-remove-sign',function(){
		var li  = $(this).closest('li.row');
		if(confirm('Удалить страницу и вложенные страницы?')){
			$.post('/admin/contents/delete',{ id: li.attr('data-id') }, function(m){
				li.fadeOut(800,function(){
					$(this).remove();
				})				
			},'json');
		}
	});
	
	
	
	$('.structure_tree').on('click','.actShowHide',function(e){
		var _this = $(this),
			ul = _this.closest('li.row').find('ul:first');
		if(_this.hasClass('plus')){
			if(ul.find('li.row').length==0){
				if(!_this.hasClass('ajaxMini')){
					_this.addClass('ajaxMini');
					__loadAjaxTree( ul.attr('data-id'), ul, _this );
				}
			}else{
				ul.removeClass('hide').addClass('show');
				_this.removeClass('plus').addClass('minus');
			}
		}else{
			_this.removeClass('minus').addClass('plus');
			ul.removeClass('show').addClass('hide');
		}
		
	})	
});
</script>
<?endif;?>