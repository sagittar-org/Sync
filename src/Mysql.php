<?php
namespace pieni\Sync;

class Mysql implements Driver
{
	public function __construct($params = [])
	{
		$this->handler = $params['handler'];
		$this->database = $params['database'];
		$this->db = $params['db'];
	}

	public function mtime($name = '')
	{
		$mtime = $this->db->query("SELECT MAX(`UPDATE_TIME`) FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = '".$this->database_name($name)."'")->fetch_row()[0];
		return $mtime !== null ? strtotime($mtime) : -1;
	}

	public function get($name = '')
	{
		$columns = end($this->handler->drivers)->columns;
		foreach (array_keys($columns) as $table) {
			$data[$table] = [];
			$result = $this->db->query("SELECT `".(implode('`, `', array_merge(['id'], $columns[$table])))."` FROM `".$this->database_name($name)."`.`{$table}` ORDER BY `id` ASC");
			while (($row = $result->fetch_assoc()) !== null) {
				$id = array_shift($row);
				$data[$table][$id] = $row;
			}
		}
		return $data;
	}

	public function put($data, $mtime, $name = '')
	{
		$columns = end($this->handler->drivers)->columns;
		$this->db->query("CREATE DATABASE IF NOT EXISTS `".$this->database_name($name)."`");
		foreach (array_keys($columns) as $table) {
			$rows = $data[$table];
			$column_defs = '';
			foreach ($columns[$table] as $c => $column) {
				$column_defs .= ", `{$column}` text NOT NULL";
			}

			$this->db->query("CREATE TABLE IF NOT EXISTS `".$this->database_name($name)."`.`{$table}` (`row_index` int(11) NOT NULL UNIQUE AUTO_INCREMENT, `id` varchar(255) PRIMARY KEY{$column_defs});");
			$this->db->query("DELETE FROM `".$this->database_name($name)."`.`{$table}`");
			$this->db->query("ALTER TABLE `".$this->database_name($name)."`.`{$table}` AUTO_INCREMENT = 1");
			foreach ($rows as $id => $row) {
				$field_defs = "'{$id}'";
				foreach ($row as $c => $field) {
					$field = $this->db->real_escape_string($field);
					$field_defs .= ", '{$field}'";
				}
				$this->db->query("INSERT INTO `".$this->database_name($name)."`.`{$table}` (`".(implode('`, `', array_merge(['id'], $columns[$table])))."`) VALUES ({$field_defs});");
			}
		}
	}

	private function database_name($name)
	{
		return $name !== '' ? "{$this->database}_{$this->handler->name}_{$name}" : "{$this->database}_{$this->handler->name}";
	}
}
