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

namespace LimberSupport;

require 'limber_support.php';

describe("ArrayObject class", function($spec) {
	$spec->context("constructing", function($spec) {
		$spec->it("should start empty", function($spec, $data) {
			$spec(count(new ArrayObject))->should->be(0);
		});
		
		$spec->it("should push any argument passed to constructor", function($spec, $data) {
			$spec(count(new ArrayObject(1, 2, 3)))->should->be(3);
		});
	});
	
	$spec->context("getting and setting internal array", function($spec) {
		$spec->it("should return the current array data", function($spec, $data) {
			$enum = new ArrayObject(1, 2, 3);
			
			$spec($enum->get_array())->should->be(array(1, 2, 3));
		});
		
		$spec->it("should accept an array as new internal data", function($spec, $data) {
			$enum = new ArrayObject(1, 2);
			
			$spec($enum->set_array(array(5, 4))->get_array())->should->be(array(5, 4));
		});
	});
	
	$spec->context("doing array access", function($spec) {
		$spec->it("should get an array index", function($spec, $data) {
			$enum = new ArrayObject(1, 2, 3);
			
			$spec($enum[1])->should->be(2);
		});
		
		$spec->it("should set an array index", function($spec, $data) {
			$enum = new ArrayObject(1, 2, 3);
			$enum[2] = 5;
			
			$spec($enum[2])->should->be(5);
		});
	});
	
	$spec->context("accessing first and last elements", function($spec) {
		$spec->it("should get the first element of array", function($spec, $data) {
			$enum = new ArrayObject(1, 2, 3);
			
			$spec($enum->first())->should->be(1);
		});
		
		$spec->it("should return null at first if array has no elements", function($spec, $data) {
			$enum = new ArrayObject();
			
			$spec($enum->first())->should->be(null);
		});
		$spec->it("should get the last element of array", function($spec, $data) {
			$enum = new ArrayObject(1, 2, 3);
			
			$spec($enum->last())->should->be(3);
		});
		
		$spec->it("should return null at last if array has no elements", function($spec, $data) {
			$enum = new ArrayObject();
			
			$spec($enum->last())->should->be(null);
		});
	});
	
	$spec->context("iterating over enumerable", function($spec) {
		$spec->it("should iterate over enumerable items", function($spec, $data) {
			$enum = new ArrayObject(1, 2);
			
			$sum = 0;
			
			foreach ($enum as $value) {
				$sum += $value;
			}
			
			$spec($sum)->should->be(3);
		});
	});
});
