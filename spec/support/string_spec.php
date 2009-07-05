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
});
