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

class Route extends \LimberSupport\DynamicObject
{
	public static $PARAM_MATCHER = "[a-z][a-z0-9_]*";
	
	public $_raw;
	public $_options;
	
	public $_assigned;
	public $_params;
	
	public function __construct($route, $options = array())
	{
		$this->raw = $route;
		
		$this->_options = array_merge(array(
			"controller" => null,
			"action" => null,
			"defaults" => array(),
			"requirements" => array()
		), $options);
		
		$this->_assigned = false;
		$this->_params = array();
		
		$this->_params["controller"] = $options["controller"];
		$this->_params["action"] = $options["action"];
		$this->_params = array_merge($this->_params, $this->_options["defaults"]);
		
		if (!$this->_params["controller"] && !$this->has_url_param("controller")) {
			throw new InvalidRouteException("The controller should be defined in the route");
		}
		
		if (!$this->_params["action"] && !$this->has_url_param("action")) {
			throw new InvalidRouteException("The action should be defined in the route");
		}
	}
	
	public function has_url_param($param)
	{
		$param = preg_quote($param);
		
		return preg_match("/:$param/", $this->raw);
	}
	
	public function controller()
	{
		if (!$this->_assigned) throw new RouteNotAssignedException();
		
		return $this->_params["controller"];
	}
		
	public function action()
	{
		if (!$this->_assigned) throw new RouteNotAssignedException();
		
		return $this->_params["action"];
	}
	
	public function params()
	{
		if (!$this->_assigned) throw new RouteNotAssignedException();
		
		return $this->_params;
	}
	
	public function create_route_matcher()
	{
		$route = preg_quote($this->raw, "/");
		$route = preg_replace("/\\\\:" . static::$PARAM_MATCHER . "/i", "([a-z0-9_]+)", $route);
		
		return "/^$route$/i";
	}
	
	public function map_param_names()
	{
		$params = array();
		
		if (preg_match_all("/:(" . static::$PARAM_MATCHER . ")/", $this->raw, $matches)) {
			array_append($params, $matches[1]);
		}
		
		return $params;
	}
	
	public function match($route_string)
	{
		$route_matcher = $this->create_route_matcher();
		
		if (preg_match($route_matcher, $route_string, $matches)) {
			
			$param_order = $this->map_param_names();
			
			foreach ($param_order as $key => $value) {
				$this->_params[$value] = $matches[$key + 1];
			}
			
			foreach ($this->_options["requirements"] as $key => $value) {
				if (!preg_match($value, $this->_params[$key])) {
					return false;
				}
			}
			
			$this->_assigned = true;
			
			return true;
		}
		
		return false;
	}
}

Route::attr_accessor("raw");

class RouteNotAssignedException extends \Exception {}
class InvalidRouteException extends \Exception {}