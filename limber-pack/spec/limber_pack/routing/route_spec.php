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
			$spec(function() { new Route("route", array()); })->should->throw("LimberPack\Routing\InvalidRouteException");
		});
		
		$spec->it("should throw a InvalidRouteException if the action is not defined", function($spec, $data) {
			$spec(function() { new Route("route", array("controller" => "main")); })->should->throw("LimberPack\Routing\InvalidRouteException");
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
			$spec(function() use($data) { $data->route->controller; })->should->throw("LimberPack\Routing\RouteNotAssignedException");
		});
		
		$spec->it("should throw a RouteNotAssignedException is the route was not assigned and try to get action", function($spec, $data) {
			$spec(function() use($data) { $data->route->action; })->should->throw("LimberPack\Routing\RouteNotAssignedException");
		});
		
		$spec->it("should throw a RouteNotAssignedException is the route was not assigned and try to get params", function($spec, $data) {
			$spec(function() use($data) { $data->route->params; })->should->throw("LimberPack\Routing\RouteNotAssignedException");
		});
	});
	
	$spec->context("creating route matcher", function($spec) {
		$spec->it("should create route matcher", function($spec, $data) {
			$route = new Route(":action", array("controller" => "main"));
		
			$spec($route->create_route_matcher())->should->be("/^([a-z0-9_]+)$/i");
		});
		
		$spec->it("should keep static names as quoted regex", function($spec, $data) {
			$route = new Route("my_route/complex", array("controller" => "main", "action" => "index"));
			
			$spec($route->create_route_matcher())->should->be("/^my_route\/complex$/i");
		});
		
		$spec->it("should create route matcher with many variable paths", function($spec, $data) {
			$route = new Route(":controller/:action", array("controller" => "main"));
		
			$spec($route->create_route_matcher())->should->be("/^([a-z0-9_]+)\/([a-z0-9_]+)$/i");
		});
		
		$spec->it("should create route matcher with many variable paths and statics", function($spec, $data) {
			$route = new Route(":controller/some/:action", array("controller" => "main"));
		
			$spec($route->create_route_matcher())->should->be("/^([a-z0-9_]+)\/some\/([a-z0-9_]+)$/i");
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
			
			$spec($route->map_param_names())->should->be(array(array("controller", ":"), array("id", ":")));
		});
		
		$spec->it("should replace de params correctly", function($spec, $data) {
			$route = new Route(":controller/:action/:id");
			$route->match("users/view/5");
			
			$spec($route->controller)->should->be("users");
			$spec($route->action)->should->be("view");
			$spec($route->params["id"])->should->be("5");
		});
		
		$spec->it("should not match if the matcher dont pass", function($spec, $data) {
			$route = new Route(":action", array("controller" => "my_controller"));
			
			$spec($route->match("any/action"))->should->be(false);
		});

		$spec->context("using default parameters", function($spec) {
			$spec->it("should accepts default parameters", function($spec, $data) {
				$route = new Route("photos/:id", array("controller" => "photos", "action" => "show", "defaults" => array("format" => "jpg")));
				$route->match("photos/5");
				
				$spec($route->params["format"])->should->be("jpg");
			});
		});
		
		$spec->it("should not match if requirements don't pass", function($spec, $data) {
			$route = new Route(":action", array("controller" => "main", "requirements" => array("action" => "/.*_path/")));
			
			$spec($route->match("some"))->should->be(false);
		});
		
		$spec->it("should match globbing routes", function($spec, $data) {
			$route = new Route("photos/*other", array("controller" => "photos", "action" => "unknown"));
			$route->match("photos/some/other/values");
			
			$spec($route->params["other"])->should->be(array("some", "other", "values"));
		});
	});
	
	$spec->context("generating uri", function($spec) {
		$spec->context("checking for params", function($spec) {
			$spec->it("should return true if the route can parse the params with dynamic params", function($spec, $data) {
				$route = new Route(":controller/:action");
				$spec($route->support_params(array("controller" => "main", "action" => "index")))->should->be(true);
			});
			
			$spec->it("should return true if the params are in defaults of route", function($spec, $data) {
				$route = new Route("my/route", array("controller" => "main", "action" => "index"));
				$spec($route->support_params(array("controller" => "main", "action" => "index")))->should->be(true);
			});

			$spec->it("should return false if the params doesn't supports", function($spec, $data) {
				$route = new Route("my/route", array("controller" => "products", "action" => "index"));
				$spec($route->support_params(array("controller" => "main", "action" => "index")))->should->be(false);
			});
			
			$spec->it("should return with mixed params place", function($spec, $data) {
				$route = new Route("my/:action", array("controller" => "main", "id" => "some"));
				$spec($route->support_params(array("controller" => "main", "action" => "other")))->should->be(true);
			});
			
			$spec->it("should return false if route needs params that aren't in requested params", function($spec, $data) {
				$route = new Route(":controller/:action/:id");
				$spec($route->support_params(array("controller" => "main", "action" => "other")))->should->be(false);
			});
		});
		
		$spec->context("generating", function($spec) {
			$spec->it("should replace params", function($spec, $data) {
				$route = new Route(":controller/view/:action");
				$spec($route->generate_for(array("controller" => "main", "action" => "index")))->should->be("main/view/index");
			});
			
			$spec->it("should add extra params if it isn't presente at raw of route", function($spec, $data) {
				$route = new Route(":controller/view/:action");
				$spec($route->generate_for(array("controller" => "main", "action" => "index", "page" => "5")))->should->be("main/view/index?page=5");
			});
			
			$spec->it("should not add extra value if its in defaults", function($spec, $data) {
				$route = new Route("test", array("controller" => "main", "action" => "test", "defaults" => array("format" => "png")));
				$spec($route->generate_for(array("controller" => "main", "action" => "test", "format" => "png", "page" => "2")))->should->be("test?page=2");
			});
			
			$spec->it("should works with similar names", function($spec, $data) {
				$route = new Route(":controller/view/:action/:cont");
				$spec($route->generate_for(array("cont" => "some", "controller" => "main", "action" => "index")))->should->be("main/view/index/some");
			});
			
			$spec->it("should return null if doesnt matches", function($spec, $data) {
				$route = new Route("users/:id", array("controller" => "users", "action" => "show"));
				$spec($route->generate_for(array("controller" => "users", "action" => "other")))->should->be(null);
			});
			
			$spec->it("should correct replace glob elements", function($spec, $data) {
				$route = new Route("photos/*other", array("controller" => "photos", "action" => "unknown"));
				$spec($route->generate_for(array("controller" => "photos", "action" => "unknown", "other" => array("some", "more", "items"))))->should->be("photos/some/more/items");
			});
		});
	});
});
