<?
	


//pre(  get_defined_vars());


?>
<html>
	<title>[ERROR] <?=$data['type']?>: <?=$data['message']?></title>
	
	<link rel="stylesheet" type="text/css" href="/assets/backend/css/bootstrap.css">
	<script src="/assets/backend/js/jquery-1.9.1.min.js"></script>
	<script src="/assets/backend/js/bootstrap.js"></script>
	<script src="/assets/backend/js/jquery-ui-1.10.0.custom.min.js"></script>
	<script src="/assets/backend/js/jquery.mousewheel.min.js"></script>
	
	<style type="text/css"><? echo $this->render('_style')?></style>
<body>
<div class="container">
	<h1><?=$data['type']?></h1>

<p class="message">
	<?php echo nl2br(htmlspecialchars($data['message'],ENT_QUOTES))?>
</p>
<div class="source">
	<p class="file"><?php echo htmlspecialchars($data['file'],ENT_QUOTES)?> (<?=ceil($data['line'])?>)</p>
	<?php echo $this->renderSourceCode($data['file'],$data['line']); ?>
</div>

	<div class="traces">
		<h2>Stack Trace</h2>
		<?php $count=0; ?>
		<table style="width:100%;">
		<?php foreach($data['trace'] as $n => $trace): ?>
		<?php
			$cssClass = 'app expanded';
			$hasCode=$trace['file']!=='unknown' && is_file($trace['file']);
		?>
		<tr class="trace <?php echo $cssClass; ?>">
			<td class="number">
				#<?php echo $n; ?>
			</td>
			<td class="content">
				<div class="trace-file">
					<?php if($hasCode): ?>
						<div class="plus">+</div>
						<div class="minus">–</div>
					<?php endif; ?>
					<?php
						echo '&nbsp;';
						echo htmlspecialchars($trace['file'],ENT_QUOTES)."(".$trace['line'].")";
						echo ': ';
						if(!empty($trace['class']))
							echo "<strong>{$trace['class']}</strong>{$trace['type']}";
						echo "<strong>{$trace['function']}</strong>(";
						if(!empty($trace['args']))
							//echo htmlspecialchars($this->argumentsToString($trace['args']),ENT_QUOTES,Yii::app()->charset);
						echo ')';
					?>
				</div>

				<?php if($hasCode) echo $this->renderSourceCode($trace['file'],$trace['line'], 25); ?>
			</td>
		</tr>
		<?php endforeach; ?>
		</table>
	</div>
</div>

<script>
$(function(){
	$('.trace-file').on('click',function(e){
		var _this = $(this);
		
		if(_this.find('.plus').css('display')=='block')
		_this.closest('.content').find('.code').slideDown(200,function(){
			_this.find('.plus').hide();
			_this.find('.minus').show();
		});	
		else
		_this.closest('.content').find('.code').slideUp(200,function(){
			_this.find('.minus').hide();
			_this.find('.plus').show();
		});	
	});
});	
</script>



</body></html>