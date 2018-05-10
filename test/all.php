<?php

/*****************************************************************************\
	This is the testing framework for PUDL. This testing framework is
	designed to be launched exclusively form within an Altaform based
	application. The instructions on how to set this up will be provided
	at a later time. This file, however, still gives some clear examples
	as to the types of SQL statements that can be generated through the
	PUDL library.

	IMPORTANT NOTE: The ->string() part of these queries means that they
	will *NOT* be executed, but instead ONLY return an object containing
	the SQL query statement generated. Removing ->string() from each line
	will allow execution of the generated statement. This is simply added
	here to compare the generated statements to their expected results, to
	ensure that all queries are generated by PUDL correctly.

	To execute this test from the command line, simply run the following:

	php _pudl/test/cli.php

	For HHVM users, simply run the following instead:

	hhvm _pudl/test/cli.php
\*****************************************************************************/


function pudlTest($expected) {
	global $db;
	if (is_string($expected)	&&	$expected === $db->query()) return;
	if (is_bool($expected)		&&	$expected) return;
	$trace = debug_backtrace()[0];
	echo "\n\n";
	echo "ERROR: FAILED!!\n\n";
	echo "PHP:\t" . PHP_VERSION . "\n";
	echo "FILE:\t$trace[file]\n";
	echo "LINE:\t$trace[line]\n\n";
	echo "EXPECTED:\n";
	echo "'" . (is_bool($expected) ? 'TRUE' : $expected) . "'\n\n";
	echo "QUERY:\n";
	echo "'" . $db->query() . "'\n\n";
	exit(1);
}


function pudlError($exception, $expected) {
	if ($exception->getMessage() === $expected) return;
	$trace = debug_backtrace()[0];
	echo "\n\n";
	echo "ERROR: FAILED!!\n\n";
	echo "PHP:\t" . PHP_VERSION . "\n";
	echo "FILE:\t$trace[file]\n";
	echo "LINE:\t$trace[line]\n\n";
	echo "EXPECTED:\n";
	echo "'" . $expected . "'\n\n";
	echo "ERROR:\n";
	echo "'" . $exception->getMessage() . "'\n\n";
	echo "\n\n";
	exit(1);
}


function pudlUnit($result, $expected=true) {
	if ($result === $expected) return;
	$trace = debug_backtrace()[0];
	echo "\n\n";
	echo "ERROR: FAILED!!\n\n";
	echo "PHP:\t" . PHP_VERSION . "\n";
	echo "FILE:\t$trace[file]\n";
	echo "LINE:\t$trace[line]\n\n";
	echo "EXPECTED:\n";
	var_dump($expected);
	echo "\n\n";
	echo "RESULT:\n";
	var_dump($result);
	exit(1);
}


//PHP 5.x COMPATIBILITY
if (!defined('PHP_INT_MIN'))		define('PHP_INT_MIN',		~PHP_INT_MAX);
if (!defined('PHP_FLOAT_MIN'))		define('PHP_FLOAT_MIN',		2.2250738585072E-308);
if (!defined('PHP_FLOAT_MAX'))		define('PHP_FLOAT_MAX',		1.7976931348623E+308);
if (!defined('PHP_FLOAT_EPSILON'))	define('PHP_FLOAT_EPSILON',	2.2204460492503E-16);




//BASIC QUERIES, NOT USING THE CUSTOM GENERATOR
require __DIR__ . '/basic.php';

//RETURNED COLUMNS
require __DIR__ . '/column.php';

//FROM TABLES
require __DIR__ . '/table.php';

//JOIN TABLES
require __DIR__ . '/join.php';

//WHERE CLAUSES
require __DIR__ . '/clause.php';

//ORDER BY
require __DIR__ . '/order.php';

//SET OF DATA
require __DIR__ . '/inset.php';

//SELEX - ALL OF THE ABOVE AT ONCE
require __DIR__ . '/selex.php';

//INSERT STATEMENTS
require __DIR__ . '/insert.php';

//UPDATE STATEMENTS
require __DIR__ . '/update.php';

//SHORTHAND NOTATION FOR SELECT STATEMENTS
//CUSTOM COMPLEX SELECTS
require __DIR__ . '/select.php';

//SHORTHAND NOTATION FOR SELECT STATEMENTS
//RETURN A SINGLE CELL
require __DIR__ . '/cell.php';

//SHORTHAND NOTATION FOR SELECT STATEMENTS
//RETURN A SINGLE ROW OR ROWS
require __DIR__ . '/row.php';

//SHORTHAND NOTATION FOR SELECT STATEMENTS
//TRANSLATE TWO COLUMNS INTO A KEY/VALUE PAIR IN AN ARRAY
require __DIR__ . '/collection.php';

//SHORTHAND NOTATION FOR UPDATE STATEMENTS
//INCREMENT A SINGLE COLUMN'S VALUE
require __DIR__ . '/increment.php';

//SUBQUERIES
require __DIR__ . '/subquery.php';

//CUSTOM FUNCTIONS
require __DIR__ . '/function.php';

//MYSQL GLOBALS, VARIABLES, STATUS
require __DIR__ . '/variables.php';

//LOCK TABLES
require __DIR__ . '/lock.php';

//DYNAMIC COLUMNS
require __DIR__ . '/dynamic.php';

//ERROR HANDLING
require __DIR__ . '/errors.php';

//QUERY LOGGING
require __DIR__ . '/log.php';

//PUDL OBJECT
require __DIR__ . '/object.php';

//DELETE QUERIES
require __DIR__ . '/delete.php';

//VIRTUAL PUDL INTERFACE
require __DIR__ . '/clone.php';

//JSON FUNCTIONS - https://mariadb.com/kb/en/library/json-functions/
require __DIR__ . '/json.php';
