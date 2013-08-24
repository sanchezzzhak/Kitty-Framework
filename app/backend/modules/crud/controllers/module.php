<?php

class module_controller extends Controller{
    public  $layout = '//main';

    // Форма
    public function actionIndex(){
        $this->render('module_form');
    }

    // Создать модуль
    public function actionSave(){
        $this->layout = false;
        $success = false;
        $error = $list = array();

        if(!($name =  $this->post('name',null,'!empty'))){
            $error[] = 'Не заданно название модуля';
        }elseif(!preg_match('#^[a-z]{1}([a-z0-9\_]+)$#i',$name) ){
            $error[] = 'Неверно заданно название модуля';
        }

        if(!($path = $this->post('path',null,'!empty')))
            $error[] = 'Не указан путь для сохранения модуля';

        $path = App::getBasePath()."/../../".trim($path,'/');  // путь от /index.php



        // Создаем деректорию для модуля и контроллера
        $path.= "/". trim($name,'/');
        $dir_control = $path."/controllers";
        if(!is_dir($dir_control)){
            $oldmask=@umask(0);
            $result=@mkdir($dir_control,0777,true);
            @umask($oldmask);
            if(!$result) {
                $error[] ="Не возможно создать директорию '$dir_control'.";
            }else{
                $oldmask=@umask(0);
                @mkdir($path."/views",0777,true);
                @mkdir($path."/models",0777,true);
                @umask($oldmask);
            }

        }

        if(count($error)==0){
            // Генерируем код модуля
            $content = $this->render('generators/template_module',array(
                'name'   => ucfirst($name),
            ),true);

            // Генерируем код контроллера по умолчанию.
            $content2 = $this->render('generators/template_controller',array(
                'extend' => 'Controller',
                'name'   => 'default_main',
                'layout' => '//main',
                'actions'=> array('index')
            ),true);


            $code_file  = new CodeFile($path . "/".strtolower($name) . ".php" , $content, $this->post('overwrite',false) );
            $code_file2 = new CodeFile($path . "/controllers/".strtolower($name) . ".php" , $content2, $this->post('overwrite',false) );

            if($code_file->save() && $code_file2->save() ){
                $success = true;
            }else{
                $error+= $code_file->getErrors();
                $error+= $code_file2->getErrors();
            }
        }

        print json_encode(array(
            'success' => $success,
            'errors'  => $error,
            'list'    => $list,
        ));


    }

}