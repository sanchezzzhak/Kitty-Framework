<?


Class CodeFile {

    public
         $path,     // Путь
         $errors,   // Массив ошибок
         $content;  // Содержимое файла


    /*
     *
     **/
    public function __construct($path, $content = null , $overwrite = false){
        $this->path    = strtr($path,array('/'=>DIRECTORY_SEPARATOR,'\\'=>DIRECTORY_SEPARATOR));
        $this->content = $content;

        if(is_file($path)){
            if(!$overwrite) $this->errors[] = 'Файл уже существует';
        }
    }

    /**
     * Сохранить файл
     * @return bool
     **/
    public function save(){
        if( count($this->errors)>0 ) return false;

        if(@file_put_contents($this->path,$this->content)===false){
            $this->errors[]="Невозможно записать файл '{$this->path}'.";
            return false;
        }

        return true;
    }

    /**
     * Получить список ошибок
     * @return array
     **/
    public function getErrors(){
        return $this->errors;
    }

}