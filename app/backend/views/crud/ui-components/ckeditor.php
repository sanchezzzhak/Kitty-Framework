<div class="formRow">
    <label contenteditable="true" data-attr="caption"><?=arr::get($params,'caption','')?></label>
    
	<?
		CKEditor(arr::get($params,'name', 'ck'.uniqid() ) , arr::get($params,'vslue',''));
	?>
	
	
	<div class="clear"></div>
</div>