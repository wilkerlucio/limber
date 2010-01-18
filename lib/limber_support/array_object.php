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

class ArrayObject extends DynamicObject implements \ArrayAccess, \Countable, \Iterator
{
	private $data;
	
	public function __construct()
	{
		$this->data = array();
		
		call_user_func_array(array($this, 'push'), func_get_args());
	}
	
	//implementing ArrayAccess interface
	public function offsetSet($offset, $value)
	{
		$this->data[$offset] = $value;
	}
	
	public function offsetExists($offset)
	{
		return isset($this->data[$offset]);
	}
	
	public function offsetUnset($offset)
	{
		unset($this->data[$offset]);
	}
	
	public function offsetGet($offset)
	{
		return isset($this->data[$offset]) ? $this->data[$offset] : null;
	}
	
	//implementing Countable interface
	public function count()
	{
		return count($this->data);
	}
	
	//implementing Iterator interface
	public function current()
	{
		return current($this->data);
	}
	
	public function key()
	{
		return key($this->data);
	}
	
	public function next()
	{
		next($this->data);
	}
	
	public function rewind()
	{
		reset($this->data);
	}
	
	public function valid()
	{
		return isset($this->data[$this->key()]);
	}
	
	//fun starts here :)
	
	/**
	 * Get the internal array data of ArrayObject
	 *
	 * @return array
	 */
	public function get_array()
	{
		return $this->data;
	}
	
	/**
	 * Override current internal array data
	 *
	 * @param array $array new array data
	 */
	public function set_array($array)
	{
		$this->data = $array;
		
		return $this;
	}
	
	/**
	 * Get the first element of array
	 *
	 * @return mixed
	 */
	public function first()
	{
		return $this->count() > 0 ? reset($this->data) : null;
	}
	
	/**
	 * Get the last element of array
	 *
	 * @return mixed
	 */
	public function last()
	{
		return $this->count() > 0 ? end($this->data) : null;
	}
	
	/**
	 * Add elements at beggining of array
	 *
	 * @param [...] elements to be shifted
	 */
	public function unshift()
	{
		$elements = func_get_args();
		
		foreach ($elements as $element) {
			array_unshift($this->data, $element);
		}
		
		return $this;
	}
	
	/**
	 * Remove the first element of array and return it
	 *
	 * @return mixed
	 */
	public function shift()
	{
		return array_shift($this->data);
	}
	
	/**
	 * Add new elements at the end of array
	 */
	public function push()
	{
		$elements = func_get_args();
		
		array_append($this->data, $elements);
		
		return $this;
	}
	
	/**
	 * Remove the last element of array and return it
	 *
	 * @return mixed
	 */
	public function pop()
	{
		return array_pop($this->data);
	}
	
	/**
	 * Map the current data of object
	 *
	 * note: this method clones the current object and return the cloned object
	 *       with replaced data by mapping, for performance reasons you should
	 *       use map_self to only change current data if you dont need the old
	 *       copy
	 *
	 * @param function Mapper function
	 * @return ArrayObject
	 */
	public function map($callback)
	{
		$obj = clone $this;
		$obj->map_self($callback);
		
		return $obj;
	}
	
	/**
	 * Map current data of object
	 *
	 * note: after call this method you will lose the old data, to map data to a
	 *       copy of object use map
	 *
	 * @param function Mapper function
	 * @return ArrayObject
	 */
	public function map_self($callback)
	{
		$this->data = array_map($callback, $this->data);
		
		return $this;
	}
	
	/**
	 * Make a new ArrayObject with the pluck data of object
	 *
	 * @see array_pluck
	 * @param string $attribute
	 * @return ArrayObject
	 */
	public function pluck($attribute)
	{
		$obj = clone $this;
		$obj->pluck_self($attribute);
		
		return $obj;
	}
	
	/**
	 * Pluck an attribute of objects into array
	 *
	 * @see array_pluck
	 * @param string $attribute
	 * @return ArrayObject
	 */
	public function pluck_self($attribute)
	{
		$this->data = array_pluck($this->data, $attribute);
		
		return $this;
	}
	
	/**
	 * Make a new ArrayObject with the invoked data of objects
	 *
	 * @see array_invoke
	 * @param string $method
	 * @return ArrayObject
	 */
	public function invoke($method)
	{
		$args = func_get_args();
		
		$obj = clone $this;
		call_user_func_array(array($obj, "invoke_self"), $args);
		
		return $obj;
	}
	
	/**
	 * Make a new ArrayObject with the invoked data of objects
	 *
	 * @see array_invoke
	 * @param string $method
	 * @return ArrayObject
	 */
	public function invoke_self()
	{
		$args = func_get_args();
		array_unshift($args, $this->data);
		
		$this->data = call_user_func_array("array_invoke", $args);
		
		return $this;
	}
}
