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

require_once dirname(__FILE__) . "/../../../lib/limber_record/adapters/base.php";

describe("Base adapter", function($spec) {
	$spec->context("delaying connection", function($spec) {
		$spec->before_each(function($data) {
			$data->adapter = new MockAdapter();
			$data->adapter->connect("localhost", "root", "password");
		});
		
		$spec->it("should not connect if no query given", function($spec, $data) {
			$spec($data->adapter->is_connected)->should->be(false);
		});
		
		$spec->it("should connect when given a query", function($spec, $data) {
			$data->adapter->select("some query");
			
			$spec($data->adapter->is_connected)->should->be(true);
		});
		
		$spec->it("should select a database if was set", function($spec, $data) {
			$data->adapter->select_db("database");
			$data->adapter->select("some query");
			
			$spec($data->adapter->db_selected)->should->be(true);
		});
		
		$spec->it("should not select a database if was not set", function($spec, $data) {
			$data->adapter->select("some query");
			
			$spec($data->adapter->db_selected)->should->be(false);
		});
	});
});

class MockAdapter extends Base
{
	public $is_connected;
	public $db_selected;
	
	public function __construct()
	{
		$this->is_connected = false;
		$this->db_selected = false;
	}
	
	protected function _connect($host, $user, $password)
	{
		$this->is_connected = true;
		
		return true;
	}
	
	protected function _select_db($database)
	{
		$this->db_selected = true;
		
		return true;
	}
	
	protected function _close() {}
	protected function _create_table($table_name, $fields_description) {}
	protected function _drop_table($table_name) {}
	protected function _execute($sql) {}
	protected function _select($sql) {}
	protected function _insert($sql) {}
	protected function _update($sql) {}
	protected function _transaction_begin() {}
	protected function _transaction_commit() {}
	protected function _transaction_rollback() {}
}
