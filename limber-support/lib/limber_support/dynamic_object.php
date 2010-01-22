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

/**
 * DynamicObject provides some meta-programming features in a hand
 *
 * Many times you need to modify the classes outside of class definitions,
 * or simple to write code thats write code. If you think in this a good,
 * this class is for you.
 *
 * The simpler way to describe the DynamicObject, is to say that this class
 * provides a way to you modifify the classes outside of their definition. To
 * give this capacity to the classe you only need to extend from DynamicObject
 * and them the DynamicObject will do all the magic for you.
 *
 * At first DynamicObject provides a default extensions for you, let's see
 * these default extensions first.
 *
 * Using property accessors:
 *
 * Many times you follow that OOP pattern: define a private variable, define
 * the getter method, define the setter method, like in the code above
 *
 * <code>
 * class Person
 * {
 *    private $name;
 *
 *    public function get_name()
 *    {
 *      return $this->name;
 *    }
 *
 *    public function set_name($name)
 *    {
 *      $this->name = $name;
 *    }
 * }
 * </code>
 *
 * This kind of code is repeated in almost all of your code, its big and
 * exaustive.
 *
 * With the meta-programming of DynamicObject you can do the same think with
 * the following code
 *
 * <code>
 * class Person extends LimberSupport\DynamicObject
 * {
 *	 public $_name;
 * }
 *
 * Person::attr_accessor("name");
 *
 * $p = new Person();
 * $p->name = "Foo"
 * 
 * echo $p->name; // => "Foo"
 * </code>
 *
 * And later, when you need to put some validation or modification of attribute
 * you can do this simple
 *
 * <code>
 * class Person extends LimberSupport\DynamicObject
 * {
 *   public $_name;
 *
 *   public function set_name($name)
 *   {
 *	   if (strlen($name) > 3) $this->_name = $name;
 *   }
 * }
 * Person::attr_accessor("name");
 *
 * $p = new Person();
 * $p->name = "Foo"
 * 
 * echo $p->name; // => "Foo"
 * </code>
 * 
 * As you notice, the variable needs to be public, the reason is the variable
 * will be externally accessed. This breaks the encapsulation a little, yes,
 * but if it's private, since PHP 5.3 you can access this by reflection (even
 * it's private). The point here is the DynamicObject is creating the methods
 * outside of the class, this can be usefull when you need a class that will
 * needs to be external extended.
 *
 * Now let's see how to create your own extensions
 *
 * //TODO: doc for create extensions
 *
 * @package LimberSupport
 */
abstract class DynamicObject
{
	public $_instance_methods = array();
	
	public static function extend($classname)
	{
		$reflection = new \ReflectionClass($classname);
		$methods = $reflection->getMethods();
		
		foreach ($methods as $method) {
			if (!$method->isPublic() || $method->isAbstract()) continue;
			
			$method_name = $method->getName();
			
			if ($method->isStatic()) {
				static::define_static_method($method_name, function () use ($classname, $method_name) {
					$args = func_get_args();
					
					return call_user_func_array(array($classname, $method_name), $args);
				});
			} else {
				static::define_method($method_name, function () use ($classname, $method_name) {
					$obj = new $classname();
					$args = func_get_args();
					
					return call_user_func_array(array($classname, $method_name), $args);
				});
			}
		}
	}
	
	public function define_instance_method($method_name, $callback)
	{
		$this->_instance_methods[$method_name] = $callback;
	}
	
	public static function define_getter($callback)
	{
		ClassParams::append(get_called_class(), "getters", $callback);
	}
	
	public static function define_setter($callback)
	{
		ClassParams::append(get_called_class(), "setters", $callback);
	}
	
	public static function define_method($method_name, $callback)
	{
		$methods = ClassParams::get(get_called_class(), "methods", array());
		$methods[$method_name] = $callback;
		
		ClassParams::set(get_called_class(), "methods", $methods);
	}
	
	public static function define_static_method($method_name, $callback)
	{
		$methods = ClassParams::get(get_called_class(), "static_methods", array());
		$methods[$method_name] = $callback;
		
		ClassParams::set(get_called_class(), "static_methods", $methods);
	}
		
	public static function define_ghost_method($callback)
	{
		ClassParams::append(get_called_class(), "ghost_methods", $callback);
	}
	
	public static function define_static_ghost_method($callback)
	{
		ClassParams::append(get_called_class(), "static_ghost_methods", $callback);
	}
	
	public function has_method($method_name)
	{
		if (method_exists($this, $method_name)) {
			return true;
		}
		
		if (isset($this->_instance_methods[$method_name])) {
			return true;
		}
		
		$iterator = new ClassAncestorsIterator(get_class($this));
		
		foreach ($iterator as $current_class) {
			$methods = ClassParams::get($current_class, "methods", array());
			
			if (isset($methods[$method_name])) {
				return true;
			}
		}
		
		return false;
	}
	
	public function _get_property($var)
	{
		$method_name = "get_{$var}";
		
		if ($this->has_method($method_name)) {
			return $this->$method_name();
		}
		
		throw new CallerContinueException();
	}
	
	public function _get_function($var)
	{
		if ($this->has_method($var)) {
			return $this->$var();
		}
		
		throw new CallerContinueException();
	}
	
	public function _set_property($var, $value)
	{
		$method_name = "set_{$var}";
		
		if ($this->has_method($method_name)) {
			return $this->$method_name($value);
		}
		
		throw new CallerContinueException();
	}
	
	public function __get($var)
	{
		$iterator = new ClassAncestorsIterator(get_class($this));
		
		foreach ($iterator as $current_class) {
			$getters = array_reverse(ClassParams::get($current_class, "getters", array()));
			
			foreach ($getters as $getter) {
				try {
					if (is_string($getter)) {
						$value = $this->$getter($var);
					} else {
						$value = $getter($this, $var);
					}
				
					return $value;
				} catch (CallerContinueException $e) {}
			}
		}
		
		throw new CallerNotFoundException("Can't handle getter for {$var}");
	}
	
	public function __set($var, $value)
	{
		$iterator = new ClassAncestorsIterator(get_class($this));
		
		foreach ($iterator as $current_class) {
			$setters = array_reverse(ClassParams::get($current_class, "setters", array()));
			
			foreach ($setters as $setter) {
				try {
					if (is_string($setter)) {
						$value = $this->$setter($var, $value);
					} else {
						$value = $setter($this, $var, $value);
					}
				
					return $value;
				} catch (CallerContinueException $e) {}
			}
		}
		
		throw new CallerNotFoundException("Can't handle setter for {$var}");
	}
	
	public function __call($method, $arguments)
	{
		$iterator = new ClassAncestorsIterator(get_class($this));
		
		array_unshift($arguments, $this);
		
		if (isset($this->_instance_methods[$method])) {
			return call_user_func_array($this->_instance_methods[$method], $arguments);
		}
		
		//try regular method definitions
		foreach ($iterator as $current_class) {
			$methods = ClassParams::get($current_class, "methods", array());
			
			if (isset($methods[$method])) {
				return call_user_func_array($methods[$method], $arguments);
			}
		}
		
		//try ghost method definitions after
		foreach ($iterator as $current_class) {
			$ghosts = ClassParams::get($current_class, "ghost_methods", array());
			
			foreach ($ghosts as $ghost) {
				try {
					$value = call_user_func($ghost, $this, $method, array_slice($arguments, 1));
					return $value;
				} catch (CallerContinueException $e) {}
			}
		}
		
		throw new CallerNotFoundException("Can't handle caller for {$method}");
	}
	
	public static function __callStatic($method, $arguments)
	{
		$iterator = new ClassAncestorsIterator(get_called_class());
		
		array_unshift($arguments, get_called_class());
		
		//try regular method definitions
		foreach ($iterator as $current_class) {
			$methods = ClassParams::get($current_class, "static_methods", array());
			
			if (isset($methods[$method])) {
				return call_user_func_array($methods[$method], $arguments);
			}
		}
		
		//try ghost method definitions after
		foreach ($iterator as $current_class) {
			$ghosts = ClassParams::get($current_class, "static_ghost_methods", array());

			foreach ($ghosts as $ghost) {
				try {
					$value = call_user_func($ghost, $current_class, $method, array_slice($arguments, 1));
					return $value;
				} catch (CallerContinueException $e) {}
			}
		}
		
		throw new CallerNotFoundException("Can't handle static caller for {$method}");
	}
}

//basic getters and setters
DynamicObject::define_getter("_get_function");
DynamicObject::define_getter("_get_property");
DynamicObject::define_setter("_set_property");

//defining attribute accessors
DynamicObject::define_static_method("attr_reader", function() {
	$attributes = func_get_args();
	$class = array_shift($attributes);
	
	foreach ($attributes as $attr) {
		$class::define_method("get_{$attr}", function($object) use ($attr) {
			$attr = "_" . $attr;
			return $object->$attr;
		});
	}
});

DynamicObject::define_static_method("attr_writer", function() {
	$attributes = func_get_args();
	$class = array_shift($attributes);
		
	foreach ($attributes as $attr) {
		$class::define_method("set_{$attr}", function($object, $value) use ($attr) {
			$attr = "_" . $attr;
			$object->$attr = $value;
		});
	}
});

DynamicObject::define_static_method("attr_accessor", function() {
	$attributes = func_get_args();
	$class = array_shift($attributes);
	
	foreach ($attributes as $attr) {
		$class::define_method("get_{$attr}", function($object) use ($attr) {
			$attr = "_" . $attr;
			return $object->$attr;
		});
		
		$class::define_method("set_{$attr}", function($object, $value) use ($attr) {
			$attr = "_" . $attr;
			$object->$attr = $value;
		});
	}
});

class CallerContinueException extends \Exception {}
class CallerNotFoundException extends \Exception {}
