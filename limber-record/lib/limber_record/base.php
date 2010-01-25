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

namespace LimberRecord;

class Base extends \LimberSupport\DynamicObject
{
	public $persistent;
	public $attributes = array();
	
	public function __construct($data = array())
	{
		$this->persistent = false;
		$this->initialize_attributes();
		$this->fill($data);
	}
	
	/**
	 * Get current connection in use
	 *
	 * @return LimberRecord\Adapters\Base
	 */
	public static function connection()
	{
		$con = Manager::instance()->connection();
		
		return $con;
	}
	
	/**
	 * Setup the attributes with blank values
	 */
	public function initialize_attributes()
	{
		$fields = static::table_fields();
		
		foreach ($fields as $field) {
			$this->attributes[$field] = null;
		}
	}
	
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
	 * Get fields for the table of current model
	 *
	 * This method simple gets the list with fields of table and cache the
	 * result for the next requests
	 *
	 * @return array
	 */
	public static function table_fields()
	{
		$fields = static::get_static_data("table_fields");
		
		if (!$fields) {
			$con = static::connection();
			$fields = $con->table_fields(static::table_name());
		}
		
		return $fields;
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
	
	/**
	 * Get the primary key value of this record
	 *
	 * @return string the primary key value for current record
	 */
	public function primary_key_value()
	{
		$pk = static::primary_key_field();
		
		return $this->$pk;
	}
	
	/**
	 * Alias for primary_key_value()
	 */
	public function get_id()
	{
		return $this->primary_key_value();
	}
	
	/**
	 * Fill the object with given data
	 *
	 * This is a simple loop setting the values for the object
	 * If you pass $raw as true, the values will be setted exactly as you
	 * give them (without going to virtual attributes or other possible
	 * transformations)
	 *
	 * @param array $data associative array containing data
	 * @param boolean $raw use true for raw attributes set
	 */
	public function fill($data, $raw = false)
	{
		foreach ($data as $key => $value) {
			if ($raw) {
				$this->write_attribute($key, $value);
			} else {
				$this->$key = $value;
			}
		}
	}
	
	/**
	 * Raw read attribute of object
	 *
	 * @param string $name
	 * @return string
	 */
	public function read_attribute($name)
	{
		return $this->attributes[$name];
	}
	
	/**
	 * Raw write attribute of object
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function write_attribute($name, $value)
	{
		$this->attributes[$name] = $value;
	}
	
	/**
	 * Retrive items from database
	 */
	public static function find($what, $options = array())
	{
		$options = array_merge(static::default_find_options(), $options);
		
		switch ($what) {
			case 'all':
				return static::find_every($options);
			case 'first':
				return static::find_initial($options);
			case 'last':
				return static::find_last($options);
		}
		
		return static::find_from_ids($what, $options);
	}
	
	public static function default_find_options()
	{
		$con = static::connection();

		return array(
			"select"     => $con->quote_table_name(static::table_name()) . ".*",
			"from"       => static::table_name(),
			"joins"      => null,
			"conditions" => null,
			"groupby"    => null,
			"order"      => $con->quote_table_name(static::table_name() . "." . static::primary_key_field()) . " asc",
			"limit"      => null,
			"offset"     => null
		);
	}
		
	public static function all()
	{
		$args = func_get_args();
		array_unshift($args, "all");
		
		return call_user_func_array(array("static", "find"), $args);
	}
				
	public static function first()
	{
		$args = func_get_args();
		array_unshift($args, "first");
		
		return call_user_func_array(array("static", "find"), $args);
	}
	
	public static function last()
	{
		$args = func_get_args();
		array_unshift($args, "last");
		
		return call_user_func_array(array("static", "find"), $args);
	}
	
	public static function find_every($options)
	{
		return static::find_by_sql(static::construct_sql_finder($options));
	}
	
	public static function find_initial($options)
	{
		$data = static::find_every($options);
		
		return $data->first;
	}
	
	public static function find_last($options)
	{
		if ($options['order']) {
			$options['order'] = static::reverse_sql_order($options['order']);
		}

		return static::find_initial($options);
	}
	
	public static function find_from_ids($ids, $options)
	{
		$con = static::connection();
		$pk = static::primary_key_field();
		
		if (is_array($ids)) {
			$options["conditions"] = $con->quote_table_name($pk) . " in (" . implode(",", static::sanitize_array($ids)) . ")";
			
			return static::find_every($options);
		} else {
			$options["conditions"] = $con->quote_table_name($pk) . " = " . static::sanitize($ids);
			
			return static::find_initial($options);
		}
	}
	
	public static function select_all($sql)
	{
		$con = static::connection();
		
		return $con->select($sql);
	}
	
	public static function count()
	{
		$con = static::connection();
		$table_name = $con->quote_table_name(static::table_name());
		
		return (int) $con->select_cell("select count(*) from $table_name");
	}
	
	public static function find_by_sql($sql)
	{
		$data = static::select_all($sql);
		$data = array_map(array(static::class_name(), "map_object"), $data);
		
		$collection = new Collection();
		
		foreach ($data as $object) {
			$object->persistent = true;
			$collection->push($object);
		}
		
		return $collection;
	}
	
	public static function map_object($data)
	{
		$object = new static;
		$object->fill($data, true);
		
		return $object;
	}
	
	public static function construct_sql_finder($options)
	{
		$con = static::connection();
		
		$sql  = "SELECT {$options['select']} ";
		$sql .= "FROM " . $con->quote_table_name($options['from']) . " ";

		static::add_joins($sql, $options['joins']);
		static::add_conditions($sql, $options['conditions']);
		static::add_groupby($sql, $options['groupby']);
		static::add_order($sql, $options['order']);
		static::add_limit($sql, $options['limit'], $options['offset']);

		return $sql;
	}
	
	public static function add_joins(&$sql, $joins)
	{
		$sql .= $joins . " ";
	}
	
	public static function add_conditions(&$sql, $conditions)
	{
		if (!$conditions) return;
		
		$sql .= 'WHERE ' . static::build_conditions($conditions) . ' ';
	}
	
	public static function build_conditions($conditions)
	{
		$con = static::connection();

		$sql = '';
		
		if (is_array($conditions)) {
			if (array_keys($conditions) === range(0, count($conditions) - 1)) {
				$query = array_shift($conditions);
				
				if (is_array($conditions[0])) {
					$conditions = array_map(array(static::class_name(), 'prepare_for_value'), $conditions[0]);
					
					$sql .= preg_replace_callback("/:(\w+)/", function($matches) use ($conditions) {
						$value = $conditions[$matches[1]];
						
						return $value;
					}, $query);
				} else {
					for($i = 0; $i < strlen($query); $i++) {
						if ($query[$i] == '?') {
							if (count($conditions) == 0) {
								throw new QueryMismatchParamsException('The number of question marks is more than provided params');
							}
							
							$sql .= static::prepare_for_value(array_shift($conditions));
						} else {
							$sql .= $query[$i];
						}
					}
				}
			} else {
				$factors = array();
				
				foreach ($conditions as $key => $value) {
					$matches = array();
					
					if (preg_match("/([a-z_].*?)\s*((?:[><!=\s]|LIKE|IS|NOT)+)/i", $key, $matches)) {
						$key  = $matches[1];
						$op   = strtoupper($matches[2]);
					} else {
						if ($value === null) {
							$op = 'IS';
						} elseif (is_array($value)) {
							$op = 'IN';
						} else {
							$op = "=";
						}
					}
					
					$value = static::prepare_for_value($value);
					
					$factors[] = $con->quote_column_name($key) . " $op $value";
				}
				
				$sql .= implode(" AND ", $factors);
			}
		} else {
			$sql .= $conditions;
		}
		
		return $sql;
	}
	
	public static function reverse_sql_order($order)
	{
		$reversed = explode(',', $order);
		
		foreach ($reversed as $k => $rev) {
			if (preg_match('/\s(asc|ASC)$/', $rev)) {
				$rev = preg_replace('/\s(asc|ASC)$/', ' DESC', $rev);
			} elseif (preg_match('/\s(desc|DESC)$/', $rev)) {
				$rev = preg_replace('/\s(desc|DESC)$/', ' ASC', $rev);
			} elseif (!preg_match('/\s(acs|ASC|desc|DESC)$/', $rev)) {
				$rev .= " DESC";
			}
			
			$reversed[$k] = $rev;
		}
		
		return implode(',', $reversed);
	}
	
	public static function add_groupby(&$sql, $order)
	{
		if ($order) {
			$sql .= "GROUP BY $order ";
		}
	}
	
	public static function add_order(&$sql, $order)
	{
		if ($order) {
			$sql .= "ORDER BY $order ";
		}
	}
	
	public static function add_limit(&$sql, $limit, $offset)
	{
		if ($limit) {
			if ($offset !== false) {
				$sql .= "LIMIT $offset, $limit ";
			} else {
				$sql .= "LIMIT $limit ";
			}
		}
	}
	
	public static function prepare_for_value($value)
	{
		$quoted = static::sanitize($value);
		
		return is_array($value) ? "($quoted)" : $quoted;
	}
	
	public static function sanitize($item)
	{
		$con = static::connection();
		
		return $con->quote($item);
	}
	
	public static function sanitize_array($array)
	{
		return array_map(array(static::class_name(), "sanitize"), $array);
	}
	
	public function save()
	{
		return $this->create_or_update();
	}
	
	public function create_or_update()
	{
		if ($this->persistent) {
			$this->update();
		} else {
			$this->create();
		}
		
		return $this;
	}
	
	public function create()
	{
		$con = static::connection();
		$pk = static::primary_key_field();
		$table = $con->quote_table_name($this->table_name());
		$fields = $this->attributes;
		
		$sql_fields = implode(",", array_map(array($con, "quote_column_name"), array_keys($fields)));
		$sql_values = implode(",", array_map(array($this, 'prepare_for_value'), $fields));
		
		$sql = "INSERT INTO $table ($sql_fields) VALUES ($sql_values);";
		
		$this->$pk = $con->insert($sql);
		$this->persistent = true;
		
		return $this;
	}
	
	public function update()
	{
		$con = static::connection();
		
		$pk = static::primary_key_field();
		$pk_value = static::sanitize($this->id);
		$table = static::table_name();
		$fields = $this->attributes;
		
		$sql_set = array();
		
		foreach ($fields as $key => $value) {
			$sql_set[] = $con->quote_column_name($key) . " = " . $this->prepare_for_value($value);
		}
		
		$sql_set = implode(",", $sql_set);
		
		$sql = "UPDATE " . $con->quote_table_name($table) . " SET $sql_set WHERE " . $con->quote_column_name($pk) . " = $pk_value;";
		
		$con->update($sql);
		
		return $this;
	}
	
	public function destroy()
	{
		//check if record exists before delete
		if (!$this->persistent) {
			return false;
		}
		
		$con = static::connection();
		
		$pk     = static::primary_key_field();
		$pk_val = static::prepare_for_value($this->$pk);
		$table  = $con->quote_table_name($this->table_name());
		
		$sql = "DELETE FROM $table WHERE " . $con->quote_column_name($pk) . " = $pk_val";
		
		$con->update($sql);
		
		$this->persistent = false;
		
		return true;
	}
	
	public function toString() {
		$base = "LimberRecord::Base::" . get_class($this);
		
		if ($this->persistent) {
			$pk = $this->primary_key_value();
			$base .= "($pk)";
		}
		
		return $base;
	}
}

Base::define_getter(function($object, $attribute) {
	if (array_key_exists($attribute, $object->attributes)) {
		return $object->attributes[$attribute];
	}
	
	throw new \LimberSupport\CallerContinueException();
});

Base::define_setter(function($object, $attribute, $value) {
	if (array_key_exists($attribute, $object->attributes)) {
		$object->attributes[$attribute] = $value;
		
		return $object;
	}
	
	throw new \LimberSupport\CallerContinueException();
});

class QueryMismatchParamsException extends \Exception {}
