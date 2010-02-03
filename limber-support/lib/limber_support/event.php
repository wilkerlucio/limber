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
 * Event provides a easy way to collect and invoke methods
 *
 * Event Class is designed to be used into objects as collection of listeners
 */
class Event
{
	private $listeners;
	
	/**
	 * the default params of listeners
	 */
	public $default_params;
	
	/**
	 * Construct a new Event
	 *
	 * If you pass any arguments into construct, this arguments will be used
	 * as default arguments for the listeners call, the calling of listeners
	 * will use the default params as first params, and then will add the
	 * called params
	 *
	 * @param mixed $args,...
	 */
	public function __construct()
	{
		$this->default_params = func_get_args();
		$this->listeners = array();
	}
	
	/**
	 * Add a new listener to the event
	 *
	 * @param function $listener
	 */
	public function add_listener($listener)
	{
		$this->listeners[] = $listener;
		
		return $this;
	}
	
	/**
	 * Get the list of functions assigned to this event
	 *
	 * @return array
	 */
	public function listeners()
	{
		return $this->listeners;
	}
	
	/**
	 * Call the listeners of event, all params passed to this method will be
	 * redirected to the listeners
	 *
	 * @param mixed $args,...
	 */
	public function call()
	{
		$args = func_get_args();
		
		return $this->call_array($args);
	}
	
	/**
	 * Call the listeners passing an array of parameters, this parameters
	 * will be expanded into listeners call
	 *
	 * @param array $args
	 */
	public function call_array($args = array())
	{
		array_invoke_array($this->listeners, null, array_add($this->default_params, $args));
		
		return $this;
	}
	
	/**
	 * Some suggar for call method
	 *
	 * @see call
	 */
	public function __invoke()
	{
		$args = func_get_args();
		
		return $this->call_array($args);
	}
}
