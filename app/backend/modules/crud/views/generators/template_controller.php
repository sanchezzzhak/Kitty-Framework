<?="<?php\n"; ?>

class <?=trim($name);?>_controller extends <?=$extend?>
{
    public  $layout = '<?=$layout?>';
    <? foreach($actions as $action): ?>

    public function action<?=ucfirst($action); ?>(){
        $this->render('<?=$action;?>');
    }

    <? endforeach; ?>
}