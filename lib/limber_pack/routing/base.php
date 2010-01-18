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

require_once "limber_pack/routing/connector.php";
require_once "limber_pack/routing/route.php";

class Base extends \LimberSupport\DynamicObject
{
	public $routes = array();
	public $named_routes = array();
	
	/**
	 * Get current routes
	 */
	public function routes()
	{
		return $this->routes;
	}
	
	/**
	 * Starts the route drawing
	 *
	 * This method is a simple alias to avoid long class name rewrite
	 */
	public function draw($mapper)
	{
		$mapper(new Connector($this));
	}
	
	/**
	 * Defines a new route
	 */
	public function connect($route_name, $options = array())
	{
		$route = new Route($route_name, $options);
		$this->routes[] = $route;
		
		return $route;
	}
	
	/**
	 * Defines a named route
	 */
	public function connect_named_route($name, $route, $options = array())
	{
		$this->named_routes[$name] = $this->connect($route, $options);
	}
	
	/**
	 * Try to match a route string with defined routes
	 */
	public function match($route_string)
	{
		return array_find($this->routes, function($r) use ($route_string) { return $r->match($route_string); });
	}
}
