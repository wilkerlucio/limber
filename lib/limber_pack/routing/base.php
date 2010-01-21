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
	
	public static function split_optional_routes($route)
	{
		if (preg_match("/\(([^()]+|(?R))*\)/", $route, $matches)) {
			$routes = array();
			
			do {
				$route = preg_replace("/\(([^()]+|(?R))*\)/", "", $route);
				$routes[] = $route;
				$route .= substr($matches[0], 1, -1);
			} while (preg_match("/\(([^()]+|(?R))*\)/", $route, $matches));
			
			$routes[] = $route;
			
			return array_reverse($routes);
		} else {
			return null;
		}
	}
	
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
	public function connect($route, $options = array())
	{
		if ($routes = static::split_optional_routes($route)) {
			$routes = array_map(function($r) use ($options) {
				return new Route($r, $options);
			}, $routes);
			
			array_append($this->routes, $routes);
			
			return $routes;
		} else {
			$this->routes[] = new Route($route, $options);
			
			return $route_obj;
		}
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
	
	/**
	 * Get the best route for a set of params
	 *
	 * @param array $params
	 * @return string
	 */
	public function generate($params)
	{
		foreach ($this->routes as $route) {
			if (!$route->support_params($params)) continue;
			
			return $route->generate_for($params);
		}
		
		throw new RouteNotAvailableException("Can't discover a route for: $params");
	}
}

class RouteNotAvailableException extends \Exception {}
