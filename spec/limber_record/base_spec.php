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
			
			Item::table_name(null);
		});
	});
});
