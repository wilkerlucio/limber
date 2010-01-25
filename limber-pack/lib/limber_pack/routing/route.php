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

namespace LimberPack\Routing;

class Route extends \LimberSupport\DynamicObject
{
	public static $PARAM_MATCHER = "[a-z][a-z0-9_]*";
	
	public $raw;
	public $options;
	public $assigned;
	
	public $_params;
	
	public function __construct($route, $options = array())
	{
		$this->raw = $route;
		
		$this->options = array_merge(array(
			"controller" => null,
			"action" => null,
			"defaults" => array(),
			"requirements" => array()
		), $options);
		
		$this->assigned = false;
		$this->_params = array();
		
		$this->options["defaults"]["controller"] = $options["controller"];
		$this->options["defaults"]["action"] = $options["action"];
		
		$this->_params = array_merge($this->_params, $this->options["defaults"]);
		
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
		if (!$this->assigned) throw new RouteNotAssignedException();
		
		return $this->_params["controller"];
	}
		
	public function action()
	{
		if (!$this->assigned) throw new RouteNotAssignedException();
		
		return $this->_params["action"];
	}
	
	public function params()
	{
		if (!$this->assigned) throw new RouteNotAssignedException();
		
		return $this->_params;
	}
	
	public function create_route_matcher()
	{
		$route = preg_quote($this->raw, "/");
		$route = preg_replace("/\\\\:" . static::$PARAM_MATCHER . "/i", "([a-z0-9_]+)", $route);
		$route = preg_replace("/\\\\\\*" . static::$PARAM_MATCHER . "/i", "(.+)", $route);
		
		return "/^$route$/i";
	}
	
	public function map_param_names()
	{
		$params = array();
		
		if (preg_match_all("/(:|\\*)(" . static::$PARAM_MATCHER . ")/", $this->raw, $matches)) {
			array_append($params, array_zip($matches[2], $matches[1]));
		}
		
		return $params;
	}
	
	public function match($route_string)
	{
		$route_matcher = $this->create_route_matcher();
		
		if (preg_match($route_matcher, $route_string, $matches)) {
			$param_order = $this->map_param_names();
			
			foreach ($param_order as $key => $value) {
				$this->_params[$value[0]] = $value[1] == '*' ? explode("/", $matches[$key + 1]) : $matches[$key + 1];
			}
			
			foreach ($this->options["requirements"] as $key => $value) {
				if (!preg_match($value, $this->_params[$key])) {
					return false;
				}
			}
			
			$this->assigned = true;
			
			return true;
		}
		
		return false;
	}
	
	public function support_params($params)
	{
		$important_params = array("controller", "action");
		$variable_params = array_map_key($this->map_param_names(), 0);
		$options = $this->options;
		
		foreach ($variable_params as $required_param) {
			if (!isset($params[$required_param])) return false;
		}
		
		return array_all($important_params, function($param) use ($params, $variable_params, $options) {
			if (in_array($param, $variable_params) || $options[$param] == $params[$param]) {
				return true;
			}
			
			return false;
		});
	}
	
	public function generate_for($params)
	{
		if (!$this->support_params($params)) return null;
		
		krsort($params); // ensure bigger keys will be matched before
		
		$qs_params = array();
		$route = $this->raw;
		$route_params = array_map_key($this->map_param_names(), 0);
		
		foreach ($params as $key => $value) {
			if (is_array($value)) {
				$value_enc = implode("/", array_map("urlencode", $value));
				$mark = "*";
			} else {
				$value_enc = urlencode($value);
				$mark = ":";
			}
			
			if (in_array($key, $route_params)) {
				$route = str_replace($mark . $key, $value_enc, $route);
			} else {
				if (array_get($this->options["defaults"], $key, null) != $value) {
					$qs_params[] = urlencode($key) . "=" . $value_enc;
				}
			}
		}
		
		if (count($qs_params) > 0) {
			$route .= "?" . implode("&", $qs_params);
		}
		
		return $route;
	}
}

class RouteNotAssignedException extends \Exception {}
class InvalidRouteException extends \Exception {}