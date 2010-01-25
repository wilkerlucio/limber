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

/**
 * This script setups the database to execute the mysql adapter tests
 */

//setup connection
$db = mysql_connect("localhost", "root", "");
mysql_query("DROP DATABASE IF EXISTS `limber_record`", $db);
mysql_query("CREATE DATABASE `limber_record`", $db);
mysql_select_db("limber_record", $db);

//create tables and data
mysql_query("CREATE TABLE `cars` (
	id int(11) not null auto_increment,
	name varchar(255),
	year varchar(4),
	color varchar(60),
	primary key(id)
) type=innodb");

mysql_query("INSERT INTO `cars` (id, name, year, color) values (1, 'Ferrari', '2009', 'black')");
mysql_query("INSERT INTO `cars` (id, name, year, color) values (2, 'Lamborguini', '2009', 'yellow')");
mysql_query("INSERT INTO `cars` (id, name, year, color) values (3, 'BMW', '2008', 'gray')");

//closes connection
mysql_close($db);
