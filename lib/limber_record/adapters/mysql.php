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

require_once dirname(__FILE__) . '/base.php';

class Mysql extends Base
{
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
		
		return $data;
	}
	
	protected function _insert($sql)
	{
		$this->_execute($sql);
		
		return @mysql_insert_id($this->id);
	}
	
	protected function _update($sql)
	{
		$this->_execute($sql);
		
		return @mysql_affected_rows($this->id);
	}
	
	protected function _create_table($table_name, $fields_description) {}
	protected function _drop_table($table_name) {}
	protected function _transaction_begin() {}
	protected function _transaction_commit() {}
	protected function _transaction_rollback() {}
	
	//improve performance for select cell
	public function select_cell($sql)
	{
		$query = $this->_execute($sql);
		
		return @mysql_result($query, 0, 0);
	}
}
