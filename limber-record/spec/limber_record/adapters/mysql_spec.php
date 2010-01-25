<?php

/*
 * Copyright 2009-2010 Limber Framework
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

require_once "limber_record/adapters/mysql.php";

describe("Mysql Adapter", function($spec) {
	$spec->before_all(function($data) {
		include __DIR__ . "/mysql_setup.php"; //setup database
	});
	
	$spec->it("should connect to database", function($spec) {
		$adapter = new Mysql();
		
		try {
			$adapter->connect("localhost", "root", "");
			$adapter->force_connection();
			$spec(true)->should->be(true);
		} catch(ConnectionException $e) {
			$spec(true)->should->be(false);
		}
	});
	
	$spec->it("should throw exception when trying to connect with invalid data", function($spec) {
		$adapter = new Mysql();
		
		try {
			$adapter->connect("localhost", "root", "wrong");
			$adapter->force_connection();
			$spec(true)->should->be(false);
		} catch(ConnectionException $e) {
			$spec(true)->should->be(true);
		}
	});
	
	$spec->context("doing queries", function($spec) {
		$spec->before_all(function($data) {
			$data->adapter = new Mysql();
			$data->adapter->connect("localhost", "root", "");
			$data->adapter->select_db("limber_record");
		});
		
		$spec->it("should return an array data for selections", function($spec, $data) {
			$cars = $data->adapter->select("SELECT name FROM `cars`");
			
			$spec($cars)->should->be(array(
				array("name" => "Ferrari"),
				array("name" => "Lamborguini"),
				array("name" => "BMW")
			));
		});
		
		$spec->it("should return a blank array when there are no results", function($spec, $data) {
			$cars = $data->adapter->select("SELECT * FROM `cars` WHERE `year` = 2020");
			
			$spec($cars)->should->be(array());
		});
		
		$spec->it("should get a cell result", function($spec, $data) {
			$name = $data->adapter->select_cell("SELECT `name` FROM `cars` WHERE `id` = 1");
			
			$spec($name)->should->be("Ferrari");
		});
		
		$spec->it("should throw exception when do an invalid query", function($spec, $data) {
			try {
				$data->adapter->select("SELECT * FROM `non_existing_table`");
				$spec(true)->should->be(false);
			} catch (QueryException $e) {
				$spec(true)->should->be(true);
			}
		});
	
		$spec->it("should insert a record at database and return the id", function($spec, $data) {
			$id = $data->adapter->insert("INSERT INTO `cars` (`name`, `year`, `color`) VALUES ('Gol', '2005', 'white')");
			
			$spec($id)->should->be('4');
		});
		
		$spec->it("should update records and return the number of affected rows", function($spec, $data) {
			$affected = $data->adapter->update("UPDATE `cars` SET `year` = '2010' WHERE `year` = '2009'");
			
			$spec($affected)->should->be(2);
		});
		
		$spec->it("should update returns the number of removed rows on a deletion query", function($spec, $data) {
			$removed = $data->adapter->update("DELETE FROM `cars` WHERE `year` = '2005'");
			
			$spec($removed)->should->be(1);
		});
		
		$spec->it("should use transactions to rollback", function($spec, $data) {
			$initial = $data->adapter->select_cell("SELECT count(*) FROM `cars`");
			
			$data->adapter->transaction_begin();
			$removed = $data->adapter->update("DELETE FROM `cars` WHERE `year` = '2010'");
			
			$spec($data->adapter->select_cell("SELECT count(*) FROM `cars`"))->should->be((string) ($initial - $removed));
			
			$data->adapter->transaction_rollback();
			
			$spec($data->adapter->select_cell("SELECT count(*) FROM `cars`"))->should->be($initial);
		});
		
		$spec->it("should quote strings", function($spec, $data) {
			$spec($data->adapter->quote_string('name\'s'))->should->be("'name\\'s'");
		});
		
		$spec->it("should quote column names", function($spec, $data) {
			$spec($data->adapter->quote_column_name('name'))->should->be('`name`');
		});
		
		$spec->it("should quote table names", function($spec, $data) {
			$spec($data->adapter->quote_table_name('mytable'))->should->be('`mytable`');
		});
		
		$spec->it("should quote table names with column", function($spec, $data) {
			$spec($data->adapter->quote_table_name('mytable.column'))->should->be('`mytable`.`column`');
		});
	});
	
	$spec->context("getting table scheme", function($spec) {
		$spec->before_all(function($data) {
			$data->adapter = new Mysql();
			$data->adapter->connect("localhost", "root", "");
			$data->adapter->select_db("limber_record");
		});
		
		$spec->it("should get the columns of table", function($spec, $data) {
			$spec($data->adapter->table_fields("cars"))->should->be(array("id", "name", "year", "color"));
		});
	});
});
