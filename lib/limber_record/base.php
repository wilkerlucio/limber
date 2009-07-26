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

namespace LimberRecord;

class Base extends \LimberSupport\DynamicObject
{
	/**
	 * Get current class name
	 *
	 * Gets the name of current class, this method is created only by convenience
	 *
	 * @return string current class name
	 */
	public static function class_name()
	{
		return get_called_class();
	}
	
	/**
	 * This is just a simple alias to get a static data for the class
	 *
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public static function get_static_data($name, $default = null)
	{
		return \LimberSupport\ClassParams::get(static::class_name(), $name, $default);
	}
	
	/**
	 * This is just a simple alias to get a static data for the class
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public static function set_static_data($name, $value)
	{
		\LimberSupport\ClassParams::set(static::class_name(), $name, $value);
	}
	
	/**
	 * Get the table name of current model
	 *
	 * This method calculates the name of table by pluralizing and lowercasing
	 * the name of current class. You can change the table name by passing an
	 * argument to this method, to back the table name to default, simple pass
	 * null as the argument.
	 *
	 * @param string $table_name the new table name
	 * @return string the table name
	 */
	public static function table_name($table_name = false)
	{
		if ($table_name !== false) static::set_static_data("table_name", $table_name);
		
		return static::get_static_data("table_name", str_tableize(static::class_name()));
	}
	
	/**
	 * Get the primary key field of current table
	 *
	 * By default the name of primary key field will be aways 'id', you can change
	 * this by passing the new primary key field as the argument of method, simple
	 * pass null as this argument to restore default behaviour.
	 *
	 * @param string $primary_key_field the new primary key field name
	 * @return string the current primary key field
	 */
	public static function primary_key_field($primary_key_field = false)
	{
		if ($primary_key_field !== false) static::set_static_data("primary_key_field", $primary_key_field);
		
		return static::get_static_data("primary_key_field", "id");
	}
}
