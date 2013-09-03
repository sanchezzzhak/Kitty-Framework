<?
namespace app\modules\crud\controllers;


class default_main_controller extends \kitty\app\controller {

    public $layout = 'main';

    public function before(){}

    public function actionIndex(){
        $this->render('index');
    }

    public function after(){}

}
	
	
	

	
	
	
	
?>