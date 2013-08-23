<script>

$(function(){
	

	var grid = [2, 2], 	itab = 1;
		fix_top = $('.form').offset().top,
		id_component = 0, 
		deleteIdList = {};
	
	/**
	 * Загрузить свойства компонента 
	 **/
	function propertyComponent(obj){
		var obj = $(obj).closest('.component_run'), 
			id = obj.attr('id'),
			obj_upd = $('.property_box .content');
			obj_upd.attr('data-id',id);
			if(!obj_upd.hasClass('ajax')){
				$('.property_box .scroll_box').show();
			
				obj_upd.addClass('ajax').html('<span class="ajaxLoader"></span> Загрузка ждите...');
				$('.property_box .property-footer').hide();
				$.ajax({ url: '/admin/crud/property', 
					type: 'post', 
					data: { jsondata: obj.attr('data-options'), type: obj.attr('data-type') }, 
					success:function(data){
						obj_upd.removeClass('ajax');					
						obj_upd.html(data);
						$('.property_box .property-footer').show();
					},
					error: function(data){
						obj_upd.removeClass('ajax');
					}
					
				});
			}
		return false;
	}
	
	/** Сброс выделенных компонентов **/
	function unSelectedComponent(){
		$(".component_run").removeClass('selected');
	}
	
	/** Выделить компонент **/
	function selectedComponent(_this){
		var _this = $(_this);
		unSelectedComponent();
		$(_this).addClass('selected');
	}
	
	/**
	 * Сохранить свойства в компонент и в кеш
	 * кеш отрублин нужно финсить...
	 **/
	function propertyComponentSave(_this){
		
		var _this = $(_this) , 
			obj_form = $('.property_box .content'), 
			id = obj_form.attr('data-id');
		
		if(!_this.hasClass('disabled')){
			var bnt_text =  _this.text() , 
				obj = $('#'+id + '[data-options]');
			_this.addClass('disabled').attr('disabled','disabled').text(_this.attr('data-loading-text'));
			if(obj.length > 0){
				updateComponentDataOptions(obj,true);
			}
			_this.removeClass('disabled').removeAttr('disabled').text(bnt_text);
		}
	}
	
	/**
	 * Перезапись всех свойств и параметров у компонента
	 * @param  obj компонент который нужно отбновить
	 * @param  save = false обновить данные с компонента, true обновить с формы
	 *
	 **/
	function updateComponentDataOptions(obj , save ){
		var _this = $(obj);
		if(_this.is('.component_run')){
			var content = $('.property_box .content[data-id="'+_this.attr('id')+'"]'),
				type = _this.attr('data-type');
			
			if(save==undefined || save==false){			
				var json_data = JSON.parse(_this.attr('data-options') );
				json_data['width']    = _this.css('width');
				json_data['height']   = _this.css('height');
				json_data['left']     = _this.css('left');
				json_data['top']      = _this.css('top');
				json_data['float']    = _this.css('float');
				json_data['position'] = _this.css('position');
				
				var caption = _this.find('[data-attr="caption"]');
				if(caption.length>0){
					json_data['caption'] = caption.html();
				}
				
				if(content.length>0){
					$.each(json_data,function(key,item){
						var input = content.find('[name="'+key+'"]');
						if(input.is('[type="text"]')){ 
							input.val(item); 
						}else if(input.is('select')){ 
							input.find('option[selected]').removeAttr('selected');
							input.find('option[value="'+item+'"]').attr('selected','selected');
						}
					});
					_this.attr('data-options', JSON.stringify( json_data ) );
				}
			}else if(save==true){
				var json_data = jsonForm(content,false);
				_this.css('width', json_data.width).css('height', json_data.height);
				_this.css('top', json_data.top).css('left', json_data.left);
				_this.css('float', json_data.float );
				_this.css('position',json_data.position);
				if(json_data.caption!=undefined){
					_this.find('[data-attr="caption"]').html(json_data.caption);
				}
				if(type=='select' || type=='checkbox-items'){
					var json_select = {}, ul  = content.find('.controll_select_ul > li');	
					if(type=='select'){
						var select = _this.find('select');
						select.html('');
					}
					if(type=='checkbox-items'){
						var list = _this.find('.list');
						list.html('');
					}
					var i=0;
					$.each(ul,function(key,item){
						var item = $(item);
						if(item.is('.item')){
							var value = item.find('span:eq(0)').html() , 
								text  = item.find('span:eq(1)').html();
								json_select[i] = { value:value, text:text };
							
							if(type=='select'){
								select.append('<option value="'+value+'">'+text+'</option>');
							}else if(type=='checkbox-items'){
								list.append('<div><label><input type="checkbox" name="'+json_data.name+'" value="'+value+'"> '+text+'</label></div>');
							}
							
							
							
						}else if(item.is('.group')){
							var json_select_group = {} , ii = 0; 
								value = item.find('span:eq(0)').html();
								
								if(type=='select'){
									var optgroup = $('<optgroup>', {label: value });
								}else if(type=='checkbox-items'){
									var optgroup = $('<div>'+value+'</div>');
								}
							$.each(item.find('li'),function(key,item){
								var item = $(item), 
									value = item.find('span:eq(0)').html() , 
									text  = item.find('span:eq(1)').html();
								json_select_group[ii] = { value:value, text:text }; ++ii;
								
								if(type=='select'){
									optgroup.append('<option value="'+value+'">'+text+'</option>');
								}else if(type=='checkbox-items'){
									optgroup.append('<div><label><input type="checkbox" name="'+json_data.name+'" value="'+value+'"> '+text+'</label></div>');
								}
							});
							if(type=='select'){
								select.append(optgroup);
							}else if(type=='checkbox-items'){
								list.append(optgroup);
							}
							
								json_select[i] = {label: value, group : json_select_group };
							
						};
						++i;
					});
					json_data['items'] = json_select;
					
				}
				_this.attr('data-options', JSON.stringify(json_data) );						
			}	
		}
	}
	
	/**
	 * Иницилизация вкладки 
	 **/
	function createAddList(ID){
		var tmp;
		if( $(ID).length > 0 ){  tmp = $(ID); }else{ tmp = $('<div>' , {class  :'cp_dising'}); }
		
		return tmp.droppable({
			accept:function(obj){
				if (obj.is('.component_item')){
					return true;
				}else if(obj.is('.component_run[data-parent]')){
					var parent = obj.attr('data-parent');
					if(parent > 0) return true;
				}
				return false;
			},
			drop: function(ev, ui) {
				if (ui.draggable.is('.component_item')){
                    id_component = id_component + 1;
					var pos = ui.position,
						divupdate = 'new_feald_' + id_component,
						component_name = $(ui.draggable).attr('data-type'),
						options_json = $(ui.draggable).attr('data-options'),
						params = $.parseJSON(options_json),
						e = $('<div>' , {
							'class':'component_run',     
							'style':'top:'+parseInt(pos.top-50)+'px; left:'+pos.left+'px; position:absolute;', 
							'id': divupdate,          
							'data-type': component_name,
							'data-options': options_json, 
							'data-parent': 0,  
							'data-id': id_component,  // tmp ID  
							'new':true   // tmp attr
						});
						initComponentRun(e);
						// rpeload component
						$.ajax({type:"POST", url: '/admin/crud/InsertComponent', 
							data:{ 'component': component_name },
							error:function(data){},
							success:function (data) {
								e.append(data);
								initColumnDroppable(e);
							}
						});
						e.css('width',params.width);
						e.css('height',params.height);
						$(this).append(e);
				
				}else if(ui.draggable.is('.component_run')){
					var column = $(ui.draggable).closest('.column');
					if(column.length>0 && $('.drop-hover').length==0){
						var $clone = $(ui.draggable).clone();
						$clone.attr('data-parent',0);
						initComponentRun($clone);
						initColumnDroppable($clone);
						ui.draggable.detach();
						var pos = ui.offset;
						$clone.css('top',parseInt(pos.top-fix_top) +'px');
						$clone.css('left',pos.left+'px');
						
						$(this).append($clone);
						var script = $clone.find('script');
						if(script.length>0){
							$.globalEval(script.html());
						}
						
					}
				}
			}	
		});
		
	}
	
	/**
	 * Фикс высота-ширина колонки 
	 * по размеру области компонента в контейнерах
	 **/
	function columnSizeFix(_this){
		var column = _this.find('.column:first'), h = 0;
		if(column.length >0) {
			if(_this.is('[data-type="head-panel"]')) h = _this.find('.b-head').height() + 20;	
				column.css('width',_this.width() + 'px');
				column.css('height',_this.height()-h  + 'px');
		}
	}
	/** Фикс при ресайзе в сортируемой области **/
	function columnResizatableFix(_this){
		var _this = $(_this);
		if(_this.closest('.column.ui-sortable').length>0){
			_this.css('top','0px');
			$('.ui-resizable-helper').css('top', _this.offset().top+'px');
		}
	}
	/**
	 * Растягивания компонента
	 **/
	function initResizable(e){
		var e = $(e), infosize_box = null;
		e.removeClass('ui-resizable'); 
		e.find('.ui-resizable-handle').remove();
		e.resizable({
			alsoResize:false,
			helper: 'ui-resizable-helper',
			start: function(ev,ui){
				selectedComponent(e);
				columnResizatableFix(this);
				infosize_box = e.find('.infosizebox');
				if(infosize_box.length==0){
					e.append( '<div class="infosizebox">'+Math.round(ui.helper.width())+'x'+Math.round(ui.helper.height())+'</div>');
					infosize_box = e.find('.infosizebox').fadeIn(350);				
				}
			},
			resize:function(ev,ui){
				columnResizatableFix(this);
				infosize_box.html( Math.round(ui.helper.width())+'x'+Math.round(ui.helper.height()) );
			},
			stop: function(ev,ui){
				columnSizeFix(e);
				updateComponentDataOptions(e);
				unSelectedComponent(e);
				infosize_box.remove();	
			},
			handles: 'n, e, s, w, ne, se, sw, nw',
			ghost: true ,
			grid: grid
		});
		
	}
	
	/**
	 * Передвижение компонента
	 * Вызов конфига
	 * Удаление компонента + вложеные компоненты если есть
	 **/
		 
	function initDraggable(e){
		if( e.find('.prop').length==0 ){
			e.append('<div class="prop" >'+
				'<label class="label label-inverse drag" title="Двигать">'+
				'<i class="icon-white icon-move"></i></label>'+			
				'<label class="label config" title="Настр.">'+
				'<i class="icon-white icon-cog"></i></label>'+			
				'<label class="label label-important remove" title="Удалить">'+
				'<i class="icon-white icon-remove"></i></label>'+
			'</div>');
		}
		
		e.find('.config').on('click',function(ev){
			propertyComponent(this);
		});
		
		/*
		e.on('click',function(ev){
			if(!$(this).hasClass('selected'))
				selectedComponent(this);
			else unSelectedComponent(this);
		});
		*/
		
		e.find('.remove').on('click',function(ev){
			if(confirm('Подтвердите удаление')){
				
				$.each(e.find('.component_run'),function(key,item){
					var obj = $(item);
					if(!obj.is('[new]')){
						deleteIdList[ obj.attr('data-id')+'' ] = obj.attr('data-id');
					}else obj.remove();
				});
				if(!e.is('[new]')){
					deleteIdList[ e.attr('data-id')+'' ] = e.attr('data-id');
				}else e.remove();
			}
		});
		
		e.draggable({
			ghosting:false,
			handle: ".drag", 
			containment:'parent',
			start: function(ev, ui){
				selectedComponent(ev.target);
			},
			stop:function(ev,ui){
				unSelectedComponent(ev.target);
				updateComponentDataOptions(e);
			}
		});	
	}
	

	
	
	
	
	/**
	 * Колонка в компоненте + сортировка + передвежение компонента обратно на уровень ниже.
	 **/
	function initColumnDroppable(e){
		var _this = $(e), column =  _this.find(".column");
			columnSizeFix(_this);	
		if(column.length > 0){
			column.droppable({
				hoverClass: "drop-hover",
				accept:function(obj){
					// Запрет дроп колонки в колонку
					// Так-же разрешаем дропатся компонентам 
					if(obj.is('[data-type="ckeditor"]')) return false;
					if(obj.is('.component_run')){
						if( obj.find('.column').length > 0 ) return false;
						return true;
					}
					return false;
				},
				
				drop: function(ev, ui) {
					if (ui.draggable.is('.component_run')){
						var $clone = $(ui.draggable).clone();							
							ui.draggable.detach();
							// фикс
							$clone.css('left',0 +'px');
							$clone.css('top', 0 +'px');
							$clone.attr('data-parent', _this.attr('data-id') );
							// сортировка
							column.sortable({
								connectWith: '.column',
								helper: 'original',
							}).disableSelection();
							initComponentRun($clone);
							// setter: отрубаем ограничения на контейнер.
							$clone.draggable('option', 'containment', false);
						
						
							$(this).append($clone);
							var script = $clone.find('script');
							if(script.length>0){
								$.globalEval(script.html());
							}
						}
				}
			});
			
			
		}	
		
	}
	/** 
	 * Даем функционал для компонента 
	 **/
	function initComponentRun(e){
		initDraggable(e);
		initResizable(e);
	}
	
	/** Создает дополнительную вкладку + лист **/
	function tabCreate(){
        var div =  $('<div>' , { class  :'cp_panel' , style:'height: 100%;' , id:'tab'+itab}).append( createAddList() );
        $('#listtab').append(div);
        itab = itab+1;
    }
	/** Переключение вкладки  **/	
	function tabClick(){
		var _this = $(this);
		$('#tabs').find('li.active').removeClass('active');
		_this.addClass('active');
		$('#listtab > div').hide()
		$('#' + _this.attr('tab')).show()
		return false;
	}
	
	/** Иницилизация **/
		
	$('#addTab').click(function(){
        if($('#tabs li[tab="tab'+itab+ '"]').length > 0 ){ 
			itab=itab +1; 
			$(this).click();
			return;
		}
        var li = $(this).parent(), licount = $('#tabs li[tab]').length;
        li.before('<li  tab="tab'+itab+'"><a title=""><span>Новый Таб-лист '+ itab + '</span></a><b class="tabclose_bnt"></b></li>');
        if(licount>0) $('#tabs li[tab="tab0"]').show(); 
		tabCreate();
	});
	
	$('#tabs ul').on('click', 'li[tab]', tabClick );
	// Редактировать свойства в спецальном dialog	
	$('.property_box').on('click','button[data-propname]', function(ev){
		var _this = $(this);
		$('#dialog #modalLabel span').empty().html( _this.attr('data-propname') );
		var _html = $(
		'<p><label>Значение свойства</label>'+
			'<textarea style="width: 98%;"></textarea>'+
		'</p>');
		
		_html.find('textarea').val($('.property_box input[name="'+_this.attr('data-propname')+'"]').val());
		
		$('#dialog .modal-body').html(_html);
		$('#dialog').modal('show');
	});
	
	// Редактировать область тега текстовки
	var timeoutID;
		$('.form').on('DOMCharacterDataModified','[contenteditable]', function(ev) {
			clearTimeout(timeoutID);
			var _this = $(this);
			timeoutID = setTimeout(function() {
				_this.trigger('change');
			},1e3);
		});
	// Обработка действия редактирование тега текстовки
	$('.form').on('change','[contenteditable]', function(ev) {
		var _this = $(this);
		if(_this.is('[data-attr="caption"]')){
			var component = _this.closest('.component_run');	
			updateComponentDataOptions(component);
		}
	})
	

	// drag && drop создание компонента 
	$(".component_item").draggable({helper: 'clone'});
	// настройка компонента 
	$('.property_box .scroll_box').mCustomScrollbar({set_height:280, set_width:260 ,advanced:{updateOnContentResize:true} })
	
	
	// Передвижение окна свойств
	$(".property_box").draggable({ handle:'.b-head', containment:'parent' });
	// Закрытие окна по клику кнопки
	$('#closeProperty').on('click',function(){
		$('.property_box .content').empty();
		$('.property_box .scroll_box').hide();
		$('.property_box .property-footer').hide();
	});
	// сохранить настройки свойств
	$('#saveProperty').on('click',function(){
		propertyComponentSave(this);
	});
	
	// сброс селекта компонента по клику на листе
	$('.cp_dising').on('click',function(event){
		var $target = $(event.target);
		if($target.hasClass('cp_dising')) 	unSelectedComponent();
	});
	// Создаем базовый лист
	createAddList( $("#tab0").find('.cp_dising')  );
	// Сохраняем форму
	$('#saveForm').on('click',function(e){
		var _this = $(this) , jsonData = {};
		if(!_this.hasClass('disabled')){
			var bnt_text =  _this.text();
			_this.addClass('disabled').attr('disabled','disabled').text(_this.attr('data-loading-text'));
			jsonData['components'] = {};
			$.each( $('.component_run'),function(key,item){
				var comp = $(item);
				jsonData['components'][key+''] = {
					options: comp.attr('data-options'),
					type: comp.attr('data-type'),
					isnew: comp.is('[new]'),
					id: comp.attr('data-id'),
					parent:comp.attr('data-parent'),
					tab: comp.closest('.cp_panel').attr('id')
				};
			});
			jsonData['delete_list'] = deleteIdList;
			
			$.ajax({url:'/admin/crud/UiSave', data:jsonData, dataType:'json', type: 'post',
				success:function(data){
					if(data.success==false){
						// TODO сделать сообщения об ошибке
					}
					_this.removeClass('disabled').removeAttr('disabled').text(bnt_text);
					// заменяем новые кмопоненты на нужные ID
					$.each( data.replaceId, function(key,item){
						$('.component_run[data-id="'+key+'"]').attr('data-id',item.id)
						.attr('data-parent',item.parent).removeAttr('new');
					});
					// Обновляем у старых компонентов parent если он менялся 
					$.each( data.updateId, function(key,item){
						$('.component_run[data-id="'+key+'"]').attr('data-id',item.id)
						.attr('data-parent',item.parent);
					}); 
					
					
				},
				// TODO Ошибка запроса обрабатываем и говорим
				error:function(){
					_this.removeClass('disabled').removeAttr('disabled').text(bnt_text);
				}
			});
			
			
		
		}
		
	});
	// Настройка формы
	$('#confForm').on('click',function(e){
		var _this = $(this), btn_text = _this.text();
		_this.text(_this.attr('data-loading-text'));
		_this.attr('data-loading-text',btn_text);
		var panel = $('.panel-config');
		if(panel.hasClass('hide')){
			panel.removeClass('hide');
			panel.slideDown(800);
		}else{
			panel.slideUp(800,function(){
				panel.addClass('hide');
			});	
		}
	});
	/*$('.error').html('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><h4>Error!</h4><ul></ul></div>');*/
});
	
</script>



		



<? 
	/*

	$grid = new GridModel(array(
		'model' => new Content,
		'tableСssClass' => 'table-hover',
		'columns' => array(
			'id'   => array(
				'value'  => '$data->id',
				'style'  => 'width:50px;',
			),
			'code' => array(
				'filter' => true,
				'name'   => 'Код страницы',
				'value'  => '$data->code',
			),
			'action' => array(
				'filter' => false,
				'value'  => '   "<a href=\"?edit=".$data->id."\"><i class=\"icon-edit\"></i></a>" ',
			)
		),
	
	));
	$grid->render();
	*/
?>
	<? include dirname(__FILE__) . "/_menu.php";?>
		
	<div class="error"></div>
	<div style="margin:10px;">
			<!--<button type="button" id="resetForm" class="btn btn-large">Сбросить</button>-->
		<button type="button" id="saveForm" class="btn btn-large btn-primary" data-loading-text="Обработка...">Сохранить</button>
		
		<button type="button" id="confForm" class="btn btn-large btn-warning" data-loading-text="Скрыть...">Настройка</button>
	</div>
	
	<div class="panel-config hide">
		
		
	</div>

	<div class="property_box">
		<div class="b-head"><label><i class="icon-list-alt"></i> Свойтсва</label></div>
			<div class="scroll_box" style="display: none; margin-top:5px;">
				<div class="content" data-id=""></div>
			</div>
		<div class="property-footer" style="display:none; margin-top:5px;">
			<button type="button" id="saveProperty" class="btn btn-primary btn-mini" data-loading-text="Обработка...">Сохранить</button>
			<button id="closeProperty" class="btn btn-mini">Закрыть</button>
		</div>
	</div>
	
	<div style="position:relative;height: 30px;">
		<div class="components" style="position:absolute;">
			<?foreach($components as $data_type => $component ):?>
			<a href="javascript:;" class="component_item btn btn-small" 
			data-type="<?=$data_type?>" 
			data-options='<?=json_encode($component)?>'>
				<img src="/assets/backend/media/icon-components/<?=$data_type?>.png">
					<?=$data_type?></a>
			<?endforeach;?>	
		</div>
	</div>
		
	<div class="wrapper" style="background: white; display: inline-block; margin:1px;">
		<div class="tabcontrol" id="tabs">
			<ul>
				<li tab="tab0" style="display:none;"><a href="#"><span>Основной</span></a></li>
				<?/*
				foreach($tabs as $key=> $text):?>
				<li tab="<?=$key?>" onclick="return tabSelect(this)"><a> <span><?=$text?></span></a><?if($key!='tab0'):?><b class="tabclose_bnt"></b><?endif;?></li>
				<?endforeach;*/?>
				<li class="tipW" title="Добавить новый лист"><a id="addTab" href="#" title=""><span>+</span></a></li>
			</ul>
		</div>
	</div>
	
<div class="clear"></div>
<div class="form" style="height:2000px;" id="listtab">
	<div class="cp_panel" id="tab0"><div class="cp_dising"></div></div>
	
</div>

	


<div style="height:50px" class="clear"></div>


<div id="dialog" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="modalLabel">Редактировать свойство <span></span></h3>
	</div>
	<div class="modal-body">
		
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true"><i class="icon-ban-circle"></i> Отмена</button>
		<button id="savePropertyModal" class="btn btn-primary" data-dismiss="modal" aria-hidden="true"><i class="icon-ok"></i> Пременить</button>
	</div>
</div>

