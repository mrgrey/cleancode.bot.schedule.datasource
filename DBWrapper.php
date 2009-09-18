<?php
/**
 * Класс реализует обертку для запросов к БД с использованием плэйсхолдеров
 *
 * @todo Избавиться от die! Класс, убивающий веб-приложение при невозможности соединиться с базой - это недопустимо в очень большом круге задач.
 * @author mrgrey
 */
class DBWrapper {
	/**
	* Подключение к серверу
	* 
	* @var resource
	*/
	static $link;
	/**
	* Счетчик произведенных запросов
	* 
	* @var mixed
	*/
	static $query_counter;

	/**
	* Конструктор класса
	* 
	* @param string $server адрес сервера (имя хоста/ip адрес)
	* @param string $username имя пользователя 
	* @param string $password пароль
	* @param string $db_name имя базы данных
	* @return DBWrapper
	*/
	public function  __construct($server, $username, $password, $db_name) {
		self::$link = mysql_connect($server, $username, $password);
		if (!self::$link) {
			//WTF????
			die('Could not connect to mysql server: ' . mysql_error());
		}
		if(! mysql_select_db($db_name, self::$link) ) {
			//WTF????
			die('Could not use db: ' . mysql_error());
		}

		mysql_query("SET NAMES 'utf8'");
		self::$query_counter++;
	}

	/**
	* Функция посылает запрос
	* 
	* @param string $query запрос, при необходимости содержащий плейсхолдеры
	* @param mixed $placeholders_data массив значений, подставлемых в запрос (едининичное значение)
	* @return resource
	*/
	public function send_query($query, $placeholders_data = array()) {
		if(!isset(self::$link)) {
			//WTF????
			die("Not connected!");
		}

		if(is_scalar($placeholders_data)) {
			$placeholders_data = array($placeholders_data);
		}

		$placeholders_count = substr_count($query, "??");
		if($placeholders_count != count($placeholders_data)) {
			return false;
		}

		for($i = 0; $i < count($placeholders_data); $i++) {
			if(!is_int($placeholders_data[$i])) {
				$placeholders_data[$i] = mysql_real_escape_string($placeholders_data[$i]);
			}
		}

		for($i = 0; $i < $placeholders_count; $i++) {
			$position = strpos($query, "??");
			$query = substr_replace($query, $placeholders_data[$i], $position, 2);
		}

		self::$query_counter++;
		return mysql_query($query);
	}

	/**
	* Выполняет QUERY запрос и возвращает полученные значения в соответствии с указанным типом возвращаемых данных
	* 
	* @param string $query запрос, при необходимости содержащий плейсхолдеры
	* @param mixed $placeholders_data массив значений, подставлемых в запрос (едининичное значение)
	* @param mixed $return_type тип возвращаемых данных, см mysql_fetch_array()
	*/
	public function get_data_array_from_db($query, $placeholders_data = array(), $return_type = MYSQL_ASSOC) {
		$query_result = $this->send_query($query, $placeholders_data);
		if($query_result === false) {
			return false;
		}

		$return_data = array();
		while($current_result = mysql_fetch_array($query_result, $return_type)) {
			$return_data[] = $current_result;
		}

		return $return_data;
	}

	public function get_query_counter() {
		return self::$query_counter;
	}
}
?>
