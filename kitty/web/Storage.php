<?php
namespace kitty\web;

/**
 * Класс хранилища файлов
 *
 * @Using:
 *
 * $file = '/tmp/abcde.jpg';
 * $newfile = Storage::make('photo')->move_file($file, 'photo', 'jpg');
 */

class Storage {

	protected static $instances = array();

	protected $config;

	/**
	 * Возвращает объект Storage
	 *
	 * @static
	 * @param null $type
	 * @return Storage
	 */
	public static function make($type = null)
	{
		if ($type === null){
			$type = 'default';
		}

		if (!isset(Storage::$instances[$type])){
			$storage = new Storage;
			$storage->config = config::get('storage');

			Storage::$instances[$type] = $storage;
		}

		return Storage::$instances[$type];
	}

	public function __construct(){}

	// Создать уникальный путь к файлу
	private function unique_filepath($base_path, $nesting_level, $ext){
		$filename = md5(uniqid()).'.'.trim($ext,'.');

		// Подготовить путь к файлу
		$filedir = $base_path;

		for ($i = 0; $i < $nesting_level; $i++){
			if (!file_exists($filedir)){
				throw new ExceptionError('Directory not exists: :filedir', array(':filedir' => $filedir));
			}

			if (!is_dir($filedir)){
				throw new ExceptionError('Not directory: :filedir', array(':filedir' => $filedir));
			}

			if (!is_writable($filedir)){
				throw new ExceptionError('Not writable directory: :filedir', array(':filedir' => $filedir));
			}

			$filedir .= substr($filename, $i * 2, 2).'/';

			@mkdir($filedir);
		}

		$filepath = $filedir.$filename;

		// Проверить существование файла
		if (file_exists($filepath)){
			$filepath = $this->unique_filepath($base_path, $nesting_level, $ext);
		}

		return $filepath;
	}


	// Переместить файл в хранилище
	public function move_file($path, $type, $ext){
		$type = isset($this->config['types'][$type]) ? $type : 'file';
		$ext = $ext ? $ext : 'file';

		// Проверить существование файла
		if (!$path OR !@is_file($path)){
			return false; // file not found
		}

		// Получить данные из конфига
		$storage_base = $this->config['storage_base'].DIRECTORY_SEPARATOR
			.$this->config['types'][$type]['files_dir'].DIRECTORY_SEPARATOR;

		$nesting_level = $this->config['types'][$type]['nesting_level'];

		// Создать уникальный путь к файлу
		$storage_filepath = $this->unique_filepath($storage_base, $nesting_level, $ext);

		// Скопировать файл
		@copy($path, $storage_filepath);
		@chmod($storage_filepath, 0666);

		// Удалить старый файл
		@unlink($path);

		return $storage_filepath;
	}

	// Создать пустой файл с уникальным именем в хранилище
	public function create_empty_file($type, $ext){
//		$type = Arr::get($this->config['types'], $type) ? $type : 'file';
		$type = isset($this->config['types'][$type]) ? $type : 'file';
		$ext = $ext ? $ext : 'file';

		// Получить данные из конфига
		$storage_base = $this->config['storage_base'].DIRECTORY_SEPARATOR
			.$this->config['types'][$type]['files_dir'].DIRECTORY_SEPARATOR;

		$nesting_level = $this->config['types'][$type]['nesting_level'];

		// Создать уникальный путь к файлу
		$storage_filepath = $this->unique_filepath($storage_base, $nesting_level, $ext);

		// Создать пустой файл
		$f = fopen($storage_filepath, "w");
		fclose($f);
		@chmod($storage_filepath, 0666);

		return $storage_filepath;
	}

	// Получить относительный путь из абсолютного
	public function get_relative_path($path){
		return str_replace($this->config['storage_base'], '', $path);
	}

	public function get_absolute_path($relative_path){
		return $this->config['storage_base'].$relative_path;
	}
}
