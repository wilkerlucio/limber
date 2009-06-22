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

require_once dirname(__FILE__) . "/../../lib/limber_support/loaders.php";

describe("Loaders support", function($spec) {
	$spec->context("using path_autoloader", function($spec) {
		$spec->before_all(function($data) {
			$data->loader = path_autoloader(dirname(__FILE__) . '/loaders_dummy_classes');
		});
		
		$spec->it("should load classes at root path", function($spec, $data) {
			$loader = $data->loader;
			
			$spec($loader("Dummy"))->should->be(true);
		});
		
		$spec->it("should load nested class", function($spec, $data) {
			$loader = $data->loader;
			
			$spec($loader("Package_OtherDummy"))->should->be(true);
		});
	});
	
	$spec->context("using require_dir", function($spec) {
		$spec->it("should load all classes into nested directories", function($spec, $data) {
			require_dir(dirname(__FILE__) . '/loaders_dummy_classes');
			
			$spec(class_exists("Package_Sub_MoreDummy"))->should->be(true);
		});
	});
});
