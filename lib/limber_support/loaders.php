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

require_once dirname(__FILE__) . "/string.php";

/**
 * @package Support
 * @subpackage loaders
 */

/**
 * Require a complete directory
 *
 * This method scans one directory and require all php files (with require_once)
 * found
 *
 * @param string $path base path to scan
 * @param boolean $recursive if true, the scan will be recursive (following directories)
 * @return void
 */
function require_dir($path, $recursive = true)
{
	$path = rtrim($path, "\\/");
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

/**
 * Generate one autoloader for a given path
 *
 * This method generates one autoloader function, this function will try to
 * autoload a class based one her name, where the default package will be
 * the main directory, and subpackages will be separated by a underscore (_).
 *
 * Example: given the following directory tree (the parentesis represents the class name)
 *
 * my_lib
 * |-- class1.php (Class1)
 * |-- package
 *     |-- class2.php (Package\Class2)
 *     |-- more_package
 *         |-- class_compose3.php (Package\MorePackage\ClassCompose3)
 *     |-- class4.php (Package\Class4)
 *
 * According to exemple above, you can do something like this:
 *
 * <code>
 * path_autoloader("my_lib");
 *
 * //all classes above will be autoloaded
 * $c1 = new Class1();
 * $c2 = new Package\Class2();
 * $c3 = new Package\MorePackage\Class3();
 * $c4 = new Package\Class4();
 * </code>
 *
 * @param string $path base path of classes
 * @param boolean $autoregister if true, the function will be automatic added to auto_register queue
 * @return function generated autoloader function
 */
function path_autoloader($path, $autoregister = true)
{
	$path = rtrim($path, "\\/");
	
	$fn = function($classname) use ($path) {
		$classpath = explode('\\', $classname);
		$classpath = array_map("str_underscore", $classpath);
		$classpath = implode("/", $classpath);
		
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

/**
 * Add a new path to include_path
 *
 * This method simple add a new path to include path, this method also checks
 * if the path isn't already included at include path
 *
 * @param string $path the path to be added to include path
 * @return boolean true if the path is added
 */
function add_include_path($path)
{
	$include_path  = ini_get("include_path");
	$paths = explode(PATH_SEPARATOR, $include_path);
	
	if (in_array($path, $paths)) {
		return false;
	}
	
	$include_path .= PATH_SEPARATOR . $path;
	
	ini_set("include_path", $include_path);
	
	return true;
}
