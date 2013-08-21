<?
	$uni = uniqid();
?>


<div class="formRow" data-rowid="<?=$uni?>">
    <label contenteditable="true" data-attr="caption"><?=arr::get($params,'caption','')?></label>
	<button class="btn btn-mini btn-upload">Загрузить файлы</button>
	<div class="upload_box" style="display:none;"></div>
	<ul class="upload_list"></ul>
	<script>
		$(function(){
			
			var _this = $('.formRow[data-rowid="<?=$uni?>"]');
			_this.find('.upload_box').empty();
			uploader(
				_this.find('.upload_box').get(0), // elements
				'/admin/storage/uploadFiles', // path upload
				{},           // params
				true,        // multiple
				function(id, fileName, response){
					var right = _this.find('.right1[process="'+id+'"]');
					
					if (response.success==true){
						right.html(
							response.filename+
							'<input type="hidden" value="'+ response.filename +'">'+
							'<a href="javascript:;" title="Удалить файл?" class="fr"><i class="delete_file icon-remove"></i></a>'	  
						);
					}	
				}, // complite
				function(id, fileName){}, // submit
				function(id, fileName, loaded, total){
					var right = _this.find('.right1[process="'+id+'"]'),
						procent = Math.round(loaded / total * 100);
					if(right.length==0){
						_this.find('ul').append('<li class="right1" process="'+id+'"><div class="progress">'+
						'<div class="bar" style="width:'+procent+'%">'+fileName+' '+procent+'%</div></div></li>');
					}else{
						right.find('.bar').css('width',procent+'%').text(fileName+' '+procent+'%');
					}
				}, // progress
				function(id, fileName){}  // cancell
			);
			
			_this.on('click', '.btn-upload', function(ev){
				_this.find('input[type="file"]').trigger('click');
			});
			
			
		});
	</script>
    <div class="clear"></div>
</div>