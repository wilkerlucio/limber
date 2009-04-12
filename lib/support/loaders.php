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
 * @package Support
 * @subpackage loaders
 */

/**
 * Require a complete directory
 */
function require_dir($path, $recursive = true)
{
	$path = rtrim($path, "/");
	$dir = opendir($path);
	
	while ($file = readdir($dir)) {
		if ($file == '.' || $file == '..') continue;
		
		$file_path = $path . '/' . $file;
		
		if (is_file($file_path) && pathinfo($file_path, PATHINFO_EXTENSION) == "php") {
			require_once $file_path;
		} elseif ($recursive && is_dir($file_path)) {
			require_dir($file_path);
		}
	}
}

function path_autoloader($path, $autoregister = true)
{
	$path = rtrim($path, "/");
	
	$fn = function($classname) use ($path) {
		$classpath = str_replace("_", "/", $classname);
		
		$fullpath = $path . '/' . $classpath . '.php';
		
		if (file_exists($fullpath)) {
			require_once $fullpath;
			
			return class_exists($classname);
		}
		
		return false;
	};
	
	if ($autoregister) spl_autoload_register($fn);
	
	return $fn;
}
