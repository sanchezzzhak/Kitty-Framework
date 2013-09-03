<?="<?\n"; ?>
namespace <?=$namespace;?>
use /kitty/web/arr;

/**
*
* @see Прификс _controller Обезателен в каждом классе контролера
*/

class <?=trim($name);?>_controller extends <?=$extend?>
{
    public  $layout = '<?=$layout?>';
    <? foreach($actions as $action): ?>

    public function action<?=ucfirst($action); ?>(){
        // $this->render('<?=$action;?>');
    }

    <? endforeach; ?>

}