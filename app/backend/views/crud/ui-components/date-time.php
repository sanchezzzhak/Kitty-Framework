<?
$format = arr::get($params,'value','');
$format = !empty($format)? :date('d.m.Y H:i:s');
$created_at = strtotime($format);
$date = explode('#',date('d.m.Y#H:i',$created_at));
$hour_time = explode(':',$date[1]);

$uni = uniqid();
?>
<div class="formRow" data-rowid="<?=$uni?>">
	<div class="fl mrr5">
		<label for="hour">Часы</label>
		<select type="text" id="hour" style="width:70px;">
			<?for($i=0; $i <24;$i++):?>
				<option <?if($hour_time[0]==$i):?> selected="selected" <?endif;?> value="<?=$i<9? '0'.$i:$i?>"><?=$i<9? '0'.$i:$i?></option>
			<?endfor;?>
		</select>
	</div>
	<div class="fl mrr5">
		<label for="minute">Минуты</label>
		<select type="text" id="minute" style="width:70px;">
			<?for($i=0; $i <60;$i++):?>
			<option <?if($hour_time[1]==$i):?> selected="selected" <?endif;?>value="<?=$i<9? '0'.$i:$i?>"><?=$i<9? '0'.$i:$i?></option>
			<?endfor;?>
		</select>
	</div>
	<div class="fl">
		<label>Дата</label>
		<input type="text" class="date" value="<?=$date[0]?>" style="width: 73px;">
	</div>
</div>
<script>
$(function(){
	$('.formRow[data-rowid="<?=$uni?>"] .date[type="text"]')
		.removeClass('hasDatepicker')
		.datepicker({changeMonth:true, changeYear:true, defaultDate:+1,dateFormat:'dd.mm.yy', firstDay:1, isRTL:false, showMonthAfterYear:false, yearSuffix:''});
});
</script>