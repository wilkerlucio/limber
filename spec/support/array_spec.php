<?php

/*
 * Copyright 2009 Limber Framework
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *	   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License. 
 */

require_once "limber_support.php";

class A
{
	public $n;
	
	public function __construct($n)
	{
		$this->n = $n;
	}
	
	public function multiply($x = 2)
	{
		return $this->n * $x;
	}
	
	public function __invoke()
	{
		return $this->multiply(5);
	}
}

class B {}

describe("Array Support", function($spec) {
	$spec->context("grouping array data with group_by", function($spec) {
		$spec->it("should group data by a function criteria", function($spec) {
			$strings = array("apple", "bee", "money", "hii", "banana", "monkey", "bear");
		
			$grouped = array_group_by($strings, function($string) { return strlen($string); });
		
			$spec($grouped[3])->should->include(array("bee", "hii"));
			$spec($grouped[4])->should->include("bear");
			$spec($grouped[5])->should->include(array("apple", "money"));
			$spec($grouped[6])->should->include(array("banana", "monkey"));
		});
	});
	
	$spec->context("invoking items at array", function($spec) {
		$spec->before_each(function($data) {
			$data->items = array(new A(1), new A(2), new A(3));
		});
		
		$spec->it("should get values return by invoked method", function($spec, $data) {
			$invoked = array_invoke($data->items, "multiply");
			
			$spec($invoked)->should->be(array(2, 4, 6));
		});
		
		$spec->it("should accept arguments into invoked method", function($spec, $data) {
			$invoked = array_invoke($data->items, "multiply", 3);
			
			$spec($invoked)->should->be(array(3, 6, 9));
		});
		
		$spec->it("should invoke de objects itself if not method given", function($spec, $data) {
			$invoked = array_invoke($data->items);
			
			$spec($invoked)->should->be(array(5, 10, 15));
		});
	});
	
	$spec->context("plucking attributes of objects into array", function($spec) {
		$spec->it("should read the attribute of each element", function($spec, $data) {
			$items = array(new A(2), new A(3), new A(4));
			$attributes = array_pluck($items, "n");
			
			$spec($attributes)->should->be(array(2, 3, 4));
		});
		
		$spec->it("should return null if the attribute is not available", function($spec, $data) {
			$items = array(new A(2), null, "", 3, new A(3), new B());
			$attributes = array_pluck($items, "n");
			
			$spec($attributes)->should->be(array(2, null, null, null, 3, null));
		});
	});
	
	$spec->context("appending items at end of array", function($spec) {
		$spec->it("should append all items of one array into another", function($spec, $data) {
			$data = array("a", 1, "c");
			array_append($data, array("e", 2));
			
			$spec($data)->should->be(array("a", 1, "c", "e", 2));
		});
	});
	
	$spec->context("injecting data", function($spec) {
		$spec->it("injecting with numbers", function($spec) {
			$result = array_inject(array(2, 3, 4), 0, function($acc, $current) {
				return $acc + $current;
			});
			
			$spec($result)->should->be(9);
		});
		
		$spec->it("injecting with arrays", function($spec) {
			$result = array_inject(array(2, 3, 4), array(), function($acc, $current) {
				return array_append($acc, array($current, $current));
			});
			
			$spec($result)->should->be(array(2, 2, 3, 3, 4, 4));
		});
	});
	
	$spec->context("flattening strings", function($spec) {
		$spec->it("should do nothing into flat arrays", function($spec) {
			$spec(array_flatten(array("some", "data")))->should->be(array("some", "data"));
		});
		
		$spec->it("should flatten nested arrays", function($spec) {
			$data = array(
				"some",
				array(
					"nested", "data"
				),
				"into",
				array(
					"many",
					array(
						"deept",
						array("levels")
					)
				)
			);
			
			$spec(array_flatten($data))->should->be(array("some", "nested", "data", "into", "many", "deept", "levels"));
		});
	});
});
