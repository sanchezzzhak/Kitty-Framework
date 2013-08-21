<?php

if (!defined('doc_root')) exit('No direct script access allowed');
 
/**
 * Работа с массивом как с контейнером
 * 
 * В планах добавить события на изменения массива
**/ 

class arrData implements ArrayAccess, Iterator, Countable
{
	
	protected 
		$data , 
		$current = 0;
		
	/**
	 * Импорт данных в контейнер через конструктор
	 * @param array $data массив
	 * @return $this
	 **/
	public function __construct($data = array(){
        $this->import($data);
    }
	

	/**
	 * Импорт данных в контейнер
	 * @param array $data массив
	 **/
	public function import(Array $data){
        $this->data = $data;
    }
	
	/**
	 * Экспорт содержимого из контейнера в виде массива
	 * @return array
	 **/
	public function export(){
        return $this->data;
    }
	
	/**
     * Очищает контейнер
     */
    public function clear(){
        $this->data = array();
    }

	/**
     * Возвращает количество элементов в контейнере
     * @return integer
     */
    public function count(){
        return count($this->data);
    }
	
	/**
	 * Возвращаем текущий элемент массива 
	 **/
	public function current(){
        return current($this->data);
    }
	
	/**
     * Устанавливает значение с ключом $offset с помощью операторов для массивов
		*
     * @param string|integer $offset
     * @param mixed $value
     * @see ArrayAccess::offsetSet()
     * @see set()
     */
    public function offsetSet($offset, $value){
        return $this->set($offset, $value);
    }
	
	/**
	 * Алиас функции set
	 * @see  set();
	 **/
	public function __set($key,$value){
		$this->set($key,$value);	
	}
	
	/**
     * Устанавливает значения 
     * @param string|integer $key ключ для доступа к значению
     * @param mixed $value значение
     */
    public function set($key, $value){
		arr::set($this->data,$key,$value);
	}
	
	/**
     * Возвращает значение с ключом $offset с помощью операторов для массивов
     * @param string|integer $offset
     * @return mixed
     * @see ArrayAccess::offsetGet()
     * @see get()
     */
    public function offsetGet($offset){
        return $this->get($offset);
    }
	
	/**
	 * Алиас функции get
	 * @see get()
	 **/
	public function __get($key){
		return $this->get($key);	
	}
	
	/**
     * Получить значение по ключу
     * @param string|integer $key ключ для доступа к значению
     * @param mixed $default значение по умолчанию
	 * @return mixed value
     */
	public function get($key ,$default = null){
		return arr::get($this->data,$key,$default);
	}
	
	/**
	 * Возвращаем текущий ключ массива
	 **/
    public function key(){
        return key($this->data);
    }
	
	/**
	 * Передвигает внутренний указатель массива на одну позицию вперёд
	 **/
    public function next(){
        $this->current++;
        return next($this->data);
    }
	
	/**
	 * Сброс указателя на начало
	 **/
    public function rewind(){
        $this->current = 0;
        return reset($this->data);
    }
	
	/**
	 * Первый элемент массива
	 **/
    public function first(){
        return $this->rewind();
    }
	
	/**
	 * Последний элемент массива
	**/
    public function last(){
        end($this->data);
        return $this->current();
    }
	
	/**
	 * Получить ID
	 **/
    public function valid(){
        return $this->current < sizeof($this->data);
    }
	
	/**
     * Удаляет значение с ключом $offset с помощью операторов для массивов
		*
     * @param string|integer $offset
     * @return boolean
     * @see ArrayAccess::offsetUnset()
     * @see delete()
     */
    public function offsetUnset($offset){
        return $this->delete($offset);
    }
	
	/**
     * Удаляет значение с ключом $key
		*
     * @param string|integer $key ключ
     * @return true
     */
    public function delete($key){
        unset($this->data[$key]);
        return true;
    }
	/**
	 * Алиас функции delete
	 * @see delete
	 **/
	public function __unset($name){
       return $this->delete($name);
    }
	
	/**
     * Проверяет существует ли значение с ключом $offset
     * с помощью операторов для массивов
     * @param string|integer $offset
     * @return boolean
     * @see ArrayAccess::offsetExists()
     * @see exists()
     */
    public function offsetExists($offset){
        return $this->exists($offset);
    }
	
	/**
     * Проверяет существует ли значение с ключом $key
     * @param string|integer $key ключ
     * @return boolean
     */
    public function exists($key){
        if (!is_scalar($key)) {
            throw new СException("Key is not scalar", $key);
        }
        return isset($this->data[$key]);
    }
	/**
	 * Алиас функции exists
	 * @param string|integer $key ключ
     * @return boolean
	 **/
	public function has($key){
        return $this->exists($key);
    }
	
	public function __isset($key){
        return $this->exists($key);
    }
	
	
}
