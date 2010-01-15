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

namespace LimberPack\Routing;

require_once "limber_pack/routing/route.php";

class Base extends \LimberSupport\DynamicObject
{
	public $_routes = array();
	public $_default_action = "index";
	public $_default_format = "html";
	
	/**
	 * Get current routes
	 */
	public function routes()
	{
		return $this->_routes;
	}
	
	/**
	 * Starts the route drawing
	 *
	 * This method is a simple alias to avoid long class name rewrite
	 */
	public function draw($mapper)
	{
		$mapper($this);
	}
	
	/**
	 * Defines a new route
	 */
	public function connect($route_name, $options)
	{
		$this->_routes[] = new Route($route_name, $options);
	}
	
	/**
	 * Try to match a route string with defined routes
	 */
	public function match($route_string)
	{
		foreach ($this->_routes as $route) {
			if ($route->match($route_string)) {
				return $route;
			}
		}
	}
}

Base::attr_accessor("default_action", "default_format");
