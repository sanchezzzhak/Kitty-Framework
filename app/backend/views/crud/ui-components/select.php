<div class="formRow">
    <label contenteditable="true" data-attr="caption"><?=arr::get($params,'caption','')?></label>
	<select style="width:100%" name="<?=arr::get($params,'name','')?>">
		<?if(!isset($item['label'])):?>
			<option value="<?=$item['value']?>"><?=$item['text']?></option>
		<?else:?>
		<optgroup label="<?=$item['label']?>">
		<?foreach($item['group'] as $item):?>
			<option value="<?=$item['value']?>"><?=$item['text']?></option>
			<?endforeach;?>
		</optgroup>
		<?endif;?>
	</select>
	<div class="clear"></div>
</div>