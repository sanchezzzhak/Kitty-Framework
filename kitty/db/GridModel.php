<?php
namespace kitty\db;
use \kitty\web\arr;

/*
 * Кастомный рендер для колонки кнопок 
 **/
/*
class GridColumnBtn(){
	
	public function render(){
		return $html;
	}	
	
	
}
*/



/*
 * Грид на основе модели
 * 
 **/
 
class gridModel
{
   public 
    $isAjax        = true,	    // Использовать аякс для пагинации и фильтров?
    $page          = 1,     	// Номер страницы
	$pageSize      = 50,		// Количество на страницу
    $model         = null,  	// Модель
	$relations     = array(),
	
	$tableСssClass = '', 		// Указать класс для таблицы
	$tableStyle    = '', 		// Стиль для таблицы
	
	$columns       = array(
		/*
		Именованный ключ  - имя столбца
			value  => $data->id  |callback   Вывести атребут или анонимная функция
		    filter => false|array|callback   Фильтр false не использовать массив выподающий список или анонимная функция
			name   => текстовое имя столбца
			style  => css стиль колонки
			class  => собственный класс для вывода ячейки
		 */
	);   // Список столбцов     
   
    public function __construct( $config = array() ){
		$this->model  = arr::get($config , 'model' , null );
		if(is_null( $this->model)) {
			throw new \kitty\base\ExceptionError('Не указана модель');
		}
		$this->relations      =  arr::get( $config, 'relations', array() );
		$this->columns = arr::get( $config ,'columns',array());
		$this->page           = arr::get($config,'page',1);
		$this->pageSize       = arr::get($config,'pageSize',50);
		$this->tableStyle     = arr::get($config,'tableStyle','');
		$this->tableСssClass  = arr::get($config,'tableСssClass','');
		
    }

	
	/*
	 * Рендер представления 
	 **/
    public function render( $return  = false ){
		
		$model_name =  get_class($this->model);
		$get        =  $_GET;
		
		$filter_get =  arr::get($get,'model_'.$model_name, array() );
		
		// Поиск 
		 
		$offset = startPage( $this->page , $this->pageSize );
		$where      = '';
		$params     = array();
		$order      = array();
		$count = 0; // бинд переменая
		$arrData    =  $model_name::model()->findAll($where , $params , $order ,  $this->relations , $offset , $this->pageSize, $count );
		
		$countPage  = countPage($count , $this->pageSize);
		$pageArr = pageArr($countPage, $this->page, 6);
		// рендеринг 
		
		$pagination  = '<div class="pagination fr mrr5"><ul>';
		foreach($pageArr as $page){
			$pagination .= '<li><a href="#">'.$page.'</a></li>';
		}
		$pagination .= '</ul></div>';
		
		
		$html = "<script>$(function(){gridmodel('{$model_name}');});</script>";
		$html .= '<div class="gridbox"><div class="ajaxLoader" style="display:none;"></div>';
		
		$html .= '<table  data-name="'. $model_name . '" class="table '
		.( empty($this->tableСssClass) ?  'table-condensed' : $this->tableСssClass  )
		.'" '.(empty($this->tableStyle)?'':' style="'. $this->tableStyle .'"').'><thead>';
		
		
		$filter_html = '<tr>';
		$html .='<tr>'; 		
			$icolumn = 0;
			foreach($this->columns as $column => $item){
				$name = !empty($item['name']) ? $item['name'] : $column;
				$html .= '<th'.(!isset($item['style'])?'':' style="'. $item['style'] .'"').'>' . $name. '</th>';
				// фильтер ячейки
				$filter = '';
				if(!isset($item['filter']) or $item['filter']==true){ 
					$value = arr::get($filter_get,$column,'');
					$filter = '<input type="text" style="width:98%" data-filter="'.$column.'" value="'.$value.'">'; 
				}elseif(is_array($item['filter'])){
					$filter = '<select style="width:98%" data-filter="'.$column.'">';
					$value = arr::get($filter_get,$column,'');
					foreach($item['filter'] as $key=>$option){
						$filter.='<option value="'.$key.'"'.( $key==$value ? ' selected="selected" ':'').'>'.$option.'</option>'; 
					}
					$filter .= '</select>';
				}
				$filter_html .= '<td>'.$filter.'</td>';
				
				++$icolumn;
			}
		$html .= '</tr>';
		$filter_html.= '</tr>';
		$html .= $filter_html . '</thead><tbody>';
		// data-raw
		if($arrData){
			foreach($arrData as $model){
				$html .= '<tr>';   
					foreach($this->columns as $column => $item){
						
							$value = isset($item['value']) ? $item['value']  : '$data->'.$column;
							$html.= '<td data-attr="'.$column.'">';
							$html.= $this->itemCeil( $value , array( 'data'=> $model) );
							$html.= '</td>';
						
					}
				$html .= '</tr>';

			}
		}else{
			$html.='<tr><td colspan="'.$icolumn.'">  <i>Список пуст</i> </td></tr>';
		}
		$html .= '</tbody></table>';
		$html .='</div>';
		
		if($return) 
			return $html;
		else
			echo $html;
	}
	
	/**
	 * Содержимое ячейки
	 * @param string|callback значение или анонимная функция 
	 * @param $__data__ параметры в виде массива 
	 **/
	public function itemCeil($__item__ , $__data__ = array() ){
		// pre(get_defined_vars());
		// pre($__item__);
		if(is_string($__item__) ){
			extract($__data__);
			return  eval( 'return '. $__item__ . ';');
		}elseif(is_callable($__item__)){
			return call_user_func_array($__item__ , $__data__ );
		}
	}
	
	
	
	
}
