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

namespace LimberSupport;

require_once "limber_support.php";

class ClassAncestorIteratorTestA {}
class ClassAncestorIteratorTestB extends ClassAncestorIteratorTestA {}
class ClassAncestorIteratorTestC extends ClassAncestorIteratorTestB {}

describe("Class Ancestors Iterator", function($spec) {
	$spec->it("should iterate over class anscestors", function($spec, $data) {
		$iterator = new ClassAncestorsIterator("LimberSupport\ClassAncestorIteratorTestC");
		$data = array();
		
		foreach ($iterator as $class) {
			$data[] = $class;
		}
		
		$spec($data)->should->be(array(
			"LimberSupport\ClassAncestorIteratorTestC",
			"LimberSupport\ClassAncestorIteratorTestB",
			"LimberSupport\ClassAncestorIteratorTestA"
		));
	});
});
