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

require_once "limber_support.php";

describe("String support", function($spec) {
	$spec->context("dealing with accents", function($spec) {
		$spec->context("acuting vogals", function($spec) {
			$spec->it("should acute a vogal", function($spec, $data) {
				$spec(str_acute("e Eo"))->should->be("é Éó");
			});
			
			$spec->it("should keep previous acuted vogals", function($spec, $data) {
				$spec(str_acute("éo"))->should->be("éó");
			});
		});
		
		$spec->it("should replace the characteres with accents to without accents ones", function($spec, $data) {
			$spec(str_transliterate("aéi õÔ"))->should->be("aei oO");
		});
	});
	
	$spec->context("camelizing string", function($spec) {
		$spec->it("should keep string if no underscore", function($spec) {
			$spec(str_camelize("String"))->should->be("String");
		});
		
		$spec->it("should uppercase first letter", function($spec) {
			$spec(str_camelize("string"))->should->be("String");
		});
		
		$spec->it("should camelize when found underscores", function($spec) {
			$spec(str_camelize("underscore_splited_string"))->should->be("UnderscoreSplitedString");
		});
		
		$spec->it("should accept a second parameter to put first letter in downcase", function($spec, $data) {
			$spec(str_camelize("some_string", false))->should->be("someString");
		});
		
		$spec->it("should get namespaces back", function($spec, $data) {
			$spec(str_camelize("some_string/pack"))->should->be("SomeString\\Pack");
		});
	});
	
	$spec->context("underscoring string", function($spec) {
		$spec->it("should keep string if no camelcase", function($spec) {
			$spec(str_underscore("string"))->should->be("string");
		});
		
		$spec->it("should lowercase result", function($spec) {
			$spec(str_underscore("String"))->should->be("string");
		});
		
		$spec->it("should underscore camelcased strings", function($spec) {
			$spec(str_underscore("CamelCasedString"))->should->be("camel_cased_string");
		});
		
		$spec->it("should serialize namespaces to paths", function($spec, $data) {
			$spec(str_underscore("Camel\\CasedClass"))->should->be("camel/cased_class");
		});
	});
	
	$spec->context("testing if a string is empty", function($spec) {
		$spec->it("should return true for empty string", function($spec) {
			$spec(str_is_empty(""))->should->be(true);
		});
		
		$spec->it("should return true for string containing only tabs and spaces", function($spec) {
			$spec(str_is_empty("  \t"))->should->be(true);
		});
		
		$spec->it("should return false for strings with content", function($spec) {
			$spec(str_is_empty(" some content"))->should->be(false);
		});
	});
	
	$spec->context("pluralizing strings", function($spec) {
		$spec->it("should pluralize regular cases", function($spec, $data) {
			$spec(str_pluralize("post"))->should->be("posts");
		});
		
		$spec->it("should pluralize irregular cases", function($spec, $data) {
			$spec(str_pluralize("person"))->should->be("people");
		});
		
		$spec->it("should not pluralize when it is an uncountable word", function($spec, $data) {
			$spec(str_pluralize("sheep"))->should->be("sheep");
		});
		
		$spec->it("should pluralize only last string when dealing with composed words", function($spec, $data) {
			$spec(str_pluralize("CamelOctopus"))->should->be("CamelOctopi");
		});
	});
	
	$spec->context("singularizing strings", function($spec) {
		$spec->it("should singularize regular cases", function($spec, $data) {
			$spec(str_singularize("posts"))->should->be("post");
		});
		
		$spec->it("should singularize irregular cases", function($spec, $data) {
			$spec(str_singularize("people"))->should->be("person");
		});
		
		$spec->it("should not singularize when it is an uncountable word", function($spec, $data) {
			$spec(str_singularize("sheep"))->should->be("sheep");
		});
		
		$spec->it("should singularize only last string when dealing with composed words", function($spec, $data) {
			$spec(str_singularize("CamelOctopi"))->should->be("CamelOctopus");
		});
	});
	
	$spec->context("tableizing strings", function($spec) {
		$spec->it("should underscore and pluralize string", function($spec, $data) {
			$spec(str_tableize("UserAttribute"))->should->be("user_attributes");
		});
	});
	
	$spec->context("classifing strings", function($spec) {
		$spec->it("should camel case and singularize string", function($spec, $data) {
			$spec(str_classify("user_attributes"))->should->be("UserAttribute");
		});
	});
	
	$spec->context("squeezing strings", function($spec) {
		$spec->it("should remove repeated characteres in sequence", function($spec, $data) {
			$spec(str_squeeze("my--string-with---many-repeated----separators", "-"))->should->be("my-string-with-many-repeated-separators");
		});
	});
	
	$spec->context("dasherizing words", function($spec) {
		$spec->it("should dasherize word", function($spec, $data) {
			$spec(str_dasherize("my_underscored_string"))->should->be("my-underscored-string");
		});
	});
	
	$spec->context("humanizing strings", function($spec) {
		$spec->it("should turn into a pretty string", function($spec, $data) {
			$spec(str_humanize("some_string"))->should->be("Some string");
		});
		
		$spec->it("should remove _id from the end of string", function($spec, $data) {
			$spec(str_humanize("author_id"))->should->be("Author");
		});
	});
	
	$spec->context("get foreign key", function($spec) {
		$spec->it("should get the foreign key from class", function($spec, $data) {
			$spec(str_foreign_key("Person"))->should->be("person_id");
		});
		
		$spec->it("should remove the underline separator if user asks to", function($spec, $data) {
			$spec(str_foreign_key("Person", false))->should->be("personid");
		});
		
		$spec->it("should demodulize class name", function($spec, $data) {
			$spec(str_foreign_key("Models\Person"))->should->be("person_id");
		});
	});
	
	$spec->context("ordinalizing strings", function($spec) {
		$spec->it("should return Nst if number ends with 1", function($spec, $data) {
			$spec(str_ordinalize("1001"))->should->be("1001st");
		});
		
		$spec->it("should return Nnd if number ends with 2", function($spec, $data) {
			$spec(str_ordinalize("1022"))->should->be("1022nd");
		});
		
		$spec->it("should return Nrd if number ends with 3", function($spec, $data) {
			$spec(str_ordinalize("12333"))->should->be("12333rd");
		});
		
		$spec->it("should return Nth if number ends with 11, 12 or 13", function($spec, $data) {
			$spec(str_ordinalize("1011"))->should->be("1011th");
			$spec(str_ordinalize("1012"))->should->be("1012th");
			$spec(str_ordinalize("1013"))->should->be("1013th");
		});
		
		$spec->it("should return Nth if numbers ends with 4, 5, 6, 7, 8 or 9", function($spec, $data) {
			$spec(str_ordinalize(104))->should->be("104th");
			$spec(str_ordinalize(105))->should->be("105th");
			$spec(str_ordinalize(106))->should->be("106th");
			$spec(str_ordinalize(107))->should->be("107th");
			$spec(str_ordinalize(108))->should->be("108th");
			$spec(str_ordinalize(109))->should->be("109th");
		});
	});
	
	$spec->context("demodulizing strings", function($spec) {
		$spec->it("should keep class name if its not namespaced", function($spec, $data) {
			$spec(str_demodulize("Person"))->should->be("Person");
		});
		
		$spec->it("should remove the namespaces from class name", function($spec, $data) {
			$spec(str_demodulize("Models\\Person"))->should->be("Person");
		});
	});
	
	$spec->context("getting class as array key", function($spec) {
		$spec->it("should remove de \\ notation", function($spec, $data) {
			$spec(str_class_key("My\\Namespace"))->should->be("My_Namespace");
		});
	});
	
	$spec->context("parameterizing strings", function($spec) {
		$spec->it("should keep a clean string to use into url", function($spec, $data) {
			$spec(str_parameterize("My Complex  é_string"))->should->be("my-complex-e_string");
		});
		
		$spec->it("should accept a custom separator", function($spec, $data) {
			$spec(str_parameterize(" My Complex  é_string", "_"))->should->be("my_complex_e_string");
		});
	});
});
