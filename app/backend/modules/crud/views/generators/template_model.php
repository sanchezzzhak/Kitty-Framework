<?="<?php\n"; ?>

use \kitty\web\arr;

class <?=trim($name);?> extends \kitty\db\model
{

    public $_table = '<?=$table?>';  // Таблица
    public $_pk = '<?=$id?>';        // Primary Key
    public $_db = 'default';         // Конфигурация БД

    public $_attr = array(
    <?foreach($attrArr as $key => $attr):?>
        '<?=$key?>'  =>  array( 'name' => <?=$attr['name']?> ),
    <?endforeach;?>
    );

    static public function model(){
        return parent::make(__CLASS__);
    }

    /**
    * В этом методе описываются связи моделей в виде массива.
    * [Название связи [ Тип связи, Модель, как происходит связь HAS_ONE, HAS_MANY]]
    **/
    public function relation(){
        return array(

        );
    }

    /**
     * В этом методе описываются правила валидации модели
     * Первый параметр список полей через запятую, второй параметр тип фильтра
     * Не обязательный параметр message это кастомное сообщение ошибки.
     *   :attr имя текущего атрибута
     * Ниже представлены примеры фильтров.

    Обязательные поля + проверка что значение не пустое
     * array('список полей', 'required' , 'message'=> 'Свое сообщение :attr ' ),
    Фильтер по длине
     * array('список полей', 'length', 'min'=>0, 'max'=> 255),
    Фильтер по регулярке
     * array('список полей', 'match' , 'pattern'),
    Проверка на тип
     * array('список полей', 'type', 'is' => 'int|integer|string|float|array|boolean|bool'  ),
    Проверка на email
     * array('список полей', 'email'),
    Проверяет наличия значения в массиве
     * array('список полей', 'in' , 'arr' => array() ),
     **/
    public function rules(){
        return array(

        );
    }

}
