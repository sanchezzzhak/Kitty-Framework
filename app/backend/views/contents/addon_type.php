
<script>

$(function(){
	
	$('#saveForm').click( function(e){
		var _this = $(this);
		if(!_this.hasClass('disabled')){
			var bnt_text =  _this.text();
			_this.addClass('disabled').css('disabled','disabled').text(_this.attr('data-loading-text'));
			
			
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

<div class="error"></div>
<div style="margin:10px;">
	<button type="button" id="saveForm" class="btn btn-large btn-primary" data-loading-text="Обработка...">Сохранить</button>
</div>

<div class="form" style="margin:10px;">
	<input type="hidden" name="parent" value="<?=$parent?>">
	
	<div class="b-head">
		<label>Создать дополнительное поле</label>
	</div>
	
	<table class="table">
		<tr>
			<th>Название поля</th>
			<th>Код поля</th>
			<th>Тип поля</th>
			<th>Тип Фильтера</th>
			<th>Сообщение</th>
		</tr>
		<tr>
			<td>
				<input type="text" name="name">
			</td>
			<td>
				<input type="text" name="code">
			</td>
			<td>
				<select name="type">
					<option value="1">Текстовое поле</option>
					<option value="4">Textarea</option>
					<option value="8">Множественный список текстовых полей</option>
					<option value="12">Фото</option>
					<option value="13">Фото-галерея</option>
					<option value="14">Список файлов</option>
					
				</select>
			</td>
			<td></td>
			<td><input type="text" name="code"></td>
		</tr>
		
		
	</table>
	
	<div class="b-head">
		<label>Список дополнительных полей</label>
	</div>
	
	
	
	
	
</div>

