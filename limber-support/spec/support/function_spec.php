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

describe("Function helpers", function($spec) {
	$spec->context("testing if a var is a closure", function($spec) {
		$spec->it("should return true if it's a closure", function($spec, $data) {
			$spec(is_closure(function() {}))->should->be(true);
		});
		
		$spec->it("should return false if isn't a closure", function($spec, $data) {
			$spec(is_closure("array_map"))->should->be(false);
		});
		
		$spec->it("should return true if it's a valid function and pass true as second argument", function($spec, $data) {
			$spec(is_closure("array_map", true))->should->be(true);
		});
	});
});