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
 * Get the value of array at given index
 *
 * If you access an undefined index of array, the PHP will generate a warning,
 * this function helps you to avoid this warning, and a plus you can set a
 * default value to be returned if the index is not defined
 *
 * @param array $array the given array
 * @param mixed $index the index into array
 * @param mixed $default the default value if array doesn't exists
 * @return mixed
 */
function array_get($array, $index, $default = null)
{
	return isset($array[$index]) ? $array[$index] : $default;
}

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
 * 	
 * 	public function __invoke()
 * 	{
 * 		return $this->multiply(10);
 * 	}
 * }
 *
 * $data = array(new A(1), new A(2), new A(3));
 * 
 * print_r(array_invoke($data, "multiply"));
 * print_r(array_invoke($data, "multiply", 5));
 * print_r(array_invoke($data));
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
 * Array
 * (
 *     [0] => 10
 *     [1] => 20
 *     [2] => 30
 * )
 * </code>
 *
 * @param array $array given array
 * @param string $method the method name to be executed, if you don't give the
 * method (or pass as null) the object itself will be invoked
 * @param mixed $args,... arguments of method
 * @return array the array with the return value of method called
 */
function array_invoke($array, $method = null)
{
	$args = array_slice(func_get_args(), 2);
	
	return array_map(function($item) use ($method, $args) {
		return call_user_func_array($method ? array($item, $method) : $item, $args);
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
		if (!is_object($item) || !isset($item->$attribute)) return null;
		
		return $item->$attribute;
	}, $array);
}

/**
 * Append items from one array to another
 *
 * This method behaviour like array_push, but instead of add one item this
 * method accepts one array, and push all elements of this array into original
 * one
 *
 * <code>
 * $data = array("a", 1, "c");
 * array_append($data, array("e", 2));
 * 
 * print_r($data);
 * </code>
 *
 * This example will output:
 *
 * <code>
 * Array
 * (
 *     [0] => a
 *     [1] => 1
 *     [2] => c
 *     [3] => e
 *     [4] => 2
 * )
 * </code>
 *
 * @param array $array array to receive data
 * @param array $data array containing data to be added
 * @return void
 */
function array_append(&$array, $data)
{
	foreach ($data as $value) {
		$array[] = $value;
	}
	
	return $array;
}

/**
 * Execute a series of executions into one accumulator value
 *
 * This method is used when you have a value, and want to progressive
 * apply a serie of tasks with each of items into array to that value
 *
 * example:
 *
 * <code>
 * array_inject(array(1, 2, 3), 0, function($acc, $value) { return $acc + $value }); //will return 6
 * </code>
 *
 * @param array $array array with given data
 * @param mixed $initial the initial value
 * @param function $iterator iterator with task to do
 * @return mixed the final value after the tasks
 */
function array_inject($array, $initial = 0, $iterator)
{
	foreach ($array as $value) {
		$initial = $iterator($initial, $value);
	}
	
	return $initial;
}

/**
 * Get a flatten version of an array
 *
 * This method gets one array and remove all nesting levels, leaving an
 * flat array as the result
 *
 * example:
 *
 * <code>
 * array_flatten(array("data", array("deept", "inside"))); //will return array("data", "deept", "inside")
 * </code>
 *
 * @param array $array array to flatten
 * @return array new flatten array
 */
function array_flatten($array)
{
	return array_inject($array, array(), function($acc, $value) {
		return array_append($acc, is_array($value) ? array_flatten($value) : array($value));
	});
}

/**
 * Partition one array
 *
 * Split one array in two, giving the true data into first array, and false
 * data into the second array. You can use a custom iterator to decide if a
 * value is true or false
 *
 * <code>
 * $data = array(1, 2, 3, 4, 5);
 *
 * list($even, $odd) = array_partition($data, function($item) { return ($item % 2) == 0 });
 *
 * echo $even; // => array(2, 4)
 * echo $odd; // => array(1, 3, 5)
 * </code>
 *
 * @param array $array data to partition
 * @param function $iterator
 * @return array
 */
function array_partition($array, $iterator = null)
{
	$trues = $falses = array();
	
	if (!$iterator) {
		$iterator = function($var) { return $var; };
	}
	
	foreach ($array as $item) {
		if ($iterator($item)) {
			$trues[] = $item;
		} else {
			$falses[] = $item;
		}
	}
	
	return array($trues, $falses);
}

/**
 * Find an item into array
 *
 * This method use a scalar value or a function to search for an element into
 * the array. Search functions is offen used for this, because you can find
 * the first item into array that matches with this.
 *
 * <code>
 * array_find(array(1, 2, 3, 4), function($el) { return $el > 2; }); // => 3
 * </code>
 *
 * @param array $array the array to search into
 * @param mixed $finder the finder function, or a scalar value to find
 * @return mixed
 */
function array_find($array, $finder)
{
	if (!is_object($finder)) {
		$finder = function($i) use ($finder) { return $finder === $i; };
	}
	
	foreach ($array as $item) {
		if ($finder($item)) return $item;
	}
	
	return null;
}

/**
 * Find many items into array
 *
 * Find the elements in array that is positive, or elements that passes into
 * the iterator value.
 *
 * <code>
 * array_find_all(array(1, 2, 3, 4), function($el) { return $el > 2; }); // => array(3, 4)
 * </code>
 *
 * @param array $array the array to search into
 * @param function $iterator the iterator to search
 * @return array
 */
function array_find_all($array, $iterator = null)
{
	$found = array();
	
	if (!$iterator) {
		$iterator = function($var) { return $var; };
	}
	
	foreach ($array as $value) {
		if ($iterator($value)) {
			$found[] = $value;
		}
	}
	
	return $found;
}


/**
 * Zip data of arrays
 *
 * Zip is a way to join data of many arrays and iterate over it
 *
 * <code>
 * //sample data
 * $a = array(1, 2, 3);
 * $b = array(4, 5, 6);
 * 
 * //using without a iterator
 * array_zip($a, $b); // array(array(1, 4), array(2, 5), array(3, 6))
 * 
 * //using with a iterator
 * array_zip(function($x, $y) { return $x + $y; }, $a, $b); // array(5, 7, 9);
 * </code>
 */
function array_zip()
{
	$args = func_get_args();
	$build = array();
	
	$iterator = is_a($args[0], "Closure") ? array_shift($args) : function() { $args = func_get_args(); return $args; };
	
	for ($i = 0; $i < count($args[0]); $i++) {
		$row = array();
		
		foreach ($args as $array) {
			$row[] = $array[$i];
		}
		
		$build[] = call_user_func_array($iterator, $row);
	}
	
	return $build;
}
