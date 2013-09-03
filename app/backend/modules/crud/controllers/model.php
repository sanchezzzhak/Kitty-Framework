<?
namespace app\modules\crud\controller;

use kitty\web\arr;

class model_controller extends \kitty\app\controller {
    public  $layout = '//main';

    // Форма
    public function actionIndex(){
        $this->pageTitle = 'Конструктор модели';

        $arrDb = array_keys(\kitty\app\config::get('db'));

        $this->render('model_form',array(
            'arrDb'=>$arrDb
        ));

    }

    // Создать модель
    public function actionSave(){
        $this->layout = false;
        $success = false;

        $error = $list = $attr = $rules = array();

        if(!($name =  $this->post('name_model',null,'!empty'))){
            $error[] = 'Не заданно название модели';
        }elseif(!preg_match('#^[a-z]{1}([a-z0-9\_]+)$#i',$name) ){
            $error[] = 'Неверно заданно название модели';
        }

        if(!($path = $this->post('path_model',null,'!empty')))
            $error[] = 'Не указан путь для сохранения модели';

        if(!($db_model = $this->post('db_model',null,'!empty'))){
            $error[] = 'Не выбрана конфигурация БД';
        }

        if(!($table = $this->post('table',null,'!empty'))){
            $error[] = 'Не выбра таблица';
        }

        if(!($pk_id = $this->post('pk_id',null,'!empty'))){
            $error[] = 'Не указан PK таблицы';
        }

        $path = \kitty\app\App::getBasePath()."/../../".trim($path,'/');  // путь от /index.php

        // Выбранные аттребуты
        $attr = $this->post('attr',null, array() );


        if(count($error)==0){

            $content = $this->render('generators/template_model',array(
                'name'  => ucfirst($name),
                'table' => $table,
                'db' => $db_model,
                'id' => $pk_id,
                'attrArr'  => $attr,
                'rules' => $rules,
            ),true);

            $code_file  = new CodeFile($path . "/".strtolower($name) . ".php" , $content, $this->post('overwrite',false) );
            if(!$code_file->save()){
                $error+= $code_file->getErrors();
            }else{
                $success = true;
            }
        }

        print json_encode(array(
            'success' => $success,
            'errors'  => $error,
            'list'    => $list,
        ));

    }

    /**
     * Получение полей у таблицы
     **/
    public function actionGetTableInfo(){
        $result = array();
        $this->layout = false;
        $db_name = $this->post('db');
        $table_name = $this->post('table');
        if(isAjax() && !empty($db_name) && !empty($table_name)){
            $db = \kitty\db\db::make($db_name);
            $result = $db->show_columns($table_name);
        }
        print json_encode($result);
    }

    /*
     * Список таблиц указаной DB
     **/
    public function actionGetDbTableList(){
        $result = array();
        $this->layout = false;
        $db_name = $this->post('db',null);
        if(isAjax() && !empty($db_name)){

            $db = \kitty\db\db::make($db_name);

            $result = array();
            $result['tables'] = $db->show_tables();

        }
        print json_encode($result);
    }



}