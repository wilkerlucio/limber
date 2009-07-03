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
	
	return utf8_encode(strtr(utf8_decode($string), $normal, utf8_decode($acuted)));
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
 * By default, camelize converts strings to UpperCamelCase.
 *
 * <code>
 * str_camelize("limber_record") // => "LimberRecord"
 * </code>
 */
function str_camelize($string)
{
	$pieces = explode("_", $string);
	
	return implode("", array_map(function($piece) {
		return ucfirst($piece);
	}, $pieces));
}

/**
 * The reverse of camelize. Makes an underscored, lowercase form from the expression in the string.
 *
 * <code>
 * str_underscore("LimberRecord") // => "limber_record"
 * </code>
 */
function str_underscore($string)
{
	$string = preg_replace("/([A-Z]+)([A-Z][a-z])/", '$1_$2', $string);
	$string = preg_replace("/([a-z\d])([A-Z])/", '$1_$2', $string);
	
	return strtolower($string);
}

/**
 * Check if a string is empty
 *
 * An string is considered empty if there is no content or the content contains
 * only spaces and tabs
 *
 * @param string $string the input string
 * @return boolean
 */
function str_is_empty($string)
{
	return !trim($string);
}
