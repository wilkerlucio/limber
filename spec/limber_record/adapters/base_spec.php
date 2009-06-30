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

require_once __DIR__ . "/../../../lib/limber_record/adapters/base.php";

describe("Base adapter", function($spec) {
	$spec->it("should throw exception if try to connect with invalid data", function($spec) {
		try {
			$adapter = new MockAdapter();
			$adapter->connect("localhost", "root", "wrong");
			$adapter->force_connection();
			$spec(true)->should->be(false);
		} catch (ConnectionException $ex) {
			$spec(true)->should->be(true);
		}
	});
	
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
	
	$spec->context("selecting records", function($spec) {
		$spec->before_all(function($data) {
			$data->adapter = new MockAdapter();
			$data->adapter->connect("localhost", "root", "password");
		});
		
		$spec->it("should return the full array into select method", function($spec, $data) {
			$spec(count($data->adapter->select("")))->should->be(3);
		});
		
		$spec->it("should return only first row with select row method", function($spec, $data) {
			$spec($data->adapter->select_row(""))->should->be(array("name" => "John", "email" => "john@limbercode.com"));
		});
		
		$spec->it("should return only first cell with select cell method", function($spec, $data) {
			$spec($data->adapter->select_cell(""))->should->be("John");
		});
	});
	
	$spec->context("quoting values", function($spec) {
		$spec->before_all(function($data) {
			$data->adapter = new MockAdapter();
		});
		
		$spec->it("should quote null values", function($spec, $data) {
			$spec($data->adapter->quote(null))->should->be("NULL");
		});
		
		$spec->it("should quote true values", function($spec, $data) {
			$spec($data->adapter->quote(true))->should->be("1");
		});
		
		$spec->it("should quote false values", function($spec, $data) {
			$spec($data->adapter->quote(false))->should->be("0");
		});
		
		$spec->it("should quote integer values", function($spec, $data) {
			$spec($data->adapter->quote(20))->should->be("20");
		});
		
		$spec->it("should quote double values", function($spec, $data) {
			$spec($data->adapter->quote(10.5))->should->be("10.5");
		});
		
		$spec->it("should quote string values", function($spec, $data) {
			$spec($data->adapter->quote("some string"))->should->be("'some string'");
		});
		
		$spec->it("should quote string values replacing injection characteres", function($spec, $data) {
			$spec($data->adapter->quote("some ' OR 1 = 1"))->should->be("'some \\' OR 1 = 1'");
		});
		
		$spec->it("should quote array values", function($spec, $data) {
			$spec($data->adapter->quote(array("multi", 5, null, "items")))->should->be("'multi',5,NULL,'items'");
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
		if ($host == "localhost" && $user == "root" && $password == "password") {
			$this->is_connected = true;
			
			return true;
		} else {
			return false;
		}
	}
	
	protected function _select_db($database)
	{
		$this->db_selected = true;
		
		return true;
	}
	
	protected function _close() {}
	protected function _create_table($table_name, $fields_description) {}
	protected function _drop_table($table_name) {}
	protected function _describe_table($table_name) {}
	protected function _execute($sql) {}
	protected function _select($sql)
	{
		return array(
			array("name" => "John", "email" => "john@limbercode.com"),
			array("name" => "Mary", "email" => "mary@limbercode.com"),
			array("name" => "Ana",  "email" => "ana@limbercode.com"),
		);
	}
	protected function _insert($sql) {}
	protected function _update($sql) {}
	protected function _transaction_begin() {}
	protected function _transaction_commit() {}
	protected function _transaction_rollback() {}
}
