<?


/* Список свойств скрытые */

$arrHide  = array('parent');

/* Список свойств для select списка */
$arrSelect = array(

	'float' => array(
		'none'  => 'none',
		'left'  => 'left',
		'right' => 'right',
	),
	'position'  => array(
		'absolute'=>'absolute',
		'relative'=>'relative'
	),
	// Список размеров для upload-image
	'image-size' => array(
		'60' => '60x45',
		'80' => '80x60',
		'138'=> '138x104',
		'168'=> '168x126',
		'180'=> '180x135',
		'245'=> '245x184',
		'257'=> '257x193',
		'322'=> '322x242',
		'360'=> '360x270',
		'384'=> '384x288',
		'400'=> '400x300',
		'400'=> '420x315',
		'600'=> '600x450',	
	),
);
$arrDialogEditText = array(
	'caption',
);
?>


<table class="table" style="width:100%">
	<thead>
	<tr>
		<th></th>
		<th style="width:65px;">Свойство</th>
		<th>Значение</th>
	</tr>
	</thead>
	<tbody>
<?foreach($properts as $prop_name => $prop_value):?>
	<tr <? if(in_array($prop_name, $arrHide)): ?> style="display:none" <?endif;?>>
		<td></td>
		<td><?=$prop_name?></td>
		<td>
<?if(isset($arrSelect[$prop_name])):?>
	<select name="<?=$prop_name?>">
	<?foreach($arrSelect[$prop_name] as $opt_value => $opt_text):?>
			<option <?if($opt_value==$prop_value):?> selected="selected" <?endif;?> value="<?=$opt_value?>"><?= $opt_text ?></option>
		<?endforeach;?>
	</select>
<?elseif($prop_name=='items'):?>
	<div class="ulbox">
		<label class="fr mrl5 label label-inverse callPlus" title="Создать группу"><i class="icon-white icon-plus"></i> группа</label>
		<label class="fr label label-info callPlus" title="Создать значение"><i class="icon-white icon-plus"></i> значение</label>
	</div>
	</td></tr><tr><td colspan="3">
	<ul class="controll_select_ul select_ul">
	<?if(is_array($prop_value))
		foreach($prop_value as $item):?>
			<?if(!isset($item['label'])):?>
				<li class="item li">
					<span class="colm" contenteditable="true"><?=$item['value']?></span>
					<span class="colm" contenteditable="true"><?=$item['text']?></span>
					<label class="fr mrl5 drag label"><i class="icon-white icon-move"></i></label>
					<label class="fr remove label label-important"><i class="icon-white icon-remove"></i></label>
				</li>
			<?else:?>
			<li class="group li">
					<span contenteditable="true" class="colm_group" ><?=$item['label']?></span>
					<label class="fr mrl5 drag label"><i class="icon-white icon-move"></i></label>
					<label class="fr mrl5 remove label label-important"><i class="icon-white icon-remove"></i></label>
					<label class="fr label label-info callPlus" title="Создать значение"><i class="icon-white icon-plus"></i></label>
				<ul class="select_ul">
					<?foreach($item['group'] as $item2):?>
						<li class="item li">
							<span class="colm" contenteditable="true"><?=$item2['value']?></span>
							<span class="colm" contenteditable="true"><?=$item2['text']?></span>
							<label class="fr mrl5 drag label"><i class="icon-white icon-move"></i></label>
							<label class="fr remove label label-important"><i class="icon-white icon-remove"></i></label>
						</li>
					<?endforeach;?>
				</ul>
			</li>
			<?endif;?>
		<?endforeach;?>
	</ul>
	<?else:?>
		<input type="text" name="<?=$prop_name?>" value="<?=$prop_value?>">
		<?if(in_array($prop_name,$arrDialogEditText)):?>
			<button data-propname="<?=$prop_name?>" class="dialog btn btn-mini fr">...</button>
		<?endif;?>
	<?endif;?>

</td></tr>	
<?endforeach;?>
	</tbody>
</table>

<script>
	$(function(){
		
		var prop_box = $('.property_box');	
		prop_box.find(".controll_select_ul").sortable({
			handle : '.drag',
			connectWith: '.select_ul',
			helper : 'original',
			axis :'y', 
			items :'>li'
		});
		// key-value or group delete item to
		prop_box.off('click','.controll_select_ul li .remove');
		prop_box.on('click','.controll_select_ul li .remove', function(ev){
			var _this = $(this);
			_this.closest('li').remove();
		});
		
		// key-value or group add to click
		prop_box.off('click','.callPlus');
		prop_box.on('click','.callPlus', function(ev){
			var _this = $(this), ulbox = _this.closest('li').find('ul:first');			
			if(ulbox.length==0) 
				ulbox = prop_box.find(".controll_select_ul");		
			if(_this.hasClass('label-info')){
				ulbox.append('<li class="item li">'+
					'<span class="colm" contenteditable="true">value</span>'+
					'<span class="colm" contenteditable="true">text</span>'+
					'<label class="fr mrl5 drag label"><i class="icon-white icon-move"></i></label>'+
					'<label class="fr remove label label-important"><i class="icon-white icon-remove"></i></label>'+
				'</li>');		
			}else if(_this.hasClass('label-inverse')){
				ulbox.append('<li class="group li">'+
					'<span contenteditable="true" class="colm_group" >name group</span>'+
					'<label class="fr mrl5 drag label"><i class="icon-white icon-move"></i></label>'+
					'<label class="fr mrl5 remove label label-important"><i class="icon-white icon-remove"></i></label>'+
					'<label class="fr label label-info callPlus" title="Создать значение"><i class="icon-white icon-plus"></i></label>'+
					'<ul class="select_ul"></ul>'+
				'</li>');
				
				
				
			}	
		});
	});	
</script>