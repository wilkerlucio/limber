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

namespace LimberRecord;

require_once dirname(__FILE__) . "/../../lib/limber_record.php";

describe("connection manager", function($spec) {
	$spec->context("getting adapter", function($spec) {
		$spec->it("should find a valid adapter", function($spec) {
			$adapter = Manager::instance()->adapter("mysql");
			
			$spec($adapter)->should_not->be(null);
		});
		
		$spec->it("should throw a LimberRecord_InvalidAdapterException when trying to get a invalid adapter", function($spec) {
			try {
				$adapter = Manager::instance()->adapter("invalid_adapter");
				
				$spec(true)->should->be(false);
			} catch(InvalidAdapterException $e) {
				$spec(true)->should->be(true);
			}
		});
	});
	
	$spec->it("should connect to a valid database", function($spec) {
		$connection = Manager::instance()->connect("mock", "host", "user", "password");
		
		$spec($connection)->should_not->be(null);
	});
});

//mocks
namespace LimberRecord\Adapters;

class Mock
{
	public function connect($host, $user, $password)
	{
		
	}
}
