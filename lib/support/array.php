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

/**
 * This package provide support methods for dealing with arrays
 *
 * @package Support
 * @subpackage array
 */

/**
 * Get a grouped array based on it's contents
 *
 * This method can be usefull when need to split your array into groups defined
 * by some calculation about their values.
 *
 * <code>
 * $strings = array("apple", "bee", "money", "hii", "banana", "monkey", "bear");
 * 
 * $grouped = array_group_by($strings, function($string) { return strlen($string); });
 * 
 * print_r($grouped);
 * </code>
 *
 * This example will output:
 *
 * <code>
 * Array
 * (
 *     [5] => Array
 *         (
 *             [0] => apple
 *             [1] => money
 *         )
 *
 *     [3] => Array
 *         (
 *             [0] => bee
 *             [1] => hii
 *         )
 *
 *     [6] => Array
 *         (
 *             [0] => banana
 *             [1] => monkey
 *         )
 *
 *     [4] => Array
 *         (
 *             [0] => bear
 *         )
 *
 * )
 * </code>
 *
 * @param array $array the given array
 * @param function $grouper the method to calculate the group of each item
 * @return array
 */
function array_group_by($array, $grouper)
{
	$groups = array();
	
	foreach ($array as $item) {
		$key = $grouper($item);
		$groups[$key][] = $item;
	}
	
	return $groups;
}

/**
 * Invoke one method in all array items
 *
 * This function call a method in all items of the array, and return one
 * array with the given results.
 *
 * <code>
 * class A
 * {
 * 	public $n;
 * 	
 * 	public function __construct($n)
 * 	{
 * 		$this->n = $n;
 * 	}
 * 	
 * 	public function multiply($x = 2)
 * 	{
 * 		return $this->n * $x;
 * 	}
 * }
 *
 * $data = array(new A(1), new A(2), new A(3));
 * 
 * print_r(array_invoke($data, "multiply"));
 * print_r(array_invoke($data, "multiply", 5));
 * </code>
 *
 * This example will output:
 *
 * <code>
 * Array
 * (
 *     [0] => 2
 *     [1] => 4
 *     [2] => 6
 * )
 * Array
 * (
 *     [0] => 5
 *     [1] => 10
 *     [2] => 15
 * )
 * </code>
 *
 * @param array $array given array
 * @param string $method the method name to be executed
 * @param mixed $args,... arguments of method
 * @return array the array with the return value of method called
 */
function array_invoke($array, $method)
{
	$args = array_slice(func_get_args(), 2);
	
	return array_map(function($item) use ($method, $args) {
		return call_user_func_array(array($item, $method), $args);
	}, $array);
}

/**
 * Get some attribute of each object in array
 *
 * This method get some attribute at each element of array and return one
 * array with given attributes.
 *
 * <code>
 * class A
 * {
 * 	public $n;
 * 	
 * 	public function __construct($n)
 * 	{
 * 		$this->n = $n;
 * 	}
 * }
 *
 * $data = array(new A(1), new A(2), new A(3));
 * 
 * print_r(array_pluck($data, "n"));
 * </code>
 *
 * This example will output:
 *
 * <code>
 * Array
 * (
 *     [0] => 1
 *     [1] => 2
 *     [2] => 3
 * )
 * </code>
 *
 * @param array $array given array
 * @param string $attribute the attribute to be extracted
 * @return array array containg the attributes extracted
 */
function array_pluck($array, $attribute)
{
	return array_map(function($item) use ($attribute) {
		return $item->$attribute;
	}, $array);
}
