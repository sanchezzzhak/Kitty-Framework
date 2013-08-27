<?php
namespace kitty\app;


class app
{
   private static $_app;

   /**
    * Иницилизация 
    * @param  $class   Называние приложения 
	* @param  $config  Настройки 
	**/
   public static function make($class, $config = array()){
		if(is_null(self::$_app)){
			self::$_app = new $class($config);
		}
		return self::$_app;
   }
	
	
	/**
	 * Вызов функций из класса приложения через Меджик класса
	 * @usage  app::getBasePath();
	 **/
	public static function __callStatic($method, array $arguments) {
		if(is_null(self::$_app)){
			throw new \kitty\base\ExceptionError('Этот метод можно вызвать только после инициализации приложения');	
		}
		if(!method_exists(self::$_app, $method))
			throw new \kitty\base\ExceptionError('Указанный метод {method} не найден в классе {class} ', array(
				'{method}' => $method,
				'{class}'  => get_class(self::$_app),
			));	
		return call_user_func_array(array( self::$_app , $method), $arguments);
	}
}
