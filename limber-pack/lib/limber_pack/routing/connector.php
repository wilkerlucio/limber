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

class Connector extends \LimberSupport\DynamicObject
{
	public $router;
	
	public function __construct($router)
	{
		$this->router = $router;
	}
	
	public function connect()
	{
		$args = func_get_args();
		
		return call_user_func_array(array($this->router, "connect"), $args);
	}
}

Connector::define_ghost_method(function($object, $method, $args) {
	array_unshift($args, $method);
	
	return call_user_func_array(array($object->router, "connect_named_route"), $args);
});
