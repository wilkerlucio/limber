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

require_once "limber_record.php";

describe("LimberRecord Base", function($spec) {
	$spec->before_all(function () {
		//setup database
		$db = mysql_connect("localhost", "root", "");
		mysql_query("DROP DATABASE IF EXISTS `limber_record`", $db);
		mysql_query("CREATE DATABASE `limber_record`", $db);
		mysql_select_db("limber_record", $db);

		//create tables and data
		mysql_query("CREATE TABLE `people` (
			id int(11) not null auto_increment,
			name varchar(255),
			email varchar(255),
			created_at datetime,
			updated_at datetime,
			primary key(id)
		) type=innodb");

		mysql_query("INSERT INTO people values (1, 'Wilker', 'wilkerlucio@provider.com', '2009-06-20 20:30:42', '2009-06-20 20:30:42')");
		mysql_query("INSERT INTO people values (2, 'Paul', 'paul@provider.com', '2009-06-20 20:42:30', '2009-06-20 20:42:30')");
		mysql_query("INSERT INTO people values (3, 'Mary', 'mary@provider.com', '2009-06-20 21:30:42', '2009-06-20 21:30:42')");

		//closes connection
		mysql_close($db);
		
		//database data
		$host = "localhost";
		$user = "root";
		$password = "";
		$database = "limber_record";

		//setup connection
		LimberRecord\Manager::instance()->connect("mysql", $host, $user, $password, $database);

		//require models
		require_dir(__DIR__ . "/models");
	});
	
	$spec->context("getting table name", function($spec) {
		$spec->it("should return the pluralized name of model by default", function($spec) {
			$spec(Person::table_name())->should->be("people");
			$spec(Item::table_name())->should->be("items");
		});
		
		$spec->it("should use a customized net if it's set", function($spec) {
			Item::table_name("collection");
			
			$spec(Item::table_name())->should->be("collection");
			$spec(Person::table_name())->should->be("people");
			
			Item::table_name(null); //restore table name
		});
	});
	
	$spec->context("getting table fields", function($spec) {
		$spec->it("should read table fields", function($spec, $data) {
			$spec(Person::table_fields())->should->be(array("id", "name", "email", "created_at", "updated_at"));
		});
	});
	
	$spec->context("getting primary key field of table", function($spec) {
		$spec->it("should return id by default", function($spec, $data) {
			$spec(Person::primary_key_field())->should->be("id");
		});
		
		$spec->it("should accept an argument to be the new primary key field", function($spec, $data) {
			Person::primary_key_field("person_id");
			
			$spec(Person::primary_key_field())->should->be("person_id");
		});
		
		$spec->it("should return to default value if null is given as argument", function($spec, $data) {
			Person::primary_key_field(null);
			
			$spec(Person::primary_key_field())->should->be("id");
		});
	});
	
	$spec->context("reading records from database", function($spec) {
		$spec->context("building conditions", function($spec) {
			$spec->it("should return raw string if it's raw", function($spec, $data) {
				$spec(LimberRecord\Base::build_conditions("some = 1"))->should->be("some = 1");
			});
			
			$spec->it("should use replacements for question marks and should quote values", function($spec, $data) {
				$conditions = array("some = ? and value != ? or some > ?", "value", true, 203);
				
				$spec(LimberRecord\Base::build_conditions($conditions))->should->be("some = 'value' and value != 1 or some > 203");
			});
			
			$spec->it("should use associative replacements to quote values", function($spec, $data) {
				$conditions = array("some = :some and value != :value or some > :some", array("some" => "Some", "value" => true));
				
				$spec(LimberRecord\Base::build_conditions($conditions))->should->be("some = 'Some' and value != 1 or some > 'Some'");
			});
			
			$spec->it("should should use associative arrays and quote columns and values", function($spec, $data) {
				$conditions = array("name" => "Some", "age >" => 30);
				
				$spec(LimberRecord\Base::build_conditions($conditions))->should->be("`name` = 'Some' AND `age` > 30");
			});
		});
		
		$spec->context("using find method", function($spec) {
			$spec->it("should read a record by id", function($spec, $data) {
				$person = Person::find(2);
			
				$spec($person->name)->should->be("Paul");
				$spec($person->email)->should->be("paul@provider.com");
			});
			
			$spec->it("should read all records from database", function($spec, $data) {
				$people = Person::find("all");
				
				$spec(@get_class($people))->should->be("LimberRecord\\Collection");
			});
			
			$spec->it("should retrieve the correct length of records", function($spec, $data) {
				$spec(Person::find("all")->count)->should->be(3);
			});
			
			$spec->it("should find the first item from database", function($spec, $data) {
				$person = Person::find("first");
				
				$spec($person->name)->should->be("Wilker");
			});
			
			$spec->it("should find the last item from database", function($spec, $data) {
				$person = Person::find("last");
				
				$spec($person->name)->should->be("Mary");
			});
		});
		
		$spec->context("using helper methods", function($spec) {
			$spec->it("should return all items");
			$spec->it("should return the first item from database");
			$spec->it("should return the last item from database");
		});
	});
});
