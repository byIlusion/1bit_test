<?php

/**
*	Класс для работы с БД
*/
class DB extends mysqli
{

	private $host;
	private $port;
	private $user;
	private $pass;
	private $db;
	private $engine;

	private $link;
	
	function __construct($conf) {
    $this->host = isset($conf->host) ? $conf->host : 'localhost';
    $this->port = isset($conf->port) ? $conf->port : '3306';
    $this->user = isset($conf->user) ? $conf->user : '';
    $this->pass = isset($conf->pass) ? $conf->pass : '';
    $this->db = isset($conf->db) ? $conf->db : 'db';
    $this->engine = isset($conf->engine) && $conf->engine ? $conf->engine : NULL;
    
		$this->link = parent::__construct($this->host, $this->user, $this->pass, $this->db, $this->port);

		if (mysqli_connect_error()) {
			die('Ошибка подключения (' . mysqli_connect_errno() . ') '
				. mysqli_connect_error());
		}
    
    if (!$this->set_charset("utf8")) {
        dpm("Ошибка при загрузке набора символов utf8: %s\n", $this->error);
        exit();
    }
	}

	// Собрать в массив в определенном порядке
	private function fetchArray(&$query, $order = 'ASC') {
		$result = array();
		if ($query) {
			while ($res = $query->fetch_array()) {
				if (strtoupper($order) == 'DESC') {
					array_unshift($result, $res);
				} else {
					array_push($result, $res);
				}
			}
		} else {
			return array();
		}
		$query->close();
		return $result;
	}

  // Вернуть имя БД
	public function getDBName() {
		return $this->db;
	}
  
  // QUERY
  public function db_query(string $sql, $order = 'ASC') {
    $result = $this->query($sql);
  	return $this->fetchArray($result, $order);
  }
  
  // INSERT
  public function db_insert(string $table, array $fields) {
    foreach ($fields as &$value)
      if (is_string($value)) $value = "'$value'";
    $sql = "INSERT INTO `$table`";
		$sql .= " (`" . implode("`,`", array_keys($fields)) . "`)";
		$sql .= " VALUES(" . implode(",", $fields) . ")";
    if ($this->query($sql) === TRUE) {
			return TRUE;
		} else {
			return "Error: " . $sql . "<br>" . $this->error;
		}
  }
  
  // UPDATE
  public function db_update(string $table, array $fields, array $conditions) {
    $vals = array();
    $conds = array();
    foreach ($fields as $key => $value)
      $vals[] = "`$key` = " . (is_string($value) ? "'$value'" : $value);
    foreach ($conditions as $key => $value)
      $conds[] = "`$key` = " . (is_string($value) ? "'$value'" : $value);
    $sql = "UPDATE `$table`";
		$sql .= " SET " . implode(", ", $vals);
		$sql .= " WHERE " . implode(" AND ", $conds);
    if ($this->query($sql) === TRUE) {
			return TRUE;
		} else {
			return "Error: " . $sql . "<br>" . $this->error;
		}
  }
  
  // DELETE
  public function db_delete(string $table, array $conditions) {
    $conds = array();
    foreach ($conditions as $key => $value)
      $conds[] = "`$key` = " . (is_string($value) ? "'$value'" : $value);
    $sql = "DELETE FROM `$table`";
		$sql .= " WHERE " . implode(" AND ", $conds);
    if ($this->query($sql) === TRUE) {
			return TRUE;
		} else {
			return "Error: " . $sql . "<br>" . $this->error;
		}
  }
  
  
  // Проверка на наличие таблицы в БД
  public function checkTableInDB($table) {
    if ($table) {
      // Проверяем есть ли такая таблица в БД
      $sql = "SHOW TABLES FROM `$this->db` LIKE '$table'";
  		$result = $this->query($sql);
  		$result = $this->fetchArray($result);
      return count($result) && $result[0][0] == $table ? TRUE : FALSE;
    }
    return FALSE;
  }
  
  // Создание таблицы из схемы
  public function createTableFromSchema($table_name, $schema) {
    // Смотрим что в схеме есть что-то и есть поля
    if ($schema && isset($schema['fields']) && count($schema['fields'])) {
      // Строим поля
      $fields = array();
      foreach ($schema['fields'] as $key => $field) {
        if (!isset($field['type'])) {
          return FALSE;
        }
        $field_str = "`" . $key . "` " . mb_strtoupper($field['type']) . (isset($field['length']) ? "(" . $field['length'] . ")" : "");
        if (isset($field['unsigned']) && $field['unsigned'])
          $field_str .= " UNSIGNED";
        if (isset($field['not null']) && $field['not null'])
          $field_str .= " NOT NULL";
        if (isset($field['auto_increment']) && $field['auto_increment'])
          $field_str .= " AUTO_INCREMENT";
        if (isset($field['default']))
          $field_str .= " DEFAULT " . (is_string($field['default']) ? "'" . $field['default'] . "'" : $field['default']);
        if (isset($field['description']) && $field['description'])
          $field_str .= " COMMENT '" . $field['description'] . "'";
        $fields[] = $field_str;
      }
      if (isset($schema['primary key']))
        $fields[] = "PRIMARY KEY (`" . (is_array($schema['primary key']) ? implode("`, `", $schema['primary key']) : $schema['primary key']) . "`)";
      if (isset($schema['indexes']) && count($schema['indexes'])) {
        foreach ($schema['indexes'] as $key => $index) {
          $fields[] = "INDEX `$key` (`" . (is_array($index) ? implode("` ASC, `", $index) : $index) . "` ASC)";
        }
      }
      
      // Основной запрос
      $sql = "CREATE TABLE `$this->db`.`$table_name`(" . PHP_EOL . implode("," . PHP_EOL, $fields) . ")";
      if (isset($schema['description'])) {
        $sql .= PHP_EOL . "COMMENT = '" . $schema['description'] . "'" . ($this->engine ? "," : "");
      }
      
      if ($this->engine)
        $sql .= " engine=$this->engine";
//      dpm($sql);
      if ($this->query($sql)) {
        dpm("Таблица $table_name создана");
      }
      else {
        dpm("Таблица $table_name НЕ СОЗДАНА! (" . mysqli_errno($this) . ") " . mysqli_error($this));
        return FALSE;
      }
    }
    else {
      return FALSE;
    }
    return TRUE;
  }
}

?>