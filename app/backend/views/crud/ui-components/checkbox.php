<?
	$uni = uniqid();

?>
<div class="formRow">
	<input class="fl" id="l<?=$uni?>" type="checkbox"
		name="<?=arr::get($params,'name','')?>"
		value="<?=arr::get($params,'value','1')?>"> 
	<label class="fl mrl5" for="l<?=$uni?>" contenteditable="true" data-attr="caption"><?=arr::get($params,'caption','')?></label>
<div class="clear"></div>
</div>