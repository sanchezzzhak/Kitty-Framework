<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Александр
 * Date: 23.08.13
 * Time: 13:10
 * To change this template use File | Settings | File Templates.
 */
class controller_controller extends Controller {

    public  $layout = '//main';

    // Форма
    public function actionIndex(){
        $this->pageTitle = 'Конструктор контроллеров';


        $this->render('controller_form',array(

        ));

    }

    /*
     * Сохранить контроллер
     **/
    public function actionSave(){
        $this->layout = false;
        $success = false;
        $error = $list = array();

        $actions = array('index');

        if(!($name =  $this->post('name',null,'!empty'))){
            $error[] = 'Не назван контроллер';

        }elseif(!preg_match('#^[a-z]{1}([a-z0-9\_]+)$#i',$name) ){
            $error[] = 'Неверно заданно название контроллера';
        }

        if(!($layout = $this->post('layout',null,'!empty')))
            $error[] = 'Не указан слой';


        if(!($path = $this->post('path',null,'!empty')))
            $error[] = 'Не указан путь';


        // doc_root;
        $path = App::getBasePath()."/../../".trim($path,'/');  // путь от /index.php

        $arrAction = $this->post('action');

        foreach($arrAction as $action){
            if(preg_match('#^[a-z]{1}([a-z0-9\_]+)$#i',$action) ){
                if(!isset($actions[strtolower($action)]))
                    $list[] = 'Метод "'.$action . '" создан';

                $actions[strtolower($action)] = strtolower($action);
            }else{
                $list[] = 'Метод "'.$action . '" не создан, название содержит недопустимые символы';
            }
        }



        if(count($error)==0){

           $content = $this->render('generators/template_controller',array(
                'extend' => 'Controller',
                'name'   => $name,
                'layout' => $layout,
                'actions'=> $actions
           ),true);

            // Создаем контроллер
           $code_file = new CodeFile($path . "/".$name . ".php" , $content, $this->post('overwrite',false) );
           if($code_file->save()){
               $success = true;
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
