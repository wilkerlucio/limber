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

use LimberPack\Routing\Base as Router;

describe("Routing Base", function($spec) {
	$spec->context("mapping new routes", function($spec) {
		$spec->before_each(function($data) {
			$data->router = new Router();
		});
		
		$spec->it("should map routes with connect method", function($spec, $data) {
			$data->router->draw(function($map) {
				$map->connect("some_route", array("controller" => "main", "action" => "index"));
			});
			
			$spec(array_map(function($r) { return $r->raw; }, $data->router->routes))->should->include("some_route");
		});
	});
	
	$spec->context("matching routes", function($spec) {
		$spec->before_each(function($data) {
			$data->router = new Router();
			$data->router->draw(function($map) {
				$map->connect("my_route", array("controller" => "main", "action" => "index"));
				$map->connect(":action", array("controller" => "my_controller"));
				$map->connect(":controller/:action");
			});
		});
		
		$spec->it("should discover the controller and action by a given route", function($spec, $data) {
			$route = $data->router->match("my_route");
			
			$spec($route->controller)->should->be("main");
			$spec($route->action)->should->be("index");
		});
		
		$spec->it("should match the action url param", function($spec, $data) {
			$route = $data->router->match("some_action");
			
			$spec($route->raw)->should->be(":action");
		});
		
		$spec->it("should match the controller and action", function($spec, $data) {
			$route = $data->router->match("some/other");
			
			$spec($route->controller)->should->be("some");
			$spec($route->action)->should->be("other");
		});
	});
	
	$spec->context("named routes", function($spec) {
		$spec->it("should create a named route", function($spec, $data) {
			$router = new Router();
			$router->draw(function($map) {
				$map->login("enter", array("controller" => "usersessions", "action" => "new"));
			});
			
			$spec(array_keys($router->named_routes))->should->include("login");
		});
	});
	
	$spec->context("route requirements", function($spec) {
		$spec->before_each(function($data) {
			$data->router = new Router();
			$data->router->draw(function($map) {
				$map->connect(":action", array("controller" => "main", "requirements" => array("action" => "/.*_path/")));
				$map->connect(":action", array("controller" => "other"));
			});
		});
				
		$spec->it("should pass throught if the requirements don't pass", function($spec, $data) {
			$spec($data->router->match("some")->controller)->should->be("other");
		});
		
		$spec->it("should match if requirement pass", function($spec, $data) {
			$spec($data->router->match("some_path")->controller)->should->be("main");
		});
	});
});