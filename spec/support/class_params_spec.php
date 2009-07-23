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

class ClassParamsTestA
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

class ClassParamsTestB extends ClassParamsTestA {}
class ClassParamsTestC extends ClassParamsTestA {}

describe("ClassParams class", function($spec) {
	$spec->it("should get a default value if index is not set", function($spec, $data) {
		$spec(ClassParams::get("test_class", "some", "default"))->should->be("default");
	});
	
	$spec->it("should preserve data between child classes", function($spec, $data) {
		ClassParamsTestB::set_static_data("from b");
		ClassParamsTestC::set_static_data("data from c");
		
		$spec(ClassParamsTestB::get_static_data())->should->be("from b");
		$spec(ClassParamsTestC::get_static_data())->should->be("data from c");
	});
});
