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

//require models
require_dir(__DIR__ . "/models");

describe("LimberRecord Base", function($spec) {
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
});
