
<script>
$(function(){
	
	$('#saveForm').click( function(e){
		var _this = $(this);
		if(!_this.hasClass('disabled')){
			var bnt_text =  _this.text();
			_this.addClass('disabled').css('disabled','disabled').text(_this.attr('data-loading-text'));
			
			var jsonData = jsonForm('.form');
			// CKEDITOR получаем данные через API
			<?foreach($arrLang as $lang):?>
				jsonData['<?=$lang['code']."_content";?>'] = CKEDITOR.instances.<?=$lang['code']."_content";?>.getData();
			<?endforeach;?>
			
			$.ajax({
				url:'/admin/contents/save', type:'post', data: jsonData , dataType:'json',
				success:function(m){
					if(m.success==true){
						$('.error').html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button><h4>Success!</h4>Страница сохранена</div>');
					}else{
						$('.error').html('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><h4>Error!</h4><ul></ul></div>');
						$.each(m.error,function(key,item){
							$('.error ul').append('<li>'+item+'<br></li>');	
						});
					}
					_this.removeClass('disabled').css('disabled','').text(bnt_text);
				},
				error:function(m){
					_this.removeClass('disabled').css('disabled','').text(bnt_text);
				}
			});		
		}
	});
	
	$('.tabslist li').click(function(m){
		var _this = $(this);
		$('.tabslist li').removeClass('active');
		_this.addClass('active');
		$('div.tab[tab]').removeClass('show').addClass('hide');
		$('div.tab[tab="'+_this.attr('tab')+'"]').removeClass('hide').addClass('show');
	});

	
	
});
	//$('.form')	
</script>

<? include dirname(__FILE__)."/_menu.php";?>


<div style="margin:10px;" class="error"></div>
<div style="margin:10px;">
	<button type="button" id="saveForm" class="btn btn-large btn-primary" data-loading-text="Обработка...">Сохранить</button>
</div>

<div class="form" style="margin:10px;">
	
	<input type="hidden" name="id" value="<?=$id?>">
	<input type="hidden" name="parent" value="<?=$parent?>">
	
	<div class="fl" style="width:290px">
		<div class="b-head">
			<label for="code"><strong>Код страницы</strong></label>
		</div>
		<input  style="width:275px" type="text" id="code" name="code" value="<?=htmlspecialchars($code);?>">
	</div>
	
	<div class="fl mrl5" style="width:200px">
		<div class="b-head">
			<label><strong>Опции</strong></label>
		</div>
		<label class="label label-info"><input type="checkbox"> Вывести подразделы </label>	
	</div>
	
	<div class="fl mrl5" style="width:230px">
		<div class="b-head">
			<label><strong>Слой</strong></label>
		</div>
		<input type="text" name="layout" value="page">
	</div>
	
	
	<div class="clear"></div>

	<!-- Вкладки языков { -->	
	<ul class="tabslist nav-tabs fix">
		<?foreach($arrLang as $lang):?>
			<li tab="<?=$lang['id']?>" <?=$lang['default']==1?'class="active"':''?>><a href="javascript:;"><?=$lang['name']?></a></li>
		<?endforeach;?>
	</ul>	
	<!-- } Вкладки языков -->	
		
	<?foreach($arrLang as $lang):
		$page = arr::get($arrContens, $lang['code'] , array() );
	?>
	<div tab="<?=$lang['id']?>" class="tab <?=$lang['default']==1?'show':'hide'?>">		
		<div class="b-head">
			<label for="<?=$lang['code']?>_title"><strong>Заголовок страницы</strong></label>
		</div>
		<input  style="width:98%" type="text" id="<?=$lang['code']?>_title" name="<?=$lang['code']?>_title" value="<?=htmlspecialchars(arr::get($page,'title',''))?>">

		<div class="b-head">
			<label for="<?=$lang['code']?>_keywords"><strong>Ключивые слова</strong></label>
		</div>
		<input  style="width:98%" type="text" id="<?=$lang['code']?>_keywords" name="<?=$lang['code']?>_keywords" value="<?=htmlspecialchars(arr::get($page,'keywords',''))  ?>">
		
		<div class="b-head">
			<label for="<?=$lang['code']?>_description"><strong>Описание страницы</strong></label>
		</div>
		<input  style="width:98%" type="text" id="<?=$lang['code']?>_description" name="<?=$lang['code']?>_description" value="<?=htmlspecialchars(arr::get($page,'description',''))  ?>">
		
		<div class="b-head">
			<label><strong>Текст страницы</strong></label>
		</div>
		<? CKEditor($lang['code'].'_content', arr::get($page,'content','') ); ?>	
	</div>
	<?endforeach;?>
	
	
</div>
<div style="height:50px;">
	
	
</div>
