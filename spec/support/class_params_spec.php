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

require_once "limber_support.php";

class A
{
	public static function get_static_data()
	{
		return ClassParams::get(get_called_class(), "static_data");
	}
	
	public static function set_static_data($value)
	{
		ClassParams::set(get_called_class(), "static_data", $value);
	}
}

class B extends A {}
class C extends A {}

describe("ClassParams class", function($spec) {
	$spec->it("should preserve data between child classes", function($spec, $data) {
		B::set_static_data("from b");
		C::set_static_data("data from c");
		
		$spec(B::get_static_data())->should->be("from b");
		$spec(C::get_static_data())->should->be("data from c");
	});
});
