<?php

/*
 * Copyright 2009 Limber Framework
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License. 
 */

namespace LimberRecord\Adapters;

require_once 'limber_record/adapters/base.php';

class Mysql extends Base
{
	private static $NATIVE_TYPES = array(
		"primary_key" => "int(11) DEFAULT NULL auto_increment PRIMARY KEY",
		"string"      => array("name" => "varchar", "limit" => 255),
		"text"        => array("name" => "text"),
		"integer"     => array("name" => "int", "limit" => 11),
		"float"       => array("name" => "float"),
		"decimal"     => array("name" => "decimal"),
		"datetime"    => array("name" => "datetime"),
		"timestamp"   => array("name" => "datetime"),
		"time"        => array("name" => "time"),
		"date"        => array("name" => "date"),
		"binary"      => array("name" => "blob"),
		"boolean"     => array("name" => "tinyint", "limit" => 1)
	);
	
	private $id;
	
	//connection methods
	protected function _connect($host, $user, $password)
	{
		$this->id = @mysql_connect($host, $user, $password);
		
		return !!$this->id;
	}
	
	protected function _select_db($database)
	{
		return @mysql_select_db($database, $this->id);
	}
	
	protected function _close()
	{
		return @mysql_close($this->id);
	}
	
	//common methods
	protected function _execute($sql)
	{
		$result = @mysql_query($sql, $this->id);
		
		if (!$result) {
			throw new QueryException("Error: " . mysql_error($this->id) . " while executing query $sql", $sql);
		}
		
		return $result;
	}
	
	protected function _select($sql)
	{
		$query = $this->_execute($sql);
		$data = array();
		
		while ($row = @mysql_fetch_assoc($query)) {
			$data[] = $row;
		}
		
		@mysql_free_result($query);
		
		return $data;
	}
	
	protected function _insert($sql)
	{
		$this->_execute($sql);
		
		return (string) @mysql_insert_id($this->id);
	}
	
	protected function _update($sql)
	{
		$this->_execute($sql);
		
		return @mysql_affected_rows($this->id);
	}
	
	protected function _table_fields($table_name)
	{
		$table_name = $this->quote_table_name($table_name);
		$data = $this->select("SHOW FIELDS FROM " . $table_name);
		
		return array_map(function($row) {
			return $row["Field"];
		}, $data);
	}
	
	protected function _transaction_begin()
	{
		$this->_execute("BEGIN");
	}
	
	protected function _transaction_commit()
	{
		$this->_execute("COMMIT");
	}
	
	protected function _transaction_rollback()
	{
		$this->_execute("ROLLBACK");
	}
	
	//improve performance for select cell
	public function select_cell($sql)
	{
		$query = $this->_execute($sql);
		
		$result = @mysql_result($query, 0, 0);
		@mysql_free_result($query);
		
		return $result;
	}
	
	//quoting
	public function quote_string($string)
	{
		return "'" . mysql_real_escape_string($string, $this->id) . "'";
	}
	
	public function quote_column_name($column_name)
	{
		return "`$column_name`";
	}
	
	public function quote_table_name($table_name)
	{
		return str_replace(".", "`.`", $this->quote_column_name($table_name));
	}
}
