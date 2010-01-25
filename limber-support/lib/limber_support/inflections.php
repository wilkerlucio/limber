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

class Inflections
{
	private static $plurals      = array();
	private static $singulars    = array();
	private static $uncountables = array();
	
	private function __construct() {}
	
	public static function plural($rule, $replacement)
	{
		array_unshift(self::$plurals, array($rule, $replacement));
	}
	
	public static function singular($rule, $replacement)
	{
		array_unshift(self::$singulars, array($rule, $replacement));
	}
	
	public static function irregular($singular, $plural)
	{
		if (strtoupper($singular[0]) == strtoupper($plural[0])) {
			self::plural("/({$singular[0]})" . substr($singular, 1) . "$/i", '\1' . substr($plural, 1));
			self::singular("/({$plural[0]})" . substr($plural, 1) . "$/i", '\1' . substr($singular, 1));
		} else {
			self::plural("/" . strtoupper($singular[0]) . "(?i)" . substr($singular, 1) . "$/i", strtoupper($plural[0]) . substr($plural, 1));
			self::plural("/" . strtolower($singular[0]) . "(?i)" . substr($singular, 1) . "$/i", strtolower($plural[0]) . substr($plural, 1));
			self::singular("/" . strtoupper($plural[0]) . "(?i)" . substr($plural, 1) . "$/i", strtoupper($singular[0]) . substr($singular, 1));
			self::singular("/" . strtolower($plural[0]) . "(?i)" . substr($plural, 1) . "$/i", strtolower($singular[0]) . substr($singular, 1));
		}
	}
	
	public static function uncountable()
	{
		$uncountables = self::$uncountables;
		$uncountables[] = func_get_args();
		
		self::$uncountables = array_flatten($uncountables);
	}
	
	public static function pluralize($word)
	{
		if (str_is_empty($word) || in_array($word, self::$uncountables)) {
			return $word;
		} else {
			foreach (self::$plurals as $plural) {
				list($rule, $replacement) = $plural;
				
				if (preg_match($rule, $word)) return preg_replace($rule, $replacement, $word);
			}
			
			return $word;
		}
	}
	
	public static function singularize($word)
	{
		if (str_is_empty($word) || in_array($word, self::$uncountables)) {
			return $word;
		} else {
			foreach (self::$singulars as $singular) {
				list($rule, $replacement) = $singular;
				
				if (preg_match($rule, $word)) return preg_replace($rule, $replacement, $word);
			}
			
			return $word;
		}
	}
}

Inflections::plural("/$/", 's');
Inflections::plural("/s$/i", 's');
Inflections::plural("/(ax|test)is$/i", '\1es');
Inflections::plural("/(octop|vir)us$/i", '\1i');
Inflections::plural("/(alias|status)$/i", '\1es');
Inflections::plural("/(bu)s$/i", '\1ses');
Inflections::plural("/(buffal|tomat)o$/i", '\1oes');
Inflections::plural("/([ti])um$/i", '\1a');
Inflections::plural("/sis$/i", 'ses');
Inflections::plural("/(?:([^f])fe|([lr])f)$/i", '\1\2ves');
Inflections::plural("/(hive)$/i", '\1s');
Inflections::plural("/([^aeiouy]|qu)y$/i", '\1ies');
Inflections::plural("/(x|ch|ss|sh)$/i", '\1es');
Inflections::plural("/(matr|vert|ind)(?:ix|ex)$/i", '\1ices');
Inflections::plural("/([m|l])ouse$/i", '\1ice');
Inflections::plural("/^(ox)$/i", '\1en');
Inflections::plural("/(quiz)$/i", '\1zes');

Inflections::singular("/s$/i", '');
Inflections::singular("/(n)ews$/i", '\1ews');
Inflections::singular("/([ti])a$/i", '\1um');
Inflections::singular("/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i", '\1\2sis');
Inflections::singular("/(^analy)ses$/i", '\1sis');
Inflections::singular("/([^f])ves$/i", '\1fe');
Inflections::singular("/(hive)s$/i", '\1');
Inflections::singular("/(tive)s$/i", '\1');
Inflections::singular("/([lr])ves$/i", '\1f');
Inflections::singular("/([^aeiouy]|qu)ies$/i", '\1y');
Inflections::singular("/(s)eries$/i", '\1eries');
Inflections::singular("/(m)ovies$/i", '\1ovie');
Inflections::singular("/(x|ch|ss|sh)es$/i", '\1');
Inflections::singular("/([m|l])ice$/i", '\1ouse');
Inflections::singular("/(bus)es$/i", '\1');
Inflections::singular("/(o)es$/i", '\1');
Inflections::singular("/(shoe)s$/i", '\1');
Inflections::singular("/(cris|ax|test)es$/i", '\1is');
Inflections::singular("/(octop|vir)i$/i", '\1us');
Inflections::singular("/(alias|status)es$/i", '\1');
Inflections::singular("/^(ox)en/i", '\1');
Inflections::singular("/(vert|ind)ices$/i", '\1ex');
Inflections::singular("/(matr)ices$/i", '\1ix');
Inflections::singular("/(quiz)zes$/i", '\1');

Inflections::irregular('person', 'people');
Inflections::irregular('man', 'men');
Inflections::irregular('child', 'children');
Inflections::irregular('sex', 'sexes');
Inflections::irregular('move', 'moves');
Inflections::irregular('cow', 'kine');

Inflections::uncountable(array("equipment", "information", "rice", "money", "species", "series", "fish", "sheep"));
