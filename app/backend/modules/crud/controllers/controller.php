<?
namespace modules\crud\controller;

use \kitty\web\arr;
use \kitty\app\config;

class controller_controller extends \kitty\app\Controller {

    public  $layout = '//main';

    // Форма
    public function actionIndex(){

        $this->pageTitle = 'Конструктор контроллеров';

        $arrPathFrontend = $arrPathBackend = array();
        $path = config::get('dir_path') . "/app/frontend/modules";
        if(is_dir($path)){
             $list = scandir($path);
             foreach($list as $file){
                if($file!='.' && $file!='..' && is_dir($path."/".$file)  )
                    $arrPathFrontend[] =  '/app/frontend/modules/'.$file.'/controllers';
             }
        }

        $path = config::get('dir_path') . "/app/backend/modules";
        if(is_dir($path)){
            $list = scandir($path);
            foreach($list as $file){
                if($file!='.' && $file!='..' && is_dir($path."/".$file)  )
                    $arrPathBackend[] =  '/app/backend/modules/'.$file.'/controllers';
            }
        }

        $this->render('controller_form',array(
            'arrPathFrontend' => $arrPathFrontend,
            'arrPathBackend'  => $arrPathBackend,
        ));

    }

    /*
     * Сохранить контроллер
     **/
    public function actionSave(){

        $this->layout = false;

        $success = false;
        $error = $list = array();

        if(!($name =  $this->post('name',null,'!empty'))){
            $error[] = 'Не назван контроллер';
        }elseif(!preg_match('#^[a-z]{1}([a-z0-9\_]+)$#i',$name) ){
            $error[] = 'Неверно задано название контроллера ';
        }

        if(!($layout = $this->post('layout',null,'!empty')))
            $error[] = 'Не указан слой';

        if(!($path = $this->post('path',null,'!empty')))
            $error[] = 'Не указан путь';


        // doc_root;
        $path = App::getBasePath()."/../../".trim($path,'/');  // путь от /index.php

        $arrAction = $this->post('action',array() );

        $actions = array('index');
        foreach($arrAction as $action){
            if(preg_match('#^[a-z]{1}([a-z0-9\_]+)$#i',$action) ){
                if(!isset($actions[strtolower($action)]))
                    $list[] = 'Метод "' . $action . '" создан';

                $actions[strtolower($action)] = strtolower($action);
            }else{
                $list[] = 'Метод "'.$action . '" не создан, название содержит недопустимые символы';
            }
        }


        if(count($error)==0){

           $content = $this->render('generators/template_controller',array(
                'extend' => '\kitty\app\Controller',
                'name'   => $name,
                'layout' => $layout,
                'actions'=> $actions
           ),true);

            // Создаем контроллер
           $code_file = new CodeFile($path . "/".$name . ".php" , $content, $this->post('overwrite',false) );
           if($code_file->save()){
               $success = true;
               $list[] = "Не забудьте включить модуль в настройках '$name'";
           }else{
                $error+= $code_file->getErrors();
           }
        }


        print json_encode(array(
            'success' => $success,
            'errors'  => $error,
            'list'    => $list,
        ));
    }

}
