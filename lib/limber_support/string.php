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

/**
 * Returns the plural form of the word in the string
 *
 * examples:
 *
 * <code>
 * str_pluralize("post");         // => "posts"
 * str_pluralize("octopus");      // => "octopi"
 * str_pluralize("sheep");        // => "sheep"
 * str_pluralize("words");        // => "words"
 * str_pluralize("CamelOctopus"); // => "CamelOctopi"
 * </code>
 *
 * @param string $word word to pluralize
 * @return string the pluralized version of word
 */
function str_pluralize($string)
{
	return Inflections::pluralize($string);
}

/**
 * The reverse of pluralize, returns the singular form of a word in a string.
 *
 * examples:
 *
 * <code>
 * str_singularize("posts")       // => "post"
 * str_singularize("octopi")      // => "octopus"
 * str_singularize("sheep")       // => "sheep"
 * str_singularize("word")        // => "word"
 * str_singularize("CamelOctopi") // => "CamelOctopus"
 * </code>
 */
function str_singularize($string)
{
	return Inflections::singularize($string);
}

/**
 * Get the table name for model
 *
 * @param string $class_name The name of class
 * @return string tableized string
 */
function str_tableize($class_name)
{
	return str_pluralize(str_underscore($class_name));
}
