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

require_once "limber_support.php";

describe("Event", function($spec) {
	$spec->context("creating events", function($spec) {
		$spec->before_each(function($data) {
			$data->evt = new LimberSupport\Event();
		});
		
		$spec->it("should add new listeners to the event", function($spec, $data) {
			$data->evt->add_listener(function($data) {});
			
			$spec(count($data->evt->listeners()))->should->be(1);
		});
		
		$spec->it("should return a blank array if there is no listeners for that event", function($spec, $data) {
			$spec(is_array($data->evt->listeners()))->should->be(true);
		});
		
		$spec->it("should trigger the listeners", function($spec, $data) {
			$a = 5;
			
			$data->evt->add_listener(function($n) use (&$a) { $a += $n; });
			$data->evt->add_listener(function($n) use (&$a) { $a *= $n; });
			$data->evt->call(2);
			
			$spec($a)->should->be(14);
		});
		
		$spec->it("should trigger the listeners with array arguments", function($spec, $data) {
			$a = 5;
			
			$data->evt->add_listener(function($n, $x) use (&$a) { $a += $n * $x; });
			$data->evt->call_array(array(3, 2));
			
			$spec($a)->should->be(11);
		});
		
		$spec->it("should trigger the listeners using the object itself", function($spec, $data) {
			$a = 5;
			
			$data->evt->add_listener(function($n) use (&$a) { $a += $n; });
			$evt = $data->evt;
			$evt(3);
			
			$spec($a)->should->be(8);
		});
		
		$spec->it("should accept default params", function($spec, $data) {
			$a = 0;
			
			$evt = new LimberSupport\Event(2);
			
			$evt->add_listener(function($x, $y) use (&$a) { $a += $x + $y; });
			$evt->call(1);
			
			$spec($a)->should->be(3);
		});
	});
});
