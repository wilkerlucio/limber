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

class Manager
{
	private static $instance;
	
	private $connections;
	private $adapters_path;
	
	// prevent creating instances with new
	private function __construct()
	{
		$this->connections = array();
		
		$this->adapters_path = array();
		$this->adapters_path[] = "limber_record/adapters";
	}
	
	/**
	 * Get the manager instance
	 *
	 * @return LimberRecord\Manager
	 */
	public static function instance()
	{
		if (!self::$instance) {
			self::$instance = new self;
		}
		
		return self::$instance;
	}
	
	/**
	 * Create a new connection
	 *
	 * @param string $adapter the name of adapter
	 * @param string $host host of database
	 * @param string $user user name of database
	 * @param string $password database password
	 * @param string $database database to connect
	 * @return LimberRecord\Adapters\Abstract the connection adapter
	 */
	public function connect($adapter, $host, $user, $password, $database = null)
	{
		$driver = self::adapter($adapter);
		
		$driver->connect($host, $user, $password);
		if ($database) $driver->select_db($database);
		
		$this->connections[] = $driver;
		
		return $driver;
	}
	
	/**
	 * Get the last active connection
	 *
	 * @return LimberRecord\Adapters\Abstract | null
	 */
	public function connection()
	{
		foreach (array_reverse($this->connections) as $connection) {
			if ($connection->alive()) return $connection;
		}
		
		return null;
	}
	
	/**
	 * Get a list with current connections
	 *
	 * @return array array containing the current connections
	 */
	public function connections()
	{
		return $this->connections;
	}
	
	/**
	 * Add a path to load adapters
	 *
	 * The adapter name should follow the conventions:
	 * * The name of adapter should be: LimberRecord_Adapters_Adaptername
	 * * The file name should be the name of adapter in lowercase: adaptername.php
	 *
	 * @param string $path The path to be added
	 */
	public function add_adapter_path($path)
	{
		$path = rtrim($path, "\\/");
		
		$this->adapters_path[] = $path;
	}
	
	/**
	 * Get a instance of a given database adapter
	 *
	 * @param string $adapter the name of adapter
	 * @throws LimberRecord\InvalidAdapterException when no adapter found
	 * @return LimberRecord\Adapters\Abstract the adapter class
	 */
	public function adapter($adapter)
	{
		$classname = "\\LimberRecord\\Adapters\\" . ucfirst($adapter);
		
		if (class_exists($classname)) {
			return new $classname;
		}
		
		foreach ($this->adapters_path as $path) {
			$adapter_path = $path . "/" . $adapter . ".php";
			
			if (file_exists($adapter_path)) {
				require_once $adapter_path;
				
				if (class_exists($classname)) {
					return new $classname;
				}
			}
		}
		
		throw new InvalidAdapterException("Adapter $adapter not found.");
	}
}

class InvalidAdapterException extends \Exception {}
