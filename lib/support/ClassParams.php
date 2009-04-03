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
 * This class provides a simple way to store class level variables
 *
 * To understand why this class was created, see the following code sample:
 *
 * <code>
 * abstract class ParentClass
 * {
 * 	protected static $static_data;
 * 	
 * 	public static function get_static_data()
 * 	{
 * 		return static::$static_data;
 * 	}
 * 	
 * 	public static function set_static_data($value)
 * 	{
 * 		static::$static_data = $value;
 * 	}
 * }
 * 
 * class ChildOne extends ParentClass {}
 * class ChildTwo extends ParentClass {}
 * 
 * ChildOne::set_static_data("first value");
 * 
 * echo ChildOne::get_static_data(); //outputs: first value
 * 
 * ChildTwo::set_static_data("from second");
 * 
 * echo ChildTwo::get_static_data(); //outputs: second value
 * echo ChildOne::get_static_data(); //outputs: second value
 * </code>
 *
 * Like you saw, the static data is shared between child classes
 *
 * To solve this issue ClassParams provides a way to store class attributes
 *
 * Working sample:
 *
 * <code>
 * abstract class ParentClass
 * {
 * 	public static function get_static_data()
 * 	{
 * 		return ClassParams::get(get_called_class(), "static_data");
 * 	}
 * 	
 * 	public static function set_static_data($value)
 * 	{
 * 		ClassParams::set(get_called_class(), "static_data", $value);
 * 	}
 * }
 * 
 * class ChildOne extends ParentClass {}
 * class ChildTwo extends ParentClass {}
 * 
 * ChildOne::set_static_data("first value");
 * 
 * echo ChildOne::get_static_data(); //outputs: first value
 * 
 * ChildTwo::set_static_data("from second");
 * 
 * echo ChildTwo::get_static_data(); //outputs: second value
 * echo ChildOne::get_static_data(); //outputs: first value
 * </code>
 *
 * This can be usefull when dealing with class dynamic setup
 *
 * @package Support
 */
abstract class ClassParams
{
	private static $classes = array();
	
	/**
	 * Get a class param
	 *
	 * If you try to get a param that not already exists this method will return
	 * null (without any warnings)
	 *
	 * @param string $class class name
	 * @param string $param the param to be got
	 * @return mixed
	 */
	public static function get($class, $param)
	{
		return isset(self::$classes[$class][$param]) ? self::$classes[$class][$param] : null;
	}
	
	/**
	 * Set a class param
	 *
	 * @param string $class class name
	 * @param string $param the param to be set
	 * @param mixed $value the value to be used
	 */
	public static function set($class, $param, $value)
	{
		self::$classes[$class][$param] = $value;
	}
	
	/**
	 * Append to class param
	 *
	 * You can append a value directly without set before (the array will be
	 * automatic created by PHP)
	 *
	 * @param string $class class name
	 * @param string $param the param to be used
	 * @param mixed $value the value to be appended
	 */
	public static function append($class, $param, $value)
	{
		self::$classes[$class][$param][] = $value;
	}
}
