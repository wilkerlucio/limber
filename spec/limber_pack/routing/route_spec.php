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

require_once "limber_pack.php";

use LimberPack\Routing\Route;

describe("LimberPack Route", function($spec) {
	$spec->context("creating a new route", function($spec) {
		$spec->it("should accepts the raw route and the options in constructor", function($spec, $data) {
			$route = new Route("route", array("controller" => "main", "action" => "index"));
			
			$spec(get_class($route))->should->be("LimberPack\Routing\Route");
		});
		
		$spec->it("should get the current raw route", function($spec, $data) {
			$route = new Route("route", array("controller" => "main", "action" => "index"));
			
			$spec($route->raw)->should->be("route");
		});
		
		$spec->it("should throw a InvalidRouteException if the controller is not defined", function($spec, $data) {
			try {
				$route = new Route("route", array());
				
				$spec(true)->should->be(false);
			} catch (LimberPack\Routing\InvalidRouteException $e) {
				$spec(true)->should->be(true);
			}
		});
		
		$spec->it("should throw a InvalidRouteException if the action is not defined", function($spec, $data) {
			try {
				$route = new Route("route", array("controller" => "main"));
				
				$spec(true)->should->be(false);
			} catch (LimberPack\Routing\InvalidRouteException $e) {
				$spec(true)->should->be(true);
			}
		});
		
		$spec->it("should accept the controller and action as url params", function($spec, $data) {
			$route = new Route(":controller/:action");

			$spec(get_class($route))->should->be("LimberPack\Routing\Route");
		});
	});
	
	$spec->context("getting not assigned route params", function($spec) {
		$spec->before_each(function($data) {
			$data->route = new Route("route", array("controller" => "main", "action" => "index"));
		});
		
		$spec->it("should throw a RouteNotAssignedException is the route was not assigned and try to get controller", function($spec, $data) {
			try {
				$data->route->controller;
			} catch (LimberPack\Routing\RouteNotAssignedException $e) {
				$spec(true)->should->be(true);
			}
		});
		
		$spec->it("should throw a RouteNotAssignedException is the route was not assigned and try to get action", function($spec, $data) {
			try {
				$data->route->action;
			} catch (LimberPack\Routing\RouteNotAssignedException $e) {
				$spec(true)->should->be(true);
			}
		});
		
		$spec->it("should throw a RouteNotAssignedException is the route was not assigned and try to get params", function($spec, $data) {
			try {
				$data->route->params;
			} catch (LimberPack\Routing\RouteNotAssignedException $e) {
				$spec(true)->should->be(true);
			}
		});
	});
	
	$spec->context("creating route matcher", function($spec) {
		$spec->it("should create route matcher", function($spec, $data) {
			$route = new Route(":action", array("controller" => "main"));
		
			$spec($route->create_route_matcher())->should->be("/([a-z0-9_]+)/i");
		});
		
		$spec->it("should keep static names as quoted regex", function($spec, $data) {
			$route = new Route("my_route/complex", array("controller" => "main", "action" => "index"));
			
			$spec($route->create_route_matcher())->should->be("/my_route\/complex/i");
		});
	});
	
	$spec->context("assigning the route", function($spec) {
		$spec->it("should assign and return true if the route matches", function($spec, $data) {
			$route = new Route("my/route", array("controller" => "mycon", "action" => "some_action"));
			
			$spec($route->match("my/route"))->should->be(true);
			$spec($route->controller)->should->be("mycon");
			$spec($route->action)->should->be("some_action");
		});
		
		$spec->it("should accept variable params", function($spec, $data) {
			$route = new Route(":action", array("controller" => "main"));
			$route->match("some");
			
			$spec($route->controller)->should->be("main");
			$spec($route->action)->should->be("some");
		});
		
		$spec->it("should map the params array", function($spec, $data) {
			$route = new Route(":controller/view/:id", array("action" => "show"));
			
			$spec($route->map_param_names())->should->be(array("controller", "id"));
		});
		
		$spec->it("should replace de params correctly", function($spec, $data) {
			$route = new Route(":controller/:action/:id");
			$route->match("users/view/5");
			
			$spec($route->controller)->should->be("users");
			$spec($route->action)->should->be("view");
			$spec($route->params["id"])->should->be("5");
		});
	});
});
