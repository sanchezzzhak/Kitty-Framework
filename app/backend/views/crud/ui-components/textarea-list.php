<? 

$uni = uniqid(); 

?>
<script>
$(function(){
	
	var formRow = $('.formRow[data-rowid="<?=$uni?>"]');
	formRow.find('.addTextArea').on('click',function(e){
		var _html = '<div class="item">'+
		'<a href="javascript:;"><i class="icon-remove"></i> удалить </a>'+
		'<textarea name="<?=arr::get($params,'name','')?>"></textarea>'+
		'<div class="clear"></div>'+
		'</div>';
		formRow.find('.box').append(_html);
	});
	//formRow.find('.scroll_box').mCustomScrollbar({set_height:280,advanced:{updateOnContentResize:true} })
});
</script>
<div class="formRow" data-rowid="<?=$uni?>">
	<div class="b-head">
		<label contenteditable="true" data-attr="caption"><?=arr::get($params,'caption','')?></label>
	</div>
	<button type="button" style="margin-bottom:5px" class="btn btn-mini btn-primary addTextArea">Добавить</button>
	<div class="scroll_box">
		<div class="box">
			
		</div>
	</div>	
</div>	
	


