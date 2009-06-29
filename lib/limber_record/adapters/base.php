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
 * This file provides the base class for database adapters and adapters
 * exceptions
 *
 * @package LimberRecord
 * @subpackage Adapters
 */

namespace LimberRecord\Adapters;

/**
 * This class provides de basic abstraction for creating a database adapter
 *
 * In order to create a database adapter you should extend for this class
 * and implement the abstract methods to do the real database work
 *
 * See the documentation at each abstract method to get details on how it
 * may be implemented.
 *
 * @abstract
 */
abstract class Base
{
	private $host;
	private $user;
	private $password;
	private $database;
	
	private $connected;
	
	//delay connection methods
	public function connect($host, $user, $password)
	{
		$this->host = $host;
		$this->user = $user;
		$this->password = $password;
	}
	
	public function select_db($database)
	{
		$this->database = $database;
		
		if ($this->connected) {
			if(!$this->_select_db($database)) {
				throw new DatabaseSelectException("Unable to select database $database");
			}
		}
	}
	
	/**
	 * Force the connection to database
	 *
	 * By default, the connections are delayed, it can improve the script
	 * performance by avoiding connections if the connection is not really
	 * needed. Use this method if you need to force the connection to be made.
	 */
	public function force_connection()
	{
		if (!$this->connected) {
			$this->connected = $this->_connect($this->host, $this->user, $this->password);
			
			if (!$this->connected) {
				throw new ConnectionException("Unable to connect to {$this->host} with user {$this->user}", $this->host, $this->user, $this->password);
			}
			
			if ($this->database) $this->select_db($this->database);
		}
	}
	
	//selection methods
	/**
	 * Wraps internal select method
	 *
	 * @see _select
	 */
	public function select($sql)
	{
		$this->force_connection();
		
		return $this->_select($sql);
	}
	
	/**
	 * Do a select and return first row
	 *
	 * @param string $sql sql statement
	 * @return array associative array with given data
	 */
	public function select_row($sql)
	{
		$data = $this->select($sql);
		
		return reset($data);
	}
	
	/**
	 * Do a select_row and return first item
	 *
	 * @param string $sql sql statement
	 * @return string cell data
	 */
	public function select_cell($sql)
	{
		$data = $this->select_row($sql);
		
		return reset($data);
	}
	
	//update methods
	/**
	 * Wraps internal _insert method
	 *
	 * @see _insert
	 */
	public function insert($sql)
	{
		$this->force_connection();
		
		return $this->_insert($sql);
	}
	
	/**
	 * Wraps internal _update method
	 *
	 * @see _update
	 */
	public function update($sql)
	{
		$this->force_connection();
		
		return $this->_update();
	}
	
	//connection methods
	/**
	 * Connect to database
	 *
	 * This method does the real connection with database, remember that the
	 * connection will be delayed, the real connection (this method) is only
	 * made when the user really request an sql from server.
	 *
	 * You should return true or false (true when connection occurs without
	 * problems, false otherwise)
	 *
	 * @param string $host hostname for connection
	 * @param string $user username for connection
	 * @param string $password password for connection
	 * @return boolean
	 */
	protected abstract function _connect($host, $user, $password);
	
	/**
	 * Select database
	 *
	 * This method does the internal database selection, like the connection this
	 * method is also delayed to be executed only when a query is needed.
	 *
	 * @param string $database database name
	 * @return boolean
	 */
	protected abstract function _select_db($database);
	
	//scheme methods
	protected abstract function _create_table($table_name, $fields_description);
	protected abstract function _drop_table($table_name);
	
	//common methods
	/**
	 * Execute an sql statement
	 *
	 * This method should execute the given sql at database, you don't need
	 * to return anything, but you can return anything you want to use at
	 * internal level of adapter.
	 *
	 * In order to robust the adapter, this method should throw a
	 * LimberRecord\Adapters\QueryException when the sql statement fails
	 *
	 * @param string $sql sql statement to be executed
	 * @throws LimberRecord\Adapters\QueryException when occur and error at statement
	 */
	protected abstract function _execute($sql);
	
	/**
	 * Select data from database
	 *
	 * This method should execute a given sql and return a table-like array
	 * with the result data, for exemple:
	 *
	 * <code>
	 * array (
	 *   array (
	 *     "id" => "5",
	 *     "name" => "user name",
	 *     "created_at" => "2009-03-14"
	 *   ),
	 *   array (
	 *     "id" => "7",
	 *     "name" => "other name",
	 *     "created_at" => "2009-03-21"
	 *   ),
	 * )
	 * </code>
	 *
	 * @param string $sql select sql statement
	 * @return array
	 */
	protected abstract function _select($sql);
	
	/**
	 * Insert data into database
	 *
	 * This method should execute a given sql and return the insertion id of
	 * element at table level (the current id of inserted element)
	 *
	 * @param string $sql insert sql statement
	 * @return string the insert id, despite this param should be a string,
	 *                usually this will be a string containg a number
	 */
	protected abstract function _insert($sql);
	
	/**
	 * Update datarow
	 *
	 * This method should execute a given sql and return the number of affected
	 * rows at table
	 *
	 * @param string $sql
	 * @return integer the number of affected rows
	 */
	protected abstract function _update($sql);
	
	//transaction methods
	protected abstract function _transaction_begin();
	protected abstract function _transaction_commit();
	protected abstract function _transaction_rollback();
}

/**
 * Connection Exception
 *
 * This exception should be used when problems occur when connecting to a
 * database
 */
class ConnectionException extends \Exception
{
	public $host;
	public $user;
	public $password;
	
	public function __construct($message, $host, $user, $password)
	{
		parent::__construct($message);
		
		$this->host = $host;
		$this->user = $user;
		$this->password = $password;
	}
}

/**
 * Query Exception
 *
 * This exception should be used when problems occur when executing sql
 * statements
 */
class QueryException extends \Exception
{
	public $sql;
	
	public function __construct($message, $sql)
	{
		parent::__construct($message);
		
		$this->sql = $sql;
	}
}

/**
 * Database Select Exception
 *
 * This exception should be used when problems occur when selecting a database
 */
class DatabaseSelectException extends \Exception {}
