<div class="formRow">
	<label contenteditable="true" data-attr="caption"><?=arr::get($params,'caption','')?></label>
	<div class="list">
<?
$items = arr::get($params,'items',array() );
$name  = arr::get($params,'name','');
foreach($items as $item):?>
	<?if(!isset($item['label'])):?>
	<div><label><input type="checkbox" name="<?=$name?>" value="<?=$item['value']?>"><?=$item['text']?></label></div>
	<?else:?>
		<div><?=$item['label']?></div>
		<?foreach($item['group'] as $item):?>
			<div><label><input type="checkbox" name="<?=$name?>" value="<?=$item['value']?>"><?=$item['text']?></label></div>
		<?endforeach;?>
	<?endif;?>
<?endforeach;?>
	</div>
    <div class="clear"></div>
</div>