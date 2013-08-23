<?

class model_controller extends controller {
    public  $layout = '//main';

    // Форма
    public function actionIndex(){
        $this->pageTitle = 'Конструктор модели';

        $arrDb = array_keys(config::get('db'));

        $this->render('model_form',array(
            'arrDb'=>$arrDb
        ));

    }

    // Создать / изменить
    public function actionSave(){
        PRINT '1';

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
            $db = db::make($db_name);
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

            $db = db::make($db_name);

            $result = array();
            $result['tables'] = $db->show_tables();

        }
        print json_encode($result);
    }



}