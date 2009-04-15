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

/**
 * Add acute accent at vogals
 *
 * note: you should use utf8 encoded strings to this function works
 *
 * @param string $string string to be acuted
 * @return string
 */
function str_acute($string)
{
	$normal = "aeiouAEIOU";
	$acuted = "áéíóúÁÉÍÓÚ";
	
	return utf8_encode(strtr($string, $normal, utf8_decode($acuted)));
}

/**
 * Remove accents from string and replace with relative characteres without
 * accents
 *
 * note: you should use utf8 encoded strings to this function works
 *
 * @param string $string string to remove accents
 * @return string
 */
function str_remove_accents($string)
{
	$with_accents    = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
	$without_accents = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYbsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
	
	return strtr(utf8_decode($string), utf8_decode($with_accents), $without_accents);
}

/**
 * Get the pluralized version of a string following brazilian portuguese
 * pluralization rules
 *
 * @param string $string string to be pluralized
 * @return string
 */
function str_pluralize_br($string)
{
	if (preg_match("/[rz]$/i", $string)) {
		return $string . 'es';
	}

	if (preg_match("/[au]l$/i", $string)) {
		return substr($string, 0, -1) . 'is';
	}

	if (preg_match("/([eo])l$/i", $string, $matches)) {
		return substr($string, 0, -2) . str_acute($matches[1]) . 'is';
	}
	
	if (preg_match("/m$/i", $string)) {
		return substr($string, 0, -1) . 'ns';
	}
	
	if (preg_match("/x$/i", $string)) {
		return $string;
	}
	
	if (preg_match("/en$/i", $string)) {
		$string = str_remove_accents($string);
	}
	
	return $string . 's';
}
