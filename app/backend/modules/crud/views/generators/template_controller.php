<?="<?php\n"; ?>

use /kitty/web/arr;

class <?=trim($name);?>_controller extends <?=$extend?>
{
    public  $layout = '<?=$layout?>';
    <? foreach($actions as $action): ?>

    public function action<?=ucfirst($action); ?>(){
        // $this->render('<?=$action;?>');
    }

    <? endforeach; ?>

}