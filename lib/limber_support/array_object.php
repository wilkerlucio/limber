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
	 * Add new elements at the end of array
	 */
	public function push()
	{
		array_append($this->data, func_get_args());
		
		return $this;
	}
}
