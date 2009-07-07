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

class Base
{
	public static function class_name()
	{
		return get_called_class();
	}
	
	public static function get_static_data($name, $default = null)
	{
		return \ClassParams::get(static::class_name(), $name, $default);
	}
	
	public static function set_static_data($name, $value)
	{
		\ClassParams::set(static::class_name(), $name, $value);
	}
	
	public static function table_name($table_name = false)
	{
		if ($table_name !== false) static::set_static_data("table_name", $table_name);
		
		return static::get_static_data("table_name", str_tableize(static::class_name()));
	}
	
	public static function primary_key_field($primary_key_field = false)
	{
		if ($primary_key_field !== false) static::set_static_data("primary_key_field", $primary_key_field);
		
		return static::get_static_data("primary_key_field", "id");
	}
}
