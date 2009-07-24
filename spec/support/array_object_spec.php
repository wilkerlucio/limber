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

class ArrayObjectExtendedTest extends ArrayObject
{
	public $n;
	
	public function __construct()
	{
		$n = 10;
	}
}

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
		$spec->before_each(function($data) {
			$data->enum = new ArrayObject(1, 2, 3);
		});
		
		$spec->it("should get an array index", function($spec, $data) {
			$spec($data->enum[1])->should->be(2);
		});
		
		$spec->it("should set an array index", function($spec, $data) {
			$data->enum[2] = 5;
			
			$spec($data->enum[2])->should->be(5);
		});
		
		$spec->it("should should return null when the index doesnt haves a value", function($spec, $data) {
			$spec($data->enum[20])->should->be(null);
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
	
	$spec->context("doing stack and queue operations", function($spec) {
		$spec->before_each(function($data) {
			$data->enum = new ArrayObject(2, 3);
		});
		
		$spec->it("should unshift elements into array", function($spec, $data) {
			$spec($data->enum->unshift(0, 1)->get_array())->should->be(array(1, 0, 2, 3));
		});
		
		$spec->it("should shift elements from array", function($spec, $data) {
			$spec($data->enum->shift())->should->be(2);
			$spec($data->enum->count())->should->be(1);
		});
		
		$spec->it("should push elements into array", function($spec, $data) {
			$spec($data->enum->push(0, 1)->get_array())->should->be(array(2, 3, 0, 1));
		});
		
		$spec->it("should pop elements from array", function($spec, $data) {
			$spec($data->enum->pop())->should->be(3);
			$spec($data->enum->count())->should->be(1);
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
	
	$spec->context("mapping data", function($spec) {
		$spec->it("should map the data with the given function", function($spec, $data) {
			$enum = new ArrayObject(1, 2, 3);
			
			$spec($enum->map(function($n) {return $n * 2;})->get_array())->should->be(array(2, 4, 6));
		});
		
		$spec->it("should returned a cloned version of current object", function($spec, $data) {
			$enum = new ArrayObjectExtendedTest(1);
			$enum->n = 20;
			$mapped = $enum->map(function($n) {return $n * 2;});
			
			$spec(get_class($mapped))->should->be(get_class($enum));
			$spec($mapped->n)->should->be(20);
		});
		
		$spec->it("should not change current data of object", function($spec, $data) {
			$enum = new ArrayObject(1, 2, 3);
			$mapped = $enum->map(function($n) {return $n * 2;});
			
			$spec($enum->get_array())->should->be(array(1, 2, 3));
		});
		
		$spec->it("should map current data if calls map_self", function($spec, $data) {
			$enum = new ArrayObject(1, 2, 3);
			$enum->map_self(function($n) {return $n * 2;});
			
			$spec($enum->get_array())->should->be(array(2, 4, 6));
		});
	});
});
