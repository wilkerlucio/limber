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

require_once dirname(__FILE__) . "/../../lib/support/string.php";

describe("String support", function($spec) {
	$spec->context("dealing with accents", function($spec) {
		$spec->it("should acute a vogal", function($spec, $data) {
			$spec(str_acute("e Eo"))->should->be("é Éó");
		});
		
		$spec->it("should replace the characteres with accents to without accents ones", function($spec, $data) {
			$spec(str_remove_accents("aéi õÔ"))->should->be("aei oO");
		});
	});
	
	$spec->context("inflecting strings", function($spec) {
		$spec->context("brazilian inflector", function($spec) {
			$spec->context("pluralizing strings", function($spec) {
				$spec->it("should add 's' when the string end's with vogals", function($spec, $data) {
					$spec(str_pluralize_br("carro"))->should->be("carros");
					$spec(str_pluralize_br("bola"))->should->be("bolas");
				});
				
				$spec->it("should add 's' when the string end's with ã or ãe", function($spec, $data) {
					$spec(str_pluralize_br("mãe"))->should->be("mães");
					$spec(str_pluralize_br("irmã"))->should->be("irmãs");
				});
				
				$spec->it("should add 's' and remove acentuation when the string end's with en", function($spec, $data) {
					$spec(str_pluralize_br("hífen"))->should->be("hifens");
					$spec(str_pluralize_br("abdômen"))->should->be("abdomens");
				});
				
				$spec->it("should add 'es' when the string end's with 'r' or 'z'", function($spec, $data) {
					$spec(str_pluralize_br("par"))->should->be("pares");
					$spec(str_pluralize_br("paz"))->should->be("pazes");
				});
				
				$spec->it("should change end 'l' to 'is' and remove acentuation when the string end's with 'al', or 'ul'", function($spec, $data) {
					$spec(str_pluralize_br("funeral"))->should->be("funerais");
				});
				
				$spec->it("should change end 'l' to 'is' and acentuate when string end's with 'el' or 'ol'", function($spec, $data) {
					$spec(str_pluralize_br("pastel"))->should->be("pastéis");
					$spec(str_pluralize_br("farol"))->should->be("faróis");
				});
				
				$spec->it("should change end 'm' to 'ns' when the string end's with 'm'", function($spec, $data) {
					$spec(str_pluralize_br("tom"))->should->be("tons");
					$spec(str_pluralize_br("afim"))->should->be("afins");
					$spec(str_pluralize_br("totem"))->should->be("totens");
				});
				
				$spec->it("should not change when the string end's with 'x'", function($spec, $data) {
					$spec(str_pluralize_br("ônix"))->should->be("ônix");
					$spec(str_pluralize_br("tórax"))->should->be("tórax");
				});
			});	
		});
	});
});
