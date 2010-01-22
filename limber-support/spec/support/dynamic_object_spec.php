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

require_once "limber_support.php";

class DynamicObjectTest extends DynamicObject
{
	public $n = 0;
	public $_variable;
	
	public function simple()
	{
		return "simple";
	}
	
	public function get_item()
	{
		return "item value";
	}
	
	public function internal($var)
	{
		if ($var == "internal_getter") {
			return "into scope";
		}
		
		throw new CallerContinueException();
	}
	
	public function internal_setter($var, $value)
	{
		if ($var == "number") {
			$this->n += $value;
			return;
		}
		
		throw new CallerContinueException();
	}
	
	public function set_numeric($value)
	{
		$this->n += $value;
	}
}

class DynamicModuleTest
{
	public function module_instance($obj)
	{
		return "from instance";
	}
	
	public function module_instance_mod_var($obj, $value)
	{
		$obj->n = $value;
	}
	
	public static function module_static($class)
	{
		return "static from {$class}";
	}
	
	public static function module_static_with_args($class, $arg)
	{
		return "with {$arg} argument";
	}
}

DynamicObjectTest::extend("LimberSupport\DynamicModuleTest");

DynamicObjectTest::attr_accessor("variable");

DynamicObjectTest::define_getter("internal");
DynamicObjectTest::define_getter(function($object, $var) {
	if ($var == "lambda_getter") {
		return "lambda rocks!";
	}
	
	throw new CallerContinueException();
});

DynamicObjectTest::define_setter("internal_setter");
DynamicObjectTest::define_setter(function($object, $var, $value) {
	if ($var == "num") {
		$object->n += $value;
		return;
	}
	
	throw new CallerContinueException();
});

DynamicObjectTest::define_method("cool_think", function($object, $arg = "") {
	return $arg . $object->simple();
});

DynamicObjectTest::define_static_method("some_static", function($class_name, $arg = "") {
	return $arg . $class_name;
});

DynamicObjectTest::define_static_method("define_simple_getter", function($class_name, $getter) {
	$class_name::define_method($getter, function($object) use ($getter) {
		return $getter;
	});
});

DynamicObjectTest::define_ghost_method(function($object, $method_name, $args) {
	if (preg_match("/^(.*)_path$/", $method_name, $matches)) {
		return $matches[1];
	} else {
		throw new CallerContinueException();
	}
});

DynamicObjectTest::define_static_ghost_method(function($class, $method_name, $args) {
	if (preg_match("/^find_by_(.*)$/", $method_name, $matches)) {
		return $matches[1];
	} else {
		throw new CallerContinueException();
	}
});

describe("Dynamic Object", function($spec) {
	$spec->context("using attr_accessors", function($spec) {
		$spec->it("should use dynamic attribute", function($spec, $data) {
			$obj = new DynamicObjectTest();
			$obj->variable = 10;
			
			$spec($obj->variable)->should->be(10);
		});
	});
	
	$spec->context("calling dynamic getters", function($spec) {
		$spec->context("user getters", function($spec) {
			$spec->it("should get a lambda like getter", function($spec, $data) {
				$obj = new DynamicObjectTest();
				$spec($obj->lambda_getter)->should->be("lambda rocks!");
			});
			
			$spec->it("should get a internal function", function($spec, $data) {
				$obj = new DynamicObjectTest();
				$spec($obj->internal_getter)->should->be("into scope");
			});
		});
		
		$spec->context("default getters", function($spec) {
			$spec->it("should call return the value if the class has a get_x method", function($spec, $data) {
				$obj = new DynamicObjectTest();
				$spec($obj->item)->should->be("item value");
			});
			
			$spec->it("should call a simple function if its present", function($spec, $data) {
				$obj = new DynamicObjectTest();
				$spec($obj->simple)->should->be("simple");
			});
			
			$spec->it("should call a simple function if its present as dynamic method", function($spec, $data) {
				$obj = new DynamicObjectTest();
				$spec($obj->cool_think)->should->be("simple");
			});
		});
		
		$spec->it("should throw an CallerNotFoundException when dont find a getter", function($spec, $data) {
			$obj = new DynamicObjectTest();
			
			try {
				$obj->it_doesnt_exists;
				$spec(true)->should->be(false);
			} catch(CallerNotFoundException $e) {
				$spec(true)->should->be(true);
			}
		});
	});
	
	$spec->context("calling dynamic setters", function($spec) {
		$spec->context("calling user setters", function($spec) {
			$spec->it("should works for internal setters", function($spec, $data) {
				$obj = new DynamicObjectTest();
				$obj->number = 3;
				$obj->number = 5;
				
				$spec($obj->n)->should->be(8);
			});
			
			$spec->it("should works for lambda setters", function($spec, $data) {
				$obj = new DynamicObjectTest();
				$obj->num = 3;
				$obj->num = 1;
				
				$spec($obj->n)->should->be(4);
			});
		});
		
		$spec->context("calling default setters", function($spec) {
			$spec->it("should call set_x operation when present", function($spec, $data) {
				$obj = new DynamicObjectTest();
				$obj->numeric = 3;
				$obj->numeric = 4;
				
				$spec($obj->n)->should->be(7);
			});
		});
		
		$spec->it("should throw an CallerNotFoundException when dont find a setter", function($spec, $data) {
			$obj = new DynamicObjectTest();
			
			try {
				$obj->it_doesnt_exists = 4;
				$spec(true)->should->be(false);
			} catch(CallerNotFoundException $e) {
				$spec(true)->should->be(true);
			}
		});
	});
	
	$spec->context("calling instance methods", function($spec) {
		$spec->it("should call the user defined method", function($spec, $data) {
			$obj = new DynamicObjectTest();
			$obj->define_instance_method("hello", function() {
				return "world";
			});
			
			$spec($obj->hello())->should->be("world");
		});
		
		$spec->it("should override normal dynamic methods", function($spec, $data) {
			$obj = new DynamicObjectTest();
			$obj->define_instance_method("cool_think", function() {
				return "world";
			});
			
			$spec($obj->cool_think())->should->be("world");
		});
	});
	
	$spec->context("calling dynamic methods", function($spec) {
		$spec->it("should call a dynamic method", function($spec, $data) {
			$obj = new DynamicObjectTest();
			$spec($obj->cool_think())->should->be("simple");
		});
		
		$spec->it("should accept arguments", function($spec, $data) {
			$obj = new DynamicObjectTest();
			$spec($obj->cool_think("hello "))->should->be("hello simple");
		});
		
		$spec->it("should be callable by call_user_func_array", function($spec, $data) {
			$obj = new DynamicObjectTest();
			$spec(call_user_func_array(array($obj, "cool_think"), array("hello ")))->should->be("hello simple");
		});
		
		$spec->it("should throw an CallerNotFoundException when dont find a method", function($spec, $data) {
			$obj = new DynamicObjectTest();
			
			try {
				$obj->it_doesnt_exists();
				$spec(true)->should->be(false);
			} catch(CallerNotFoundException $e) {
				$spec(true)->should->be(true);
			}
		});
	});
	
	$spec->context("calling dynamic static methods", function($spec) {
		$spec->it("should call a dynamic static method", function($spec, $data) {
			$spec(DynamicObjectTest::some_static())->should->be("LimberSupport\\DynamicObjectTest");
		});
		
		$spec->it("should can define some methods inside", function($spec, $data) {
			DynamicObjectTest::define_simple_getter("limber_static");
			
			$obj = new DynamicObjectTest();
			$spec($obj->limber_static)->should->be("limber_static");
		});
		
		$spec->it("should throw an CallerNotFoundException when dont find a static method", function($spec, $data) {
			try {
				DynamicObjectTest::it_doesnt_exists();
				$spec(true)->should->be(false);
			} catch(CallerNotFoundException $e) {
				$spec(true)->should->be(true);
			}
		});
	});
	
	$spec->context("extending object with another one (used as module)", function ($spec) {
		$spec->it("should has the instance methods defined", function ($spec, $data) {
			$obj = new DynamicObjectTest();
			
			$spec($obj->module_instance())->should->be("from instance");
		});
		
		$spec->it("should has the instance methods that modify object variables", function ($spec, $data) {
			$obj = new DynamicObjectTest();
			$obj->module_instance_mod_var(3);

			$spec($obj->n)->should->be(3);
		});

		$spec->it("should has the static methods defined", function ($spec, $data) {
			$spec(DynamicObjectTest::module_static())->should->be("static from LimberSupport\DynamicObjectTest");
		});

		$spec->it("should work with static methods with args", function ($spec, $data) {
			$spec(DynamicObjectTest::module_static_with_args(3))->should->be("with 3 argument");
		});
	});
	
	$spec->context("calling ghost methods", function($spec) {
		$spec->it("should return the value if method cautch what it needs", function($spec, $data) {
			$obj = new DynamicObjectTest();
			
			$spec($obj->name_path())->should->be("name");
		});
		
		$spec->it("should return the value if a static ghost method cautch what it needs", function($spec, $data) {
			$spec(DynamicObjectTest::find_by_anything())->should->be("anything");
		});
	});
});
