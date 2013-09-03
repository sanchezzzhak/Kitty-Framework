<?php
namespace kitty\app;
use \kitty\base\ExceptionError;

class app
{
   private static $_app;

   /**
    * Иницилизация
    * @param  $class  Класс который будет сердцем нашего App
	* @param  $config настройки
    * @return  mixed
	**/
   public static function make( $class ,$config = array()){
		if(is_null(self::$_app)){
			self::$_app = new $class($config);
		}
		return self::$_app;
   }


	/**
	 * Вызов функций из класса приложения через Меджик класса
	 * @usage  app::getBasePath();
     * @param  $method  Названия метода который нужно вызывать
     * @param  $arguments аргументы которые нужно передать методу.
     * @return  mixed
	 **/
	public static function __callStatic($method, array $arguments) {
		if(is_null(self::$_app)){
			throw new ExceptionError('Этот метод {method} можно вызвать, только после инициализации приложения', array(
                '{method}' => $method,
            ));
		}
		if(!method_exists(self::$_app, $method))
			throw new ExceptionError('Указанный метод {method} не найден в классе {class} ', array(
				'{method}' => $method,
				'{class}'  => get_class(self::$_app),
			));	
		return call_user_func_array(array( self::$_app , $method), $arguments);
	}

}
