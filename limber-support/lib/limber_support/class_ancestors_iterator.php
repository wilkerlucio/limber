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

namespace LimberSupport;

/**
 * This class provides a simple way to navigate by the class ancestors
 *
 * Use this class when you need to navigate by the class ancestors, see how
 * into the example above:
 *
 * <code>
 * class A {}
 * class B extends A {}
 * class C extends B {}
 *
 * $iterator = new LimberSupport\ClassAncestorsIterator("C");
 *
 * foreach ($iterator as $class_name) {
 *   echo $class_name . "\n";
 * }
 * </code>
 *
 * This code will output:
 *
 * <code>
 * C
 * B
 * A
 * </code>
 *
 * @package LimberSupport
 */
class ClassAncestorsIterator implements \Iterator
{
	private $base_class;
	private $current_class;
	private $key;
	
	public function __construct($class)
	{
		$this->base_class = $this->current_class = $class;
		$this->key = 0;
	}
	
	public function current()
	{
		return $this->current_class;
	}
	
	public function key()
	{
		return $this->key;
	}
	
	public function next()
	{
		$class = new \ReflectionClass($this->current_class);
		$class = $class->getParentClass();
		
		$this->current_class = $class ? $class->getName() : null;
		
		$this->key++;
	}
	
	public function rewind()
	{
		$this->current_class = $this->base_class;
		$this->key = 0;
	}
	
	public function valid()
	{
		return !!$this->current_class;
	}
}
