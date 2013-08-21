<?
	$uni = uniqid();
?>


<div class="formRow" data-rowid="<?=$uni?>">
    <label contenteditable="true" data-attr="caption"><?=arr::get($params,'caption','')?></label>
	<button class="btn btn-mini btn-upload">Загрузить файл</button>
	<div class="upload_box"></div>
	<div class="gal"></div>
	<script>
		$(function(){
			
			var _this = $('.formRow[data-rowid="<?=$uni?>"]');
			_this.find('.upload_box').empty();
			uploader( 
				_this.find('.upload_box').get(0), // elements
				'/admin/storage/uploadImage', // path upload
				{},           // params
				false,        // multiple
				function(id, fileName, response){
					var right = _this.find('.right1[process="'+id+'"]');
					if (response.success==true){
						right.html(
						'<div class="img" style="background:url(\'/storage/tmp/322_'+response.filename+
						'\') 50% 0 no-repeat; width:320px;height: 234px; background-position: 0 50%;">'+
						'<img src="/assets/backend/media/px.gif"/>'+'<ul class="img-control">'+
						'<li><span title="Удалить фото" class="ui-icon ui-icon-trash"></span></li>'+
						'<li></li>'+
						'</ul></div>'+	  
						'<input type="hidden" value="'+ response.filename +'">'
						);
					}	
				}, // complite
				function(id, fileName){}, // submit
				function(id, fileName, loaded, total){
					var right = _this.find('.right1[process="'+id+'"]'),
						procent = Math.round(loaded / total * 100);
					if(right.length==0){
						_this.find('.gal').append('<div style="width:320px;" class="right1" process="'+id+'"><div class="progress">'+
						'<div class="bar" style="width:'+procent+'%"></div>'+fileName+'</div></div>');
					}else{
						right.find('.bar').css('width',procent+'%');
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