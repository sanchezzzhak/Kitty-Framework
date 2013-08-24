<?php

class module_controller extends Controller{
    public  $layout = '//main';

    // Форма
    public function actionIndex(){
        $this->render('module_form');
    }

    public function actionSave(){
        $this->layout = false;
    }

}