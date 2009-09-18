<?php
/**
 * Класс реализует обертку для запросов к БД с использованием плэйсхолдеров
 *
 * @author mrgrey
 */
class DBWrapper {
	static $link;
	static $query_counter;

	public function  __construct($server, $username, $password, $db_name) {
		self::$link = mysql_connect($server, $username, $password);
		if (!self::$link) {
			die('Could not connect to mysql server: ' . mysql_error());
		}
		if(! mysql_select_db($db_name, self::$link) ) {
			die('Could not use db: ' . mysql_error());
		}

		mysql_query("SET NAMES 'utf8'");
		self::$query_counter++;
	}

	public function send_query($query, $placeholders_data = array()) {
		if(!isset(self::$link)) {
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
