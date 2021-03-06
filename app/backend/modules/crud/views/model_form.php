<?
	include dirname(__FILE__) . "/_menu.php";
?> 

<style>
.boxscroll {box-shadow: 0 0 8px #CCC;padding: 5px;margin:0 0 5px;}
.l1 {background:#fff;}
.l2 {background:#ECECEC;} 
.boxscroll li {line-height: 25px;padding-left:10px}	

.controll_toolbar_rule { margin-bottom: 10px; }
.rules_list {  box-shadow: 0 0 8px #CCC; }
.rule { padding-left:10px;}
.rule.color1{ background:#ECECEC;}
.rule.color2{ background:#fff; }
.rule .colum { margin: 5px 2px 2px; }

</style>	

<script>
$(function(){
	

	// Выбор конфига БД
	$('.db_model').on('click','li',function(ev){
		var _this = $(this);
		if(!$('.db_model .ajaxMini').is('span')){
			$('.db_model li').removeClass('selected');
			_this.addClass('selected');
			_this.append('<span class="ajaxMini fl" style="margin: 5px 5px;"></span>');

            $('input[name="table"]').val('');
            $('input[name="db_model"]').val( _this.text() );

            $('.model-property table tbody').empty();

			$.ajax({ type:'post', url: '/admin/crud/model/GetDbTableList', data: {db:_this.text()} , dataType:'json',
				success:function(data){
					$('.table_model').empty();
					$.each(data.tables, function(key,item){
						$('.table_model').append('<li class="l'+(key%2?'1':'2')+'">'+item+'</li>');
					});
					_this.find('.ajaxMini').remove();
				},
				error:function(){
					_this.find('.ajaxMini').remove();
				}
			});
		}
		});
		
		// Выбор таблицы
		var model_table = {};
		$('.table_model').on('click','li',function(ev){
			var _this = $(this);
			if(!$('.table_model .ajaxMini').is('span')){
				$('.table_model li').removeClass('selected');
				_this.addClass('selected');				
				_this.append('<span class="ajaxMini fl" style="margin: 5px 5px;"></span>');


                $('input[name="table"]').val( _this.text() );

				$.ajax({ type:'post', url: '/admin/crud/model/GetTableInfo',
					data: {
						db: $('.db_model li.selected').text(),
						table:_this.text()
					} , dataType:'json',
					success:function(data){
						model_table = data;
						//$('.table_model').empty();
						var table = $('.model-property table tbody');
							table.empty();
						$.each(data, function(key,item){
							table.append('<tr>'+

                            (item.isPrimaryKey==true ? '<input type="hidden" name="pk_id" value="'+item.name+'"> ': '')+

                            '<td>'+item.name+ '</td>'+
							'<td><input type="text" style="width:130px;" value="'+item.name+'"></td>'+
							'<td><input type="checkbox" name="attr['+item.name+'][name]" '+ ( item.isPrimaryKey==true ? 'disabled="disabled" ' : '')  +'checked="checked"></td>'+
							'</tr>');
						});
						_this.find('.ajaxMini').remove();
					},
					error:function(){
						_this.find('.ajaxMini').remove();
					}
				});
			}
		});
	
		$('.add_rule').on('click',function(ev){
			var list = $('.rules_list');
			var type = $('.type_rule').val();
			var l  = $('.rules_list .rule').length;
			
			list.append('<div class="rule color'+(l % 2 ? '1':'2')+'">'+
				'<div class="fl colum"><label><small>Выбрать поля</small></label><br>'+'<input type="text"></div>'+
				'<div class="fl colum" style="width:60px;"><label><small>Тип</small></label>'+type+'</div>'+
				'<div class="fl colum" style="width:280px">'+
				
				
					'<label><small>Текст ошибки</small></label><input type="text" class="text"> '+
				'</div>'+
				
				'<div class="fr colum" style="width:30px;"><a href="javascript:;"><i class="icon-remove"></i></a></div>'+
				'<div class="clear"></div>'+
			'</div>');

		});

        $('.rule_list').on('click','.icon-remove',function(e){
            $(this).closest('.rule').remove();
        })

	
	
	$('.boxscroll').mCustomScrollbar({set_height:100, set_width:300 ,advanced:{updateOnContentResize:true} })
	
	
	$('#saveForm').on('click',function(e){
		var _this = $(this) , jsonData = {};
		if(!_this.hasClass('disabled')){
			var bnt_text =  _this.text();
			_this.addClass('disabled').attr('disabled','disabled').text(_this.attr('data-loading-text'));
            var dataJson = jsonForm('.form');

            $.ajax({
                url:'/admin/crud/model/save',
                data: dataJson, type:'post', dataType:'json',
                success: function(m){
                    if (m.success == false) {
                        $('.error').html('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><h4>Error!</h4><ul></ul></div>');
                        $.each(m.errors, function (key, item) {
                            $('.error ul').append('<li>' + item + '</li>');
                        });
                    } else if (m.success == true) {
                        $('.error').html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button><h4>Success!</h4>Модель сохранена<ul></ul></div>')
                        $.each(m.list, function (key, item) {
                            $('.error ul').append('<li>' + item + '</li>');
                        });
                    }
                    _this.removeClass('disabled').removeAttr('disabled').text(bnt_text);
                },
                error: function(){
                    _this.removeClass('disabled').removeAttr('disabled').text(bnt_text);
                }
            });

		}
	});
	
});
</script>


<div class="error"></div>
<div style="margin:10px;">
	<button type="button" id="saveForm" class="btn btn-large btn-primary" data-loading-text="Обработка...">
	Сохранить</button>
</div>

<div class="form row-fluid">
    <blockquote>

    <div class="row-fluid">
        <div class="span6">
            <div class="span6">
                <div class="b-head"><label>Путь куда сохранять модели</label></div>
                <input type="text" name="path_model" class="span12" value="/app/common/models/">
            </div>

            <div class="span6">
                <div class="b-head"><label>Название модели</label></div>
                <input type="text" name="name_model" class="span12">
            </div>
        </div>
	</div>

    <div class="row-fluid">
        <div class="span6">
            <div class="span6">
                <div class="b-head"><label>Конфиг соединения БД  </label></div>
                <div class="boxscroll"><ul class="unstyled db_model" >
                    <?foreach($arrDb as $key => $item):?>
                    <li class="l<?=$key%2? '1':'2';?>"><?=$item?></li>
                    <?endforeach;?>
                </ul></div>
                <input type="hidden" name="db_model">
            </div>

            <div class="span6">
                <div class="b-head"><label>Выберите таблицу</label></div>
                <div class="boxscroll"><ul class="unstyled table_model" >
                </ul></div>
                <input type="hidden" name="table">
            </div>
        </div>
    </div>

	<div class="row-fluid">
		<div class="span6">
			<div class="b-head"><label>Свойства модели</label></div>
			<div class="model-property">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Имя свойства</th>
							<th style="width:130px">Названия</th>
							<th style="width: 20px;">Испол.</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>

    <div class="row-fluid">
        <div class="span6">

	    <div class="b-head"><label>Валидация свойств</label></div>
            <!--<div class="alert alert-block"><button type="button" class="close" data-dismiss="alert">×</button>
            <h4>Помощь</h4><p>
              required - <br>
              match - <br>
              length - <br>
              email - <br>
              in - <br>
              type - <br>
              </p></div>-->
        </div>
    </div>
    <div class="row-fluid">
        <div class="span6">
            <div class="controll_toolbar_rule">
                <button class="btn add_rule">Создать правило</button>
                Тип правила
                <select class="type_rule" style="margin:0;">
                    <option value="required">required</option>
                    <option value="match">match</option>
                    <option value="length">length</option>
                    <option value="email">email</option>
                    <option value="in">in</option>
                    <option value="type">type</option>
                </select>
            </div>
            <div class="rules_list"></div>
		</div>
    </div>
    <div class="row-fluid">
        <div class="span6">
            <div class="disabled">
                <div class="b-head"><label>Связи между моделями </label></div>
            </div>
        </div>
    </div>
    </blockquote>
</div>



